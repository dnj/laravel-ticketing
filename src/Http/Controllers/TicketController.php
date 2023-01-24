<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Http\Requests\TicketStoreRequest;
use dnj\Ticket\Http\Requests\TicketUpdateRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\Ticket\Http\Resources\TicketResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketController extends Controller
{
    public function __construct(private ITicketManager $ticket)
    {
    }

    public function index(Request $request)
    {
        $tickets = $this->ticket->search($request->all());

        return new TicketResource($tickets);
    }

    public function store(TicketStoreRequest $request)
    {
        $message = $this->ticket->store(
            $request->input('client_id', auth()->user()->id),
            $request->input('department_id'),
            $request->input('message'),
            $request->attachments ?? [],
            $request->input('title', null),
            auth()->user()->id,
            $request->input('status', null),
        );

        return TicketMessageResource::make($message)->load('ticket');
    }

    public function show(int $id)
    {
        $ticket = $this->ticket->find($id);

        return new TicketResource($ticket);
    }

    public function update(int $id, TicketUpdateRequest $request)
    {
        $ticket = $this->ticket->update($id, $request->validated());

        return new TicketResource($ticket);
    }

    public function destroy(int $id, Request $request)
    {
        $this->ticket->destroy($id);

        return response()->noContent();
    }
}
