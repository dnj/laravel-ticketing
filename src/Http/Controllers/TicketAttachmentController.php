<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Filesystem\Tmp\File;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketAttachmentRequest;
use dnj\Ticket\Http\Resources\TicketAttachmentResource;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Routing\Controller;

class TicketAttachmentController extends Controller
{
    use WorksWithAttachments;

    public function store(TicketAttachmentRequest $request)
    {
        $attachments = $this->saveAttachments($request);

        return new TicketAttachmentResource($attachments);
    }

    public function show(TicketAttachment $ticketAttachment)
    {
        $localFile = File::insureLocal($ticketAttachment->file);

        return response()->download($localFile->getPath());
    }

    public function destroy(TicketAttachment $ticketAttachment)
    {
        if (TicketAttachment::where('file', serialize($ticketAttachment->file))->count() <= 1) {
            $ticketAttachment->file->delete();
        }
        $ticketAttachment->delete();

        return response()->noContent();
    }
}
