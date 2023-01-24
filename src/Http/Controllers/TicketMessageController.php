<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Http\Requests\TicketMessageUpsertRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketMessageController extends Controller
{
    private bool $enableLog = true;

    public function __construct(private IMessageManager $message)
    {
    }

    public function index(int $ticket_id, Request $request)
    {
        $ticketMessages = $this->message->search($ticket_id, $request->all());

        return new TicketMessageResource($ticketMessages);
    }

    public function store(int $ticketId, TicketMessageUpsertRequest $request)
    {
        $message = $this->message->store(
            $ticketId,
            $request->input('message'),
            $request->input('attachments'),
            $request->input('user_id', auth()->user()->id),
        );

        return new TicketMessageResource($message);
    }

    public function update(int $ticket_id, int $message_id, TicketMessageUpsertRequest $request)
    {
        $message = $this->message->update($message_id, $request->validated());

        return new TicketMessageResource($message);
    }

    public function destroy(int $ticket_id, int $message_id)
    {
        $this->message->destroy($message_id);

        return response()->noContent();
    }
}
