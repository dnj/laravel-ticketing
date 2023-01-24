<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IAttachment;
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Managers\Concerns\WorksWithLog;
use dnj\Ticket\Models\TicketAttachment;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\UploadedFile;

class TicketAttachmentManager implements IAttachmentManager
{
    use WorksWithLog;

    private bool $enableLog;

    public function __construct(protected ILogger $userLogger, private TicketAttachment $attachment, private ITicketManager $ticket)
    {
        $this->setSaveLogs(true);
    }

    public function search(int $messageId): iterable
    {
        $attachments = $this->attachment->query()
            ->where('message_id', $messageId)->get();

        return $attachments;
    }

    public function find(int $id): TicketAttachment
    {
        return $this->attachment->findOrFail($id);
    }

    public function findOrphans(): iterable
    {
        return $this->attachment->query()->whereNull('message_id')
            ->where('created_at', '<=', now()->subMinutes(10))->get();
    }

    public function update(int $id, array $changes): TicketAttachment
    {
        $attachment = $this->attachment->whereId($id)->whereNull('message_id')->first();
        $attachment->message_id = $changes['message_id'];

        $changes = $attachment->changesForLog();

        $this->saveLog(model: $attachment, changes: $changes, log: 'updated');

        return $this->attachment;
    }

    public function storeByUpload(UploadedFile $file, ?int $message_id): IAttachment
    {
        $attachment = $this->attachment->fromUpload($file);
        $attachment->putFile($file);
        $attachment->message_id = $message_id;
        $changes = $attachment->changesForLog();

        $this->saveLog(model: $attachment, changes: $changes, log: 'created');

        $attachment->save();

        return $attachment;
    }

    public function destroy(int $id): void
    {
        $attachment = $this->attachment->find($id);
        $changes = $attachment->toArray();

        if ($this->attachment->query()->where('file', serialize($attachment->file))->count() <= 1) {
            $attachment->file->delete();
        }

        $this->saveLog(model: $attachment, changes: $changes, log: 'deleted');

        $attachment->delete();
    }

    public function setSaveLogs(bool $save): void
    {
        $this->enableLog = $save;
    }

    public function getSaveLogs(): bool
    {
        return $this->enableLog;
    }
}
