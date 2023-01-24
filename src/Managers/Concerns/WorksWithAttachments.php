<?php

namespace dnj\Ticket\Managers\Concerns;

use dnj\Ticket\Contracts\IAttachmentManager;

trait WorksWithAttachments
{
    protected function saveAttachments(array $files, int $id = null): array
    {
        $attachments = [];

        $ticketAttachment = app()->make(IAttachmentManager::class);

        foreach ($files as $file) {
            if (is_file($file)) {
                $attachments[] = $ticketAttachment->storeByUpload($file, $id);
            } else {
                $ticketAttachment->update($file, $id);
            }
        }

        return $attachments;
    }
}
