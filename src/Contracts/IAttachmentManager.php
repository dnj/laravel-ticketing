<?php

namespace dnj\Ticket\Contracts;

use Illuminate\Http\UploadedFile;

interface IAttachmentManager
{
    /**
     * @return iterable<IAttachment>
     */
    public function search(int $messageId): iterable;

    /**
     * @return iterable<IAttachment>
     */
    public function findOrphans(): iterable;

    public function storeByUpload(UploadedFile $file, ?int $messageId, bool $userActivityLog = false): IAttachment;

    /**
     * @param array{message_id?:int} $changes
     */
    public function update(int $id, array $changes, bool $userActivityLog = false): IAttachment;

    public function destroy(int $id, bool $userActivityLog = false): void;

    public function find(int $id): IAttachment;
}
