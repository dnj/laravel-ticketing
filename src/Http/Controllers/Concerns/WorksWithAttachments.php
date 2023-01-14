<?php

namespace dnj\Ticket\Http\Controllers\Concerns;

use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Http\Request;

trait WorksWithAttachments
{
    protected function saveAttachments(Request $request, int $id = null): array
    {
        $attachments = [];

        // if request has file attachment, That will be store and attached to message
        if ($request->hasfile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachment = TicketAttachment::fromUpload($file);
                $attachment->putFile($file);
                $attachment->message_id = $id;
                $attachment->save();

                $attachments[] = $attachment;
            }
        } elseif ($request->has('attachments') && is_array($request->input('attachments'))) {
            TicketAttachment::whereIn('id', $request->input('attachments'))->whereNull('message_id')->update(['message_id' => $id]);
        }

        return $attachments;
    }
}
