<?php

namespace dnj\Ticket\Contracts;

interface IMessageManager
{
    /**
     * @param array{user_id?:int,created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
     *
     * @return iterable<IMessage>
     */
    public function search(int $ticketId, ?array $filters): iterable;

    /**
     * @param array<int|UploadedFile> $files
     */
    public function store(int $ticketId, string $message, array $files = [], ?int $userId = null, bool $userActivityLog = false): IMessage;

    /**
     * @param array{message?:string,userId?:int} $changes
     */
    public function update(int $id, array $changes, bool $userActivityLog = false): IMessage;

    public function destroy(int $id, bool $userActivityLog = false): void;

    public function find(int $id): IMessage;
}
