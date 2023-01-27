<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Http\Requests\TicketStoreRequest;
use dnj\Ticket\Http\Requests\TicketUpdateRequest;
use dnj\Ticket\Http\Resources\TicketMessageResource;
use dnj\Ticket\Http\Resources\TicketResource;
use dnj\Ticket\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketController extends Controller
{
    public function __construct(protected ITicketManager $ticketManager)
    {
    }

    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->orderBy('updated_at', 'desc')
            ->filter($request->all())
            ->cursorPaginate();

        return new TicketResource($tickets);
    }

    public function store(TicketStoreRequest $request)
    {
        $me = auth()->user()->id;
        $message = $this->ticketManager->store(
            $request->input('client_id', $me),
            $request->input('department_id'),
            $request->input('message'),
            $request->input('attachments') ?? [],
            $request->input('title', null),
            $me,
            $request->input('status', null),
            true
        );

        return TicketMessageResource::make($message)->load('ticket');
    }

    public function show(int $id)
    {
        $ticket = $this->ticketManager->find($id);

        return new TicketResource($ticket);
    }

    public function update(int $id, TicketUpdateRequest $request)
    {
        $ticket = $this->ticketManager->update($id, $request->validated(), true);

        return new TicketResource($ticket);
    }

    public function destroy(int $id)
    {
        $this->ticketManager->destroy($id, true);

        return response()->noContent();
    }
}
