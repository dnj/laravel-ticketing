<?php

namespace dnj\Ticket\Contracts;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface ITicketAttachmentManager
{
    public function list(int $ticket_id, string $sort): CursorPaginator;

    public function store(UploadedFile $file, ?int $message_id): Model;

    public function update(array $id, int $message_id): void;

    public function destroy(int $id): array;

    public function find(int $id): Model;
}
