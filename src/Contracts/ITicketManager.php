<?php

namespace dnj\Ticket\Contracts;

use dnj\Ticket\Enums\TicketStatus;
use dnj\Ticket\Models\Ticket;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

interface ITicketManager
{
    public function list(?array $param): CursorPaginator;

    public function store(array $data): array;

    public function update(int $id, array $data): array;

    public function destroy(int $id): array;

    public function find(int $id): Model;

    public function updateSeenAt(int $ticket_id): void;

    public function ticketStatus(Ticket $ticket): TicketStatus;
}
