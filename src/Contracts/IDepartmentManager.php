<?php

namespace dnj\Ticket\Contracts;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Model;

interface IDepartmentManager
{
    public function list(?string $title): CursorPaginator;

    public function store(array $data): array;

    public function update(int $id, array $data): array;

    public function destroy(int $id): array;

    public function find(int $id): Model;
}
