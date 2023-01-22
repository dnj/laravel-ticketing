<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Contracts\ITicketMessageManager;
use dnj\Ticket\Models\TicketMessage;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

class TicketMessageManager implements ITicketMessageManager
{
    public function __construct(private TicketMessage $message, private ITicketManager $ticket)
    {
    }

    public function list(int $ticket_id, string $sort): CursorPaginator
    {
        $ticketMessages = $this->message->query()->where('ticket_id', $ticket_id)
            ->orderBy('created_at', $sort)
            ->cursorPaginate();

        $this->ticket->updateSeenAt($ticket_id);

        return $ticketMessages;
    }

    public function find(int $id): Model
    {
        return $this->message->findOrFail($id);
    }

    public function update(int $id, array $data): array
    {
        $message = $this->find($id);
        $message->fill($data);
        $changes = $message->changesForLog();
        $message->save();

        return ['model' => $message, 'diff' => $changes];
    }

    public function store(int $ticket_id, array $data): array
    {
        $me = auth()->user()->id;

        $ticket = $this->ticket->find($ticket_id);

        $this->message->fill([
            'ticket_id' => $ticket->id,
            'user_id' => $me,
            'message' => $data['message'],
        ]);

        $changes = $this->message->changesForLog();
        $this->message->save();

        $ticket->status = $this->ticket->ticketStatus($ticket);
        $ticket->save();

        return ['model' => $this->message, 'diff' => $changes];
    }

    public function destroy(int $id): array
    {
        $message = $this->find($id);
        $changes = $message->toArray();
        $message->delete();

        return ['model' => $message, 'diff' => $changes];
    }
}
