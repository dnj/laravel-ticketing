<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Http\Requests\TicketMessageRequest;
use dnj\Ticket\Http\Requests\TicketUpsertRequest;
use dnj\Ticket\Http\Resources\TicketResource;
use Illuminate\Routing\Controller;
use dnj\Ticket\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()->filter($request)->orderBy('updated_at', 'desc')->cursorPaginate(10);
        return $tickets;
    }

    public function store(TicketUpsertRequest $request)
    {
        $ticket = Ticket::create($request->validated());
        $ticket->messages()->create(['user_id' => auth()->user()->id, 'message' => $request->message]);

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket)
    {
        $ticket->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
        return new TicketResource($ticket);
    }

    public function update(Ticket $ticket, TicketUpsertRequest $request)
    {
        $ticket->fill($request->validated());
        $ticket->save();

        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->noContent();
    }

    public function storeTicketMessage(Ticket $ticket, TicketMessageRequest $request)
    {
        $ticket->messages()->create($request->validated());
        $ticket->changeTicketStatus();

        return new TicketResource($ticket);
    }
}
