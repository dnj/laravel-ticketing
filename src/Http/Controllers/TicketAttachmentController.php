<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Filesystem\Tmp\File;
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Http\Requests\TicketAttachmentRequest;
use dnj\Ticket\Http\Resources\TicketAttachmentResource;
use Illuminate\Routing\Controller;

class TicketAttachmentController extends Controller
{
    public function __construct(protected IAttachmentManager $attachmentManager)
    {
    }

    public function store(TicketAttachmentRequest $request)
    {
        $attachments = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $this->attachmentManager->storeByUpload($file, $request->input('message_id'), true);
            }
        }

        return new TicketAttachmentResource($attachments);
    }

    public function show(int $id)
    {
        $ticketAttachment = $this->attachmentManager->find($id);
        $localFile = File::insureLocal($ticketAttachment->file);

        return response()->download($localFile->getPath());
    }

    public function destroy(int $id)
    {
        $this->attachmentManager->destroy($id, true);

        return response()->noContent();
    }
}
