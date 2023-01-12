<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Filesystem\Tmp\File;
use dnj\Ticket\FileHelpers;
use dnj\Ticket\Http\Requests\TicketAttachmentRequest;
use dnj\Ticket\Http\Resources\TicketAttachmentResource;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Routing\Controller;

class TicketAttachmentController extends Controller
{
    use FileHelpers;

    public function store(TicketAttachmentRequest $request)
    {
        $attachmentList = [];

        if ($request->hasfile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $file = $this->saveFile($attachment, $attachment->extension());

                $attach = TicketAttachment::create([
                    'name' => $attachment->getClientOriginalName(),
                    'file' => $file,
                    'mime' => $attachment->getClientMimeType(),
                    'size' => $attachment->getSize(),
                ]);

                array_push($attachmentList, $attach);
            }
        }

        return new TicketAttachmentResource($attachmentList);
    }

    public function show(TicketAttachment $ticketAttachment)
    {
        $localFile = File::insureLocal($ticketAttachment->file);
        $downloadLink = $localFile->directory.'/'.$localFile->basename;

        return response()->download($downloadLink);
    }

    public function destroy(TicketAttachment $ticketAttachment)
    {
        if (TicketAttachment::where('file', $ticketAttachment->file->serialize())->count() <= 1) {
            $this->deleteFile($ticketAttachment->file);
        }
        $ticketAttachment->delete();

        return response()->noContent();
    }
}
