<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketStoreRequest;
use dnj\Ticket\Http\Requests\TicketUpdateRequest;
use dnj\Ticket\Http\Resources\TicketResource;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketController extends Controller
{
    use WorksWithAttachments;

    public function __construct(protected ILogger $userLogger, private ITicketManager $ticket)
    {
    }

    public function index(Request $request)
    {
        $tickets = $this->ticket->list($request->all());

        return new TicketResource($tickets);
    }

    public function store(TicketStoreRequest $request)
    {
        $ticket = $this->ticket->store($request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket['model'])
            ->withProperties($ticket['diff'])
            ->log('created');

        $ticket = $ticket['model'];
        $message = new TicketMessage();
        $message->fill([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->user()->id,
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

    public function show(int $id)
    {
        $ticket = $this->ticket->find($id);

        return new TicketResource($ticket);
    }

    public function update(int $id, TicketUpdateRequest $request)
    {
        $ticket = $this->ticket->update($id, $request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket['model'])
            ->withProperties($ticket['diff'])
            ->log('updated');

        return new TicketResource($ticket['model']);
    }

    public function destroy(int $id, Request $request)
    {
        $ticket = $this->ticket->destroy($id);

        $this->userLogger
            ->withRequest($request)
            ->performedOn($ticket['model'])
            ->withProperties($ticket['diff'])
            ->log('deleted');

        return response()->noContent();
    }
}
