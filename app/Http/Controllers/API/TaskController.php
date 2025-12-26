<?php

namespace App\Http\Controllers\API;

use App\DataTransferObjects\TaskFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\Task\TaskService;

class TaskController extends Controller
{
    public function __construct(private TaskService $tasks)
    {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(TaskIndexRequest $request)
    {
        $filters = TaskFilterData::fromRequest($request);

        $tasks = $this->tasks->listForUser($filters, $request->user());

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->tasks->create($request->validated(), $request->user()->id);

        return (new TaskResource($task->load(['project', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task)
    {
        return new TaskResource($task->load(['project', 'user']));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $updated = $this->tasks->update($task, $request->validated());

        return new TaskResource($updated);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
