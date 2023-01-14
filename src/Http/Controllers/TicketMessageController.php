<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketMessageUpsertRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketMessageController extends Controller
{
    use WorksWithAttachments;

    public function __construct(protected ILogger $userLogger)
    {
    }

    public function index(Ticket $ticket, Request $request)
    {
        $ticketMessages = TicketMessage::query()
            ->where('ticket_id', $ticket->id)
            ->orderBy('created_a', $request->input('orderBy', 'asc'))
            ->cursorPaginate();

        if (auth()->user()->id == $ticket->client_id) {
            $ticket->messages()->whereNull('seen_at')->update(['seen_at' => now()]);
        }

        return new TicketMessageResource($ticketMessages);
    }

    public function store(Ticket $ticket, TicketMessageUpsertRequest $request)
    {
        $me = auth()->user()->id;

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

        $ticket->status = $ticket->client_id == $me ? TicketStatus::UNREAD : TicketStatus::ANSWERED;
        $ticket->save();

        $this->saveAttachments($request, $message->id);

        return new TicketMessageResource($message);
    }

    public function update(Ticket $ticket, TicketMessage $message, TicketMessageUpsertRequest $request)
    {
        $message->fill($request->validated());
        $changes = $message->changesForLog();
        $message->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message)
            ->withProperties($changes)
            ->log('updated');

        $this->saveAttachments($request, $message->id);

        return new TicketMessageResource($message);
    }

    public function destroy(Ticket $ticket, TicketMessage $message, Request $request)
    {
        $message->delete();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message)
            ->log('deleted');

        return response()->noContent();
    }
}
