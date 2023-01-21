<?php

namespace dnj\Ticket\Contracts;

interface IDepartmentManager
{
    public function list(?string $title);

    public function store(array $data);

    public function update(int $id, array $data);

    public function destroy(int $id);

    public function find(int $id);
}
