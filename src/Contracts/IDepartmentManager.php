<?php

namespace dnj\Ticket\Contracts;

interface IDepartmentManager
{
    /**
     * @param array{title?:string,created_start_date?:string,created_end_date?:string,updated_start_date?:string,updated_end_date?:string}|null $filters
     *
     * @return iterable<IDepartment>
     */
    public function search(?array $filters): iterable;

    public function store(string $title, bool $userActivityLog = false): IDepartment;

    /**
     * @param array{title?:string} $changes
     */
    public function update(int $id, array $changes, bool $userActivityLog = false): IDepartment;

    public function destroy(int $id, bool $userActivityLog = false): void;

    public function find(int $id): IDepartment;
}
