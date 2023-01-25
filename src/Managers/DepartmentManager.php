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

    public function __construct(protected ILogger $userLogger, private Department $model)
    {
        $this->setSaveLogs(true);
    }

    public function search(?array $filters): iterable
    {
        $q = $this->model->query();
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
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): IDepartment
    {
        $this->model = $this->find($id);
        $this->model->fill($data);
        $changes = $this->model->changesForLog();

        $this->saveLog(changes: $changes, log: 'updated');

        $this->model->save();

        return $this->model;
    }

    public function store(string $title): IDepartment
    {
        $this->model->title = $title;
        $changes = $this->model->changesForLog();

        $this->saveLog(changes: $changes, log: 'created');

        $this->model->save();

        return $this->model;
    }

    public function destroy(int $id): void
    {
        $this->find($id);
        $changes = $this->model->toArray();

        $this->saveLog(changes: $changes, log: 'deleted');

        $this->model->delete();
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
