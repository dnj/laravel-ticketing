<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\ITicketAttachmentManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Models\TicketAttachment;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class TicketAttachmentManager implements ITicketAttachmentManager
{
    public function __construct(private TicketAttachment $attachment, private ITicketManager $ticket)
    {
    }

    public function list(int $ticket_id, string $sort): CursorPaginator
    {
        $attachments = $this->attachment->query()
            ->cursorPaginate();

        return $attachments;
    }

    public function find(int $id): Model
    {
        return $this->attachment->findOrFail($id);
    }

    public function update(array $ids, int $message_id): void
    {
        $this->attachment->query()->whereIn('id', $ids)
            ->whereNull('message_id')
            ->update(['message_id' => $message_id]);
    }

    public function store(UploadedFile $file, ?int $message_id): Model
    {
        $attachment = $this->attachment->fromUpload($file);
        $attachment->putFile($file);
        $attachment->message_id = $message_id;
        $attachment->save();

        return $attachment;
    }

    public function destroy(int $id): array
    {
        $attachment = $this->attachment->find($id);
        $changes = $attachment->toArray();

        if ($this->attachment->query()->where('file', serialize($attachment->file))->count() <= 1) {
            $attachment->file->delete();
        }

        $attachment->delete();

        return ['model' => $attachment, 'diff' => $changes];
    }
}
