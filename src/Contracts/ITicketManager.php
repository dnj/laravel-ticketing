<?php

namespace dnj\Ticket\Contracts;

use dnj\Ticket\Contracts\Exceptions\ITicketTitleHasBeenDisabledException;
use dnj\Ticket\Enums\TicketStatus;
use Illuminate\Http\UploadedFile;

interface ITicketManager extends ICanLog
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
    public function store(int $clientId, int $departmentId, string $message, array $files = [], ?string $title = null, ?int $userId = null, ?TicketStatus $status = null): IMessage;

    /**
     * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus} $changes
     */
    public function update(int $id, array $changes): ITicket;

    public function destroy(int $id): void;

    public function find(int $id): ?ITicket;

    public function updateSeenAt(int $ticket_id): void;

    public function ticketStatus(int $clientId): TicketStatus;
}
