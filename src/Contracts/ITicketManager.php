<?php

namespace dnj\Ticket\Contracts;

use dnj\Ticket\Contracts\Exceptions\ITicketTitleHasBeenDisabledException;
use dnj\Ticket\Enums\TicketStatus;
use Illuminate\Http\UploadedFile;

interface ITicketManager
{
    /**
     * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus[],created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
     *
     * @return iterable<ITicket>
     */
    public function search(?array $filters): iterable;

    /**
     * @param array<int|UploadedFile> $files
     *
     * @throws ITicketTitleHasBeenDisabledException if $title is set but title is disabled
     */
    public function store(int $clientId, int $departmentId, string $message, array $files = [], ?string $title = null, ?int $userId = null, ?TicketStatus $status = null, bool $userActivityLog = false): IMessage;

    /**
     * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus} $changes
     */
    public function update(int $id, array $changes, bool $userActivityLog = false): ITicket;

    public function destroy(int $id, bool $userActivityLog = false): void;

    public function find(int $id): ?ITicket;

    public function markAsSeenByClient(int $id): void;

    public function markAsSeenBySupport(int $id): void;
}
