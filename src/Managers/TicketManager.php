<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Managers\Concerns\WorksWithAttachments;
use dnj\Ticket\Managers\Concerns\WorksWithLog;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;

class TicketManager implements ITicketManager
{
    use WorksWithAttachments;
    use WorksWithLog;

    private bool $enableLog;

    public function __construct(protected ILogger $userLogger, private Ticket $model)
    {
        $this->setSaveLogs(true);
    }

    public function search(?array $filters): iterable
    {
        $q = $this->model->query();
        $q->orderBy('updated_at', 'desc');
        $q->when(isset($filters['title']), function ($q) use ($filters) {
            return $q->where('title', 'like', '%'.$filters['title'].'%');
        })
            ->when(isset($filters['client_id']), function ($q) use ($filters) {
                return $q->where('client_id', $filters['client_id']);
            })
            ->when(isset($filters['department_id']), function ($q) use ($filters) {
                return $q->whereIn('department_id', $filters['department_id']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                return $q->whereIn('status', $filters['status']);
            })
            ->when(isset($filters['created_start_date']), function ($q) use ($filters) {
                $created_end_date = isset($filters['created_end_date']) ? $filters['created_end_date'] : now();

                return $q->whereBetween('created_at', [$filters['created_start_date'], $created_end_date]);
            })
            ->when(isset($filters['updated_start_date']), function ($q) use ($filters) {
                $updated_end_date = isset($filters['updated_end_date']) ? $filters['updated_end_date'] : now();

                return $q->whereBetween('updated_at', [$filters['updated_start_date'], $updated_end_date]);
            });

        return $q->cursorPaginate();
    }

    public function find(int $id): Ticket
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $changes): Ticket
    {
        $this->model = $this->find($id);
        $this->model->fill($changes);

        $this->saveLog(log: 'updated');

        $this->model->save();

        return $this->model;
    }

    public function store(int $clientId, int $departmentId, string $message, array $files = [], ?string $title = null, ?int $userId = null, ?TicketStatus $status = null): TicketMessage
    {
        $this->model->fill([
            'title' => $title,
            'client_id' => $clientId,
            'department_id' => $departmentId,
            'status' => $status ?? $this->ticketStatus($clientId),
        ]);

        $this->saveLog(log: 'created');

        $this->model->save();

        $ticketMessage = app()->make(IMessageManager::class);

        $ticketMessage = $ticketMessage->store(
            ticketId: $this->model->getID(),
            message: $message,
            files: $files,
            userId: $userId,
        );

        return $ticketMessage;
    }

    public function destroy(int $id): void
    {
        $this->model = $this->find($id);

        $this->saveLog(log: 'deleted');

        $this->model->delete();
    }

    public function updateSeenAt(int $ticket_id): void
    {
        $this->model = $this->find($ticket_id);

        if (auth()->user()->id == $this->model->getClientID()) {
            $this->model->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
        }
    }

    public function ticketStatus(int $client_id): TicketStatus
    {
        return $client_id == auth()->user()->id ? TicketStatus::UNREAD : TicketStatus::ANSWERED;
    }

    public function setSaveLogs(bool $save): void
    {
        $this->enableLog = $save;
    }

    public function getSaveLogs(): bool
    {
        return $this->enableLog;
    }
}
