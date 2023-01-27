<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IDepartment;
use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Models\Department;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Support\LazyCollection;

class DepartmentManager implements IDepartmentManager
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    /**
     * @return LazyCollection<Department>
     */
    public function search(?array $filters): LazyCollection
    {
        return Department::query()
            ->orderBy('updated_at', 'desc')
            ->filter($filters)
            ->lazy();
    }

    public function find(int $id): Department
    {
        return Department::query()->findOrFail($id);
    }

    public function update(int $id, array $data, bool $userActivityLog = false): Department
    {
        $model = Department::query()
            ->findOrFail($id)
            ->fill($data);
        $changes = $model->changesForLog();
        $model->save();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('updated');
        }

        return $model;
    }

    public function store(string $title, bool $userActivityLog = false): IDepartment
    {
        $model = new Department([
            'title' => $title,
        ]);
        $changes = $model->changesForLog();
        $model->save();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($changes)
                ->log('created');
        }

        return $model;
    }

    public function destroy(int $id, bool $userActivityLog = false): void
    {
        $model = Department::query()->findOrFail($id);
        $model->delete();

        if ($userActivityLog) {
            $this->userLogger
                ->withRequest(request())
                ->performedOn($model)
                ->withProperties($model->toArray())
                ->log('deleted');
        }
    }
}
