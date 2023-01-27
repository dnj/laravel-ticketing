<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Http\Requests\DepartmentUpsertRequest;
use dnj\Ticket\Http\Resources\DepartmentResource;
use dnj\Ticket\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    public function __construct(protected IDepartmentManager $departmentManager)
    {
    }

    public function index(Request $request)
    {
        $departments = Department::query()->filter($request->all())->cursorPaginate();

        return new DepartmentResource($departments);
    }

    public function show(int $id)
    {
        $department = $this->departmentManager->find($id);

        return new DepartmentResource($department);
    }

    public function store(DepartmentUpsertRequest $request)
    {
        $department = $this->departmentManager->store($request->input('title'), true);

        return new DepartmentResource($department);
    }

    public function update(int $id, DepartmentUpsertRequest $request)
    {
        $department = $this->departmentManager->update($id, $request->validated(), true);

        return new DepartmentResource($department);
    }

    public function destroy(int $id)
    {
        $this->departmentManager->destroy($id, true);

        return response()->noContent();
    }
}
