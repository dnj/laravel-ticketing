<?php

namespace dnj\Ticket\Http\Controllers\Concerns;

use dnj\Ticket\Contracts\ITicketAttachmentManager;
use Illuminate\Http\Request;

trait WorksWithAttachments
{
    protected function saveAttachments(Request $request, int $id = null): array
    {
        $attachments = [];

        $ticketAttachment = app()->make(ITicketAttachmentManager::class);

        // if request has file attachment, That will be store and attached to message
        if ($request->hasfile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $ticketAttachment->store($file, $id);
            }
        } elseif ($request->has('attachments') && is_array($request->input('attachments'))) {
            $ticketAttachment->update($request->input('attachments'), $id);
        }

        return $attachments;
    }
}
