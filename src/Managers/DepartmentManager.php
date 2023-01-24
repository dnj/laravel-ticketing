<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IDepartment;
use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Managers\Concerns\WorksWithLog;
use dnj\Ticket\Models\Department;
use dnj\UserLogger\Contracts\ILogger;

class DepartmentManager implements IDepartmentManager
{
    use WorksWithLog;

    private bool $enableLog;

    public function __construct(protected ILogger $userLogger, private Department $department)
    {
        $this->setSaveLogs(true);
    }

    public function search(?array $filters): iterable
    {
        $q = $this->department->query();
        $q->orderBy('updated_at', 'desc');
        $q->when(isset($filters['title']), function ($q) use ($filters) {
            return $q->where('title', 'like', '%'.$filters['title'].'%');
        })
            ->when(isset($filters['created_start_date']), function ($q) use ($filters) {
                $created_end_date = isset($filters['created_end_date']) ? $filters['created_end_date'] : now();

                return $q->whereBetween('created_at', [$filters['created_start_date'], $created_end_date]);
            })
            ->when(isset($filters['updated_start_date']), function ($q) use ($filters) {
                $updated_end_date = isset($filters['updated_end_date']) ? $filters['updated_end_date'] : now();

                return $q->whereBetween('updated_at', [$filters['updated_start_date'], $updated_end_date]);
            });

        return $q->cursorPaginate();
    }

    public function find(int $id): Department
    {
        return $this->department->findOrFail($id);
    }

    public function update(int $id, array $data): IDepartment
    {
        $department = $this->find($id);
        $department->fill($data);
        $changes = $department->changesForLog();

        $this->saveLog(model: $department, changes: $changes, log: 'updated');

        $department->save();

        return $department;
    }

    public function store(string $title): IDepartment
    {
        $this->department->title = $title;
        $changes = $this->department->changesForLog();

        $this->saveLog(model: $this->department, changes: $changes, log: 'created');

        $this->department->save();

        return $this->department;
    }

    public function destroy(int $id): void
    {
        $department = $this->find($id);
        $changes = $department->toArray();

        $this->saveLog(model: $department, changes: $changes, log: 'deleted');

        $department->delete();
    }

    public function setSaveLogs(bool $save): void
    {
        $this->enableLog = $save;
    }

    public function getSaveLogs(): bool
    {
        return $this->enableLog;
    }
}
