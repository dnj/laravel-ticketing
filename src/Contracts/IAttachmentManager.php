<?php

namespace dnj\Ticket\Contracts;

use Illuminate\Http\UploadedFile;

interface IAttachmentManager extends ICanLog
{
    /**
     * @return iterable<IAttachment>
     */
    public function search(int $messageId): iterable;

    /**
     * @return iterable<IAttachment>
     */
    public function findOrphans(): iterable;

    public function storeByUpload(UploadedFile $file, ?int $messageId): IAttachment;

    /**
     * @param array{message_id?:int} $changes
     */
    public function update(int $id, array $changes): IAttachment;

    public function destroy(int $id): void;

    public function find(int $id): ?IAttachment;
}
