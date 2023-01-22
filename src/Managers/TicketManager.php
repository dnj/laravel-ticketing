<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Models\Ticket;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

class TicketManager implements ITicketManager
{
    public function __construct(private Ticket $ticket)
    {
    }

    public function list(?array $params): CursorPaginator
    {
        $q = $this->ticket->query();
        $q->orderBy('updated_at', 'desc');
        $q->when(isset($params['title']), function ($q) use ($params) {
            return $q->where('title', 'like', '%'.$params['title'].'%');
        })
            ->when(isset($params['client_id']), function ($q) use ($params) {
                return $q->where('client_id', $params['client_id']);
            })
            ->when(isset($params['department_id']), function ($q) use ($params) {
                return $q->whereIn('department_id', $params['department_id']);
            })
            ->when(isset($params['status']), function ($q) use ($params) {
                return $q->whereIn('status', $params['status']);
            })
            ->when(isset($params['created_start_date']), function ($q) use ($params) {
                $created_end_date = isset($params['created_end_date']) ? $params['created_end_date'] : now();

                return $q->whereBetween('created_at', [$params['created_start_date'], $created_end_date]);
            })
            ->when(isset($params['updated_start_date']), function ($q) use ($params) {
                $updated_end_date = isset($params['updated_end_date']) ? $params['updated_end_date'] : now();

                return $q->whereBetween('updated_at', [$params['updated_start_date'], $updated_end_date]);
            });

        return $q->cursorPaginate();
    }

    public function find(int $id): Model
    {
        return $this->ticket->findOrFail($id);
    }

    public function update(int $id, array $data): array
    {
        $ticket = $this->find($id);
        $ticket->fill($data);
        $changes = $ticket->changesForLog();
        $ticket->save();

        return ['model' => $ticket, 'diff' => $changes];
    }

    public function store(array $data): array
    {
        $me = auth()->user()->id;
        $this->ticket->client_id = $me;
        $this->ticket->fill($data);
        $this->ticket->status = $this->ticketStatus($this->ticket);
        $changes = $this->ticket->changesForLog();
        $this->ticket->save();

        return ['model' => $this->ticket, 'diff' => $changes];
    }

    public function destroy(int $id): array
    {
        $ticket = $this->find($id);
        $changes = $ticket->toArray();
        $ticket->delete();

        return ['model' => $ticket, 'diff' => $changes];
    }

    public function updateSeenAt(int $ticket_id): void
    {
        $ticket = $this->find($ticket_id);

        if (auth()->user()->id == $ticket->client_id) {
            $ticket->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
        }
    }

    public function ticketStatus(Ticket $ticket): TicketStatus
    {
        return $ticket->client_id == auth()->user()->id ? TicketStatus::UNREAD : TicketStatus::ANSWERED;
    }
}
