<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\ITicketMessageManager;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketMessageUpsertRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketMessageController extends Controller
{
    use WorksWithAttachments;

    public function __construct(protected ILogger $userLogger, private ITicketMessageManager $message)
    {
    }

    public function index(int $ticket_id, Request $request)
    {
        $ticketMessages = $this->message->list($ticket_id, $request->input('orderBy', 'asc'));

        return new TicketMessageResource($ticketMessages);
    }

    public function store(int $ticket_id, TicketMessageUpsertRequest $request)
    {
        $message = $this->message->store($ticket_id, $request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message['model'])
            ->withProperties($message['diff'])
            ->log('created');

        $this->saveAttachments($request, $message['model']->id);

        return new TicketMessageResource($message['model']);
    }

    public function update(int $ticket_id, int $message_id, TicketMessageUpsertRequest $request)
    {
        $message = $this->message->update($message_id, $request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message['model'])
            ->withProperties($message['diff'])
            ->log('updated');

        $this->saveAttachments($request, $message['model']->id);

        return new TicketMessageResource($message['model']);
    }

    public function destroy(int $ticket_id, int $message_id, Request $request)
    {
        $message = $this->message->destroy($message_id);

        $this->userLogger
            ->withRequest($request)
            ->performedOn($message['model'])
            ->withProperties($message['diff'])
            ->log('deleted');

        return response()->noContent();
    }
}
