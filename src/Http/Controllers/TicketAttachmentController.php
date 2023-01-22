<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Filesystem\Tmp\File;
use dnj\Ticket\Contracts\ITicketAttachmentManager;
use dnj\Ticket\Http\Controllers\Concerns\WorksWithAttachments;
use dnj\Ticket\Http\Requests\TicketAttachmentRequest;
use dnj\Ticket\Http\Resources\TicketAttachmentResource;
use Illuminate\Routing\Controller;

class TicketAttachmentController extends Controller
{
    use WorksWithAttachments;

    public function __construct(private ITicketAttachmentManager $attachment)
    {
    }

    public function store(TicketAttachmentRequest $request)
    {
        $attachments = $this->saveAttachments($request);

        return new TicketAttachmentResource($attachments);
    }

    public function show(int $id)
    {
        $ticketAttachment = $this->attachment->find($id);
        $localFile = File::insureLocal($ticketAttachment->file);

        return response()->download($localFile->getPath());
    }

    public function destroy(int $ticketAttachment_id)
    {
        $this->attachment->destroy($ticketAttachment_id);

        return response()->noContent();
    }
}
