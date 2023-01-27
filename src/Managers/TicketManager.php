<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Exceptions\UserIdMissingException;
use dnj\Ticket\Models\Ticket;
use dnj\Ticket\Models\TicketMessage;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Support\LazyCollection;

class TicketManager implements ITicketManager
{
    public function __construct(
        protected ILogger $userLogger,
        protected IMessageManager $messageManager,
    ) {
    }

    /**
     * @return LazyCollection<Ticket>
     */
    public function search(?array $filters): LazyCollection
    {
        return Ticket::query()
            ->orderBy('updated_at', 'desc')
            ->filter($filters)
            ->lazy();
    }

    public function find(int $id): Ticket
    {
        return Ticket::query()->findOrFail($id);
    }

    public function update(int $id, array $changes, bool $userActivityLog = false): Ticket
    {
        $model = Ticket::query()->findOrFail($id);
        $model->fill($changes);
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

    public function store(int $clientId, int $departmentId, string $message, array $files = [], ?string $title = null, ?int $userId = null, ?TicketStatus $status = null, bool $userActivityLog = false): TicketMessage
    {
        if (null === $userId) {
            $userId = auth()->user()?->id;
        }
        if (null === $userId) {
            throw new UserIdMissingException();
        }
        if (null === $status) {
            $status = $userId == $clientId ? TicketStatus::UNREAD : TicketStatus::ANSWERED;
        }
        $model = new Ticket([
            'title' => $title,
            'client_id' => $clientId,
            'department_id' => $departmentId,
            'status' => $status,
        ]);

        $changes = $model->changesForLog();
        $model->save();

        $message = $this->messageManager->store($model->getID(), $message, $files, $userId);

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('created');
        }

        return $message;
    }

    public function destroy(int $id, bool $userActivityLog = false): void
    {
        $model = Ticket::query()->findOrFail($id);
        $model->delete();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($model->toArray())
                ->log('deleted');
        }
    }

    public function markAsSeenByClient(int $id): void
    {
        Ticket::query()
            ->findOrFail($id)
            ->messages()
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }

    public function markAsSeenBySupport(int $id): void
    {
        $ticket = Ticket::query()->findOrFail($id);
        if (TicketStatus::UNREAD === $ticket->status) {
            $ticket->status = TicketStatus::READ;
            $ticket->save();
        }
    }
}
