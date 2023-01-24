<?php

namespace dnj\Ticket\Managers;

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

    private bool $enableLog = true;

    public function __construct(protected ILogger $userLogger, private Ticket $ticket)
    {
    }

    public function search(?array $filters): iterable
    {
        $q = $this->ticket->query();
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
        return $this->ticket->findOrFail($id);
    }

    public function update(int $id, array $changes): Ticket
    {
        $ticket = $this->find($id);
        $ticket->fill($changes);
        $changes = $ticket->changesForLog();

        $this->saveLog(model: $ticket, changes: $changes, log: 'updated');

        $ticket->save();

        return $ticket;
    }

    public function store(int $clientId, int $departmentId, string $message, array $files = [], ?string $title = null, ?int $userId = null, ?TicketStatus $status = null): TicketMessage
    {
        $this->ticket->fill([
            'title' => $title,
            'client_id' => $clientId,
            'department_id' => $departmentId,
            'status' => $status ?? $this->ticketStatus($clientId),
        ]);
        $changes = $this->ticket->changesForLog();

        $this->saveLog(model: $this->ticket, changes: $changes, log: 'created');

        $this->ticket->save();

        $ticketMessage = new TicketMessage();
        $ticketMessage->fill([
            'ticket_id' => $this->ticket->id,
            'user_id' => $userId,
            'message' => $message,
        ]);
        $changes = $ticketMessage->changesForLog();

        $this->saveLog(model: $ticketMessage, changes: $changes, log: 'created');

        $ticketMessage->save();

        $this->saveAttachments($files, $ticketMessage->id);

        return $ticketMessage;
    }

    public function destroy(int $id): void
    {
        $ticket = $this->find($id);
        $changes = $ticket->toArray();

        $this->saveLog(model: $ticket, changes: $changes, log: 'deleted');

        $ticket->delete();
    }

    public function updateSeenAt(int $ticket_id): void
    {
        $ticket = $this->find($ticket_id);

        if (auth()->user()->id == $ticket->client_id) {
            $ticket->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
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
