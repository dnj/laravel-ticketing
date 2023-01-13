<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\FileHelpers;
use dnj\Ticket\Http\Requests\TicketStoreRequest;
use dnj\Ticket\Http\Requests\TicketUpdateRequest;
use dnj\Ticket\Http\Resources\TicketResource;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TicketController extends Controller
{
    use FileHelpers;

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
        $ticket->save();

        $message = $ticket->messages()->create([
            'user_id' => $me,
            'message' => $request->message,
        ]);

        // if request has file attachment, That will be store and attached to message
        if ($request->hasfile('attachments')) {
            $attachmentList = [];

            foreach ($request->file('attachments') as $attachment) {
                $file = $this->saveFile($attachment);

                $attach = TicketAttachment::create([
                    'name' => $attachment->getClientOriginalName(),
                    'message_id' => $message->id,
                    'file' => $file,
                    'mime' => $attachment->getClientMimeType(),
                    'size' => $attachment->getSize(),
                ]);

                array_push($attachmentList, $attach);
            }
        } elseif ($request->has('attachments') && is_array($request->input('attachments'))) {
            TicketAttachment::whereIn('id', $request->input('attachments'))->update(['message_id' => $message->id]);
        }

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket)
    {
        return new TicketResource($ticket);
    }

    public function update(Ticket $ticket, TicketUpdateRequest $request)
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
}
