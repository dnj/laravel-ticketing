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

    public function __construct(protected ILogger $userLogger, private TicketMessage $message, private ITicketManager $ticket)
    {
        $this->setSaveLogs(true);
    }

    public function search(int $ticketId, ?array $filters): iterable
    {
        $q = $this->message->query();
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
        return $this->message->findOrFail($id);
    }

    public function update(int $id, array $changes): TicketMessage
    {
        $message = $this->find($id);
        $message->fill($changes);

        if (isset($changes['attachments'])) {
            $this->saveAttachments($changes['attachments'], $id);
        }

        $changes = $message->changesForLog();

        $this->saveLog(model: $message, changes: $changes, log: 'updated');

        $message->save();

        return $message;
    }

    public function store(int $ticketId, string $message, array $files = [], ?int $userId = null): TicketMessage
    {
        $userId ??= auth()->user()->id;

        $this->message->fill([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message' => $message,
        ]);

        $changes = $this->message->changesForLog();

        $this->saveLog(model: $this->message, changes: $changes, log: 'created');

        $this->message->save();

        $ticket = $this->ticket->find($ticketId);
        $this->ticket->update($ticketId, [
            'status' => $this->ticket->ticketStatus($ticket->getClientID()),
        ]);

        $this->saveAttachments($files, $this->message->getID());

        return $this->message;
    }

    public function destroy(int $id): void
    {
        $message = $this->find($id);
        $changes = $message->toArray();

        $this->saveLog(model: $message, changes: $changes, log: 'deleted');

        $message->delete();
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
