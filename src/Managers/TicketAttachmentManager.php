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

    public function __construct(protected ILogger $userLogger, private TicketAttachment $model, private ITicketManager $ticket)
    {
        $this->setSaveLogs(true);
    }

    public function search(int $messageId): iterable
    {
        $attachments = $this->model->query()
            ->where('message_id', $messageId)->get();

        return $attachments;
    }

    public function find(int $id): TicketAttachment
    {
        return $this->model->findOrFail($id);
    }

    public function findOrphans(): iterable
    {
        return $this->model->query()->whereNull('message_id')
            ->where('created_at', '<=', now()->subMinutes(10))->get();
    }

    public function update(int $id, array $changes): TicketAttachment
    {
        $this->model = $this->model->whereId($id)->whereNull('message_id')->first();
        $this->model->message_id = $changes['message_id'];

        $this->saveLog(log: 'updated');

        return $this->model;
    }

    public function storeByUpload(UploadedFile $file, ?int $message_id): IAttachment
    {
        $this->model = $this->model->fromUpload($file);
        $this->model->putFile($file);
        $this->model->message_id = $message_id;

        $this->saveLog(log: 'created');

        $this->model->save();

        return $this->model;
    }

    public function destroy(int $id): void
    {
        $this->model = $this->model->find($id);

        if ($this->model->query()->where('file', serialize($this->model->getFile()))->count() <= 1) {
            $this->model->getFile()->delete();
        }

        $this->saveLog(log: 'deleted');

        $this->model->delete();
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
