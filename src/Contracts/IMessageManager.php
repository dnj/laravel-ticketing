<?php

namespace dnj\Ticket\Contracts;

interface IMessageManager extends ICanLog
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
    public function store(int $ticketId, string $message, array $files = [], ?int $userId = null): IMessage;

    /**
     * @param array{message?:string,userId?:int} $changes
     */
    public function update(int $id, array $changes): IMessage;

    public function destroy(int $id): void;

    public function find(int $id): ?IMessage;
}
