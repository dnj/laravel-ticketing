<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Http\Requests\TicketMessageUpsertRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketMessageController extends Controller
{
    public function index(Ticket $ticket, Request $request)
    {
        $ticketMessages = TicketMessage::where('ticket_id', $ticket->id)
            ->orderBy('created_a', $request->input('orderBy', 'asc'))
            ->cursorPaginate(10);
        $ticket->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
        return $ticketMessages;
    }

    public function store(Ticket $ticket, TicketMessageUpsertRequest $request)
    {
        $message = $ticket->messages()->create($request->validated());
        $ticket->changeTicketStatus();

        return new TicketMessageResource($message);
    }

    public function update(Ticket $ticket, TicketMessage $message, TicketMessageUpsertRequest $request)
    {
        $message->fill($request->validated());
        $message->save();

        return new TicketMessageResource($message);
    }

    public function destroy(Ticket $ticket, TicketMessage $message)
    {
        $message->delete();

        return response()->noContent();
    }
}
