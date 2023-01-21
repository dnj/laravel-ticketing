<?php

namespace dnj\Ticket\Managers;

use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Models\Department;
use Illuminate\Contracts\Pagination\CursorPaginator;

class DepartmentManager implements IDepartmentManager
{
    public function __construct(private Department $department)
    {
    }

    public function list(?string $title): CursorPaginator
    {
        return $this->department->where('title', 'like', '%'.$title.'%')
            ->orderBy('id')
            ->cursorPaginate();
    }

    public function find(int $id)
    {
        return $this->department->findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $department = $this->department->findOrFail($id);
        $department->fill($data);
        $changes = $department->changesForLog();
        $department->save();

        return ['model' => $department, 'diff' => $changes];
    }

    public function store(array $data)
    {
        $this->department->fill($data);
        $changes = $this->department->changesForLog();
        $this->department->save();

        return ['model' => $this->department, 'diff' => $changes];
    }

    public function destroy(int $id)
    {
        $department = $this->department->findOrFail($id);
        $changes = $department->toArray();
        $department->delete();

        return ['model' => $department, 'diff' => $changes];
    }
}
