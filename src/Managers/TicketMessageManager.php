<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Managers\Concerns\WorksWithAttachments;
use dnj\Ticket\Managers\Concerns\WorksWithLog;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;

class TicketMessageManager implements IMessageManager
{
    use WorksWithAttachments;
    use WorksWithLog;

    private bool $enableLog;

    public function __construct(protected ILogger $userLogger, private TicketMessage $model, private ITicketManager $ticket)
    {
        $this->setSaveLogs(true);
    }

    public function search(int $ticketId, ?array $filters): iterable
    {
        $q = $this->model->query();
        $q->when(isset($filters['title']), function ($q) use ($filters) {
            return $q->where('title', 'like', '%'.$filters['title'].'%');
        })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                return $q->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['created_start_date']), function ($q) use ($filters) {
                $created_end_date = isset($filters['created_end_date']) ? $filters['created_end_date'] : now();

                return $q->whereBetween('created_at', [$filters['created_start_date'], $created_end_date]);
            })
            ->when(isset($filters['updated_start_date']), function ($q) use ($filters) {
                $updated_end_date = isset($filters['updated_end_date']) ? $filters['updated_end_date'] : now();

                return $q->whereBetween('updated_at', [$filters['updated_start_date'], $updated_end_date]);
            })
            ->when(isset($filters['sort']), function ($q) use ($filters) {
                return $q->orderBy('updated_at', $filters['sort']);
            });

        $this->ticket->updateSeenAt($ticketId);

        return $q->cursorPaginate();
    }

    public function find(int $id): TicketMessage
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $changes): TicketMessage
    {
        $this->model = $this->find($id);
        $this->model->fill($changes);

        if (isset($changes['attachments'])) {
            $this->saveAttachments($changes['attachments'], $id);
        }

        $this->saveLog(log: 'updated');

        $this->model->save();

        return $this->model;
    }

    public function store(int $ticketId, string $message, array $files = [], ?int $userId = null): TicketMessage
    {
        $userId ??= auth()->user()->id;

        $this->model->fill([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message' => $message,
        ]);

        $this->saveLog(log: 'created');

        $this->model->save();

        $ticket = $this->ticket->find($ticketId);
        $this->ticket->update($ticketId, [
            'status' => $this->ticket->ticketStatus($ticket->getClientID()),
        ]);

        $this->saveAttachments($files, $this->model->getID());

        return $this->model;
    }

    public function destroy(int $id): void
    {
        $this->model = $this->find($id);

        $this->saveLog(log: 'deleted');

        $this->model->delete();
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
