<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Http\Requests\DepartmentUpsertRequest;
use dnj\Ticket\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    public function __construct(private IDepartmentManager $department)
    {
    }

    public function index(Request $request)
    {
        $departments = $this->department->search($request->all());

        return new DepartmentResource($departments);
    }

    public function show(int $id)
    {
        $department = $this->department->find($id);

        return new DepartmentResource($department);
    }

    public function store(DepartmentUpsertRequest $request)
    {
        $department = $this->department->store($request->input('title'));

        return new DepartmentResource($department);
    }

    public function update(int $id, DepartmentUpsertRequest $request)
    {
        $department = $this->department->update($id, $request->validated());

        return new DepartmentResource($department);
    }

    public function destroy(int $id, Request $request)
    {
        $this->department->destroy($id);

        return response()->noContent();
    }
}
