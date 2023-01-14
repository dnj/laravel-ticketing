<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketStoreRequest;
use dnj\Ticket\Http\Requests\TicketUpdateRequest;
use dnj\Ticket\Http\Resources\TicketResource;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketController extends Controller
{
    use WorksWithAttachments;

    public function __construct(protected ILogger $userLogger)
    {
    }

    public function index(Request $request)
    {
        $q = Ticket::query();
        $q->orderBy('updated_at', 'desc');
        $q->when($request->input('title'), function ($q, $title) {
            return $q->where('title', 'like', '%'.$title.'%');
        })
            ->when($request->input('client_id'), function ($q, $client) {
                return $q->where('client_id', $client);
            })
            ->when($request->input('department_id'), function ($q, $department) {
                return $q->whereIn('department_id', $department);
            })
            ->when($request->input('status'), function ($q, $status) {
                return $q->whereIn('status', $status);
            })
            ->when($request->input('created_start_date'), function ($q, $created_start_date) use ($request) {
                $created_end_date = $request->input('created_end_date', now());

                return $q->whereBetween('created_at', [$created_start_date, $created_end_date]);
            })
            ->when($request->input('updated_start_date'), function ($q, $updated_start_date) use ($request) {
                $updated_end_date = $request->input('updated_end_date', now());

                return $q->whereBetween('updated_at', [$updated_start_date, $updated_end_date]);
            });

        return new TicketResource($q->cursorPaginate());
    }

    public function store(TicketStoreRequest $request)
    {
        $me = auth()->user()->id;
        $ticket = new Ticket();
        $ticket->client_id = $me;
        $ticket->fill($request->validated());
        $ticket->status = $ticket->client_id == $me ? TicketStatus::UNREAD : TicketStatus::ANSWERED;
        $changes = $ticket->changesForLog();
        $ticket->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket)
            ->withProperties($changes)
            ->log('created');

        $message = new TicketMessage();
        $message->fill([
            'ticket_id' => $ticket->id,
            'user_id' => $me,
            'message' => $request->message,
        ]);
        $changes = $message->changesForLog();
        $message->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message)
            ->withProperties($changes)
            ->log('created');

        $this->saveAttachments($request, $message->id);

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    public function update(Ticket $ticket, TicketUpdateRequest $request)
    {
        $ticket->fill($request->validated());
        $changes = $ticket->changesForLog();
        $ticket->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket)
            ->withProperties($changes)
            ->log('updated');

        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket, Request $request)
    {
        $changes = $ticket->toArray();

        $ticket->delete();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket)
            ->withProperties($changes)
            ->log('deleted');

        return response()->noContent();
    }
}
