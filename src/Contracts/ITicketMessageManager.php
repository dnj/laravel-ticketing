<?php

namespace dnj\Ticket\Contracts;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

interface ITicketMessageManager
{
    public function list(int $ticket_id, string $sort): CursorPaginator;

    public function store(int $ticket_id, array $data): array;

    public function update(int $id, array $data): array;

    public function destroy(int $id): array;

    public function find(int $id): Model;
}
