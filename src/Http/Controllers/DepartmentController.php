<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Http\Requests\DepartmentUpsertRequest;
use dnj\Ticket\Http\Resources\DepartmentResource;
use dnj\Ticket\Models\Department;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    public function __construct(protected ILogger $userLogger)
    {
    }

    public function index(Request $request)
    {
        $items = Department::where('title', 'like', '%'.$request->input('title', '').'%')
            ->orderBy('id')
            ->cursorPaginate();

        return new DepartmentResource($items);
    }

    public function show(Department $department)
    {
        return new DepartmentResource($department);
    }

    public function store(DepartmentUpsertRequest $request)
    {
        $department = new Department();
        $department->fill($request->validated());
        $changes = $department->changesForLog();
        $department->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($department)
            ->withProperties($changes)
            ->log('created');

        return new DepartmentResource($department);
    }

    public function update(Department $department, DepartmentUpsertRequest $request)
    {
        $department->fill($request->validated());
        $changes = $department->changesForLog();
        $department->save();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($department)
            ->withProperties($changes)
            ->log('updated');

        return new DepartmentResource($department);
    }

    public function destroy(Department $department, Request $request)
    {
        $changes = $department->toArray();

        $department->delete();

        $this->userLogger
            ->withRequest($request)
            ->performedOn($department)
            ->withProperties($changes)
            ->log('deleted');

        return response()->noContent();
    }
}
