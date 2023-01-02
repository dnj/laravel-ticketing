<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Http\Requests\DepartmentUpsertRequest;
use dnj\Ticket\Http\Resources\DepartmentResource;
use dnj\Ticket\Models\Department;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function index(Request $request)
    {
        $items = Department::where('title', 'like', '%' . $request->input('title', '') . '%')
            ->orderBy('id')
            ->cursorPaginate(10);

        return $items;
    }

    public function show(Department $department)
    {
        return new DepartmentResource($department);
    }

    public function store(DepartmentUpsertRequest $request)
    {
        $department = Department::create($request->validated());

        return new DepartmentResource($department);
    }

    public function update(Department $department, DepartmentUpsertRequest $request)
    {
        $department->fill($request->validated());
        $department->save();

        return new DepartmentResource($department);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->noContent();
    }
}
