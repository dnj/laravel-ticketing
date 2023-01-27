<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Exceptions\AttachmentAlreadyAsignedException;
use dnj\Ticket\Exceptions\UserIdMissingException;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketAttachment;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\LazyCollection;

class TicketMessageManager implements IMessageManager
{
    public function __construct(protected ILogger $userLogger, protected IAttachmentManager $attachmentManager)
    {
    }

    /**
     * @return LazyCollection<TicketMessage>
     */
    public function search(int $ticketId, ?array $filters): LazyCollection
    {
        return TicketMessage::query()->filter($filters)->lazy();
    }

    public function find(int $id): TicketMessage
    {
        return TicketMessage::query()->findOrFail($id);
    }

    public function update(int $id, array $changes, bool $userActivityLog = false): TicketMessage
    {
        $message = TicketMessage::query()->findOrFail($id);
        $message->fill($changes);

        if (isset($changes['attachments'])) {
            foreach ($changes['attachments'] as &$attachment) {
                if (!is_int($attachment)) {
                    continue;
                }

                $model = TicketAttachment::query()->findOrFail($attachment);
                if (null !== $model->message_id and $model->message_id !== $message->id) {
                    throw new AttachmentAlreadyAsignedException($attachment, $model->message_id);
                }
                $attachment = $model;
            }
            foreach ($changes['attachments'] as $attachment) {
                if (!$attachment instanceof UploadedFile) {
                    continue;
                }
                $attachment = $this->attachmentManager->storeByUpload($attachment, $message->id);
            }
        }
        $changesForLog = $message->changesForLog();
        $message->save();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($message)
                ->withProperties($changesForLog)
                ->log('updated');
        }

        if (isset($changes['attachments'])) {
            $currentAttachments = $message->attachments;
            $newAttachments = Collection::make($changes['attachments']);
            $added = $newAttachments->diff($currentAttachments)->whereNull('message_id');
            $deleted = $currentAttachments->diff($newAttachments);
            foreach ($added as $attach) {
                $attach->message_id = $message->id;
                $attach->save();
            }
            foreach ($deleted as $attach) {
                $this->attachmentManager->destroy($attach->id);
            }
        }

        return $message;
    }

    public function store(int $ticketId, string $message, array $files = [], ?int $userId = null, bool $userActivityLog = false): TicketMessage
    {
        if (null === $userId) {
            $userId = auth()->user()?->id;
        }
        if (null === $userId) {
            throw new UserIdMissingException();
        }
        $model = new TicketMessage([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message' => $message,
        ]);
        $changes = $model->changesForLog();
        $model->save();

        $ticket = Ticket::query()->findOrFail($ticketId);
        if ($userActivityLog and $ticket->messages()->doesntExist()) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('created');
        }

        $ticket->update([
            'status' => $ticket->client_id == $userId ? TicketStatus::UNREAD : TicketStatus::ANSWERED,
        ]);

        foreach ($files as $file) {
            $this->attachmentManager->storeByUpload($file, $model->id);
        }

        return $model;
    }

    public function destroy(int $id, bool $userActivityLog = false): void
    {
        $model = TicketMessage::query()->findOrFail($id);
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
