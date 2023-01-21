<?php

namespace dnj\Ticket\Http\Controllers;

use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Http\Requests\DepartmentUpsertRequest;
use dnj\Ticket\Http\Resources\DepartmentResource;
use dnj\UserLogger\Contracts\ILogger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    public function __construct(protected ILogger $userLogger, private IDepartmentManager $department)
    {
    }

    public function index(Request $request)
    {
        $items = $this->department->list($request->input('title', ''));

        return new DepartmentResource($items);
    }

    public function show(int $id)
    {
        $department = $this->department->find($id);

        return new DepartmentResource($department);
    }

    public function store(DepartmentUpsertRequest $request)
    {
        $response = $this->department->store($request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($response['model'])
            ->withProperties($response['diff'])
            ->log('created');

        return new DepartmentResource($response['model']);
    }

    public function update(int $id, DepartmentUpsertRequest $request)
    {
        $response = $this->department->update($id, $request->validated());

        $this->userLogger
            ->withRequest($request)
            ->performedOn($response['model'])
            ->withProperties($response['diff'])
            ->log('updated');

        return new DepartmentResource($response['model']);
    }

    public function destroy(int $id, Request $request)
    {
        $response = $this->department->destroy($id);

        $this->userLogger
            ->withRequest($request)
            ->performedOn($response['model'])
            ->withProperties($response['diff'])
            ->log('deleted');

        return response()->noContent();
    }
}
