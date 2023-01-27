<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IAttachment;
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Exceptions\AttachmentAlreadyAsignedException;
use dnj\Ticket\Models\TicketAttachment;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class TicketAttachmentManager implements IAttachmentManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return Collection<TicketAttachment>
     */
    public function search(int $messageId): Collection
    {
        return TicketAttachment::query()
             ->where('message_id', $messageId)
             ->get();
    }

    public function find(int $id): TicketAttachment
    {
        return TicketAttachment::query()->findOrFail($id);
    }

    /**
     * @return Collection<TicketAttachment>
     */
    public function findOrphans(): Collection
    {
        return TicketAttachment::query()
            ->whereNull('message_id')
            ->where('created_at', '<=', now()->subMinutes(10))
            ->get();
    }

    public function update(int $id, array $changes, bool $userActivityLog = false): TicketAttachment
    {
        $model = TicketAttachment::query()->findOrFail($id);
        if (null !== $model->message_id) {
            throw new AttachmentAlreadyAsignedException($id, $model->message_id);
        }

        $model->message_id = $changes['message_id'];
        $changes = $model->changesForLog();
        $model->save();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('updated');
        }

        return $model;
    }

    public function storeByUpload(UploadedFile $file, ?int $message_id, bool $userActivityLog = false): IAttachment
    {
        $model = TicketAttachment::fromUpload($file);
        $model->message_id = $message_id;
        $model->putFile($file);
        $changes = $model->changesForLog();
        $model->save();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('created');
        }

        return $model;
    }

    public function destroy(int $id, bool $userActivityLog = false): void
    {
        $model = TicketAttachment::query()->findOrFail($id);

        if (TicketAttachment::query()->where('file', serialize($model->getFile()))->count() <= 1) {
            $model->getFile()->delete();
        }
        $model->delete();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($model->toArray())
                ->log('deleted');
        }
    }
}
