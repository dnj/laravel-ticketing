<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Http\Requests\TicketMessageUpsertRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\Ticket\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketMessageController extends Controller
{
    public function __construct(
        protected ITicketManager $ticketManager,
        protected IMessageManager $messageManager
    ) {
    }

    public function index(int $ticketId, Request $request)
    {
        $ticket = $this->ticketManager->find($ticketId);

        if ($ticket->getClientID() == auth()->user()->id) {
            $this->ticketManager->markAsSeenByClient($ticketId);
        } else {
            $this->ticketManager->markAsSeenBySupport($ticketId);
        }

        $messages = TicketMessage::query()
            ->orderBy('updated_at', 'desc')
            ->where('ticket_id', $ticketId)
            ->filter($request->all())
            ->cursorPaginate();

        return new TicketMessageResource($messages);
    }

    public function store(int $ticketId, TicketMessageUpsertRequest $request)
    {
        $message = $this->messageManager->store(
            $ticketId,
            $request->input('message'),
            $request->input('attachments') ?? [],
            true
        );

        return new TicketMessageResource($message);
    }

    public function update(int $ticketId, int $messageId, TicketMessageUpsertRequest $request)
    {
        $message = $this->messageManager->update($messageId, $request->validated(), true);

        return new TicketMessageResource($message);
    }

    public function destroy(int $ticketId, int $messageId)
    {
        $this->messageManager->destroy($messageId, true);

        return response()->noContent();
    }
}
