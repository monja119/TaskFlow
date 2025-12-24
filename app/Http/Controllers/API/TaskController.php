<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index()
    {
        $user = request()->user();

        $tasks = Task::query()
            ->with(['project', 'user'])
            ->when($user && $user->isMember(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('priority'), fn ($query, $priority) => $query->where('priority', $priority))
            ->when(request('project_id'), fn ($query, $projectId) => $query->where('project_id', $projectId))
            ->when(request('user_id'), fn ($query, $userId) => $query->where('user_id', $userId))
            ->when(request('search'), fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->paginate();

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

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
        $task->update($request->validated());

        return new TaskResource($task->fresh()->load(['project', 'user']));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
