<?php

namespace dnj\Ticket\Controllers;

use dnj\Ticket\FileHelpers;
use dnj\Ticket\Requests\TicketAttachmentRequest;
use dnj\Ticket\Resources\TicketAttachmentResource;
use dnj\Ticket\Models\TicketAttachment;
use dnj\Filesystem\Contracts\IFile;
use Illuminate\Routing\Controller;

class TicketAttachmentController extends Controller
{
    use FileHelpers;

    public function store(TicketAttachmentRequest $request, IFile $file)
    {

        $attachmentList = [];
        $baseDirectory = $file->directory;
        if ($request->hasfile('attachments')) {

            foreach ($request->file('attachments') as $attachment) {

                $this->saveFile($attachment->path(), $attachment->extension(), $file);

                $attach =  TicketAttachment::create([
                    'name' => $attachment->getClientOriginalName(),
                    'file' => $file->serialize(),
                    'mime' => $attachment->getClientMimeType(),
                    'size' => $attachment->getSize()
                ]);

                array_push($attachmentList, $attach);
                $file->directory = $baseDirectory;
            }
        }

        return new TicketAttachmentResource($attachmentList);
    }

    public function show(TicketAttachment $ticketAttachment)
    {
        // 
    }

    public function destroy(TicketAttachment $ticketAttachment, IFile $file)
    {
        $attachFile = $ticketAttachment->file;
        if (TicketAttachment::where('file', $attachFile)->count() <= 1) {
            $file->unserialize($attachFile);
            $this->deleteFile($file);
        }
        $ticketAttachment->delete();
        return response()->noContent();
    }
}
