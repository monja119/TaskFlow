<?php

namespace App\Services\Task;

use App\Enums\TaskStatus;
use App\DataTransferObjects\TaskFilterData;
use App\Models\Task;
use App\Models\User;
use App\Services\Notification\TaskAssignedNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TaskService
{
    public function __construct(
        private readonly TaskAssignedNotificationService $taskAssignedNotification,
    ) {}

    public function listForUser(TaskFilterData $filters, ?User $user): LengthAwarePaginator
    {
        $query = Task::query()
            ->with(['project', 'user', 'users']);

        if ($user && $user->isMember()) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('users', fn (Builder $sub) => $sub->where('user_id', $user->id))
                    ->orWhereHas('project', fn (Builder $sub) => $sub->where('user_id', $user->id));
            });
        }

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($filters->perPage);
    }

    public function create(array $validated, int $defaultUserId): Task
    {
        $data = $this->normalizePayload($validated, $defaultUserId);
        $users = $data['users'] ?? [];
        unset($data['users']);

        $task = Task::create($data);
        
        if (!empty($users)) {
            $task->users()->attach($users);
            
            // Send notifications to assigned users
            $assignedUsers = User::whereIn('id', $users)->get();
            $this->taskAssignedNotification->send($task, ['newUsers' => $assignedUsers->all()]);
        }

        return $task->fresh(['project', 'user', 'users']);
    }

    public function update(Task $task, array $validated): Task
    {
        $data = $this->normalizePayload($validated, null, $task);
        $users = $data['users'] ?? null;
        unset($data['users']);

        $task->update($data);
        
        if ($users !== null) {
            $existingUserIds = $task->users()->pluck('users.id')->toArray();
            $newUserIds = array_diff($users, $existingUserIds);
            
            $task->users()->sync($users);
            
            // Send notifications only to newly assigned users
            if (!empty($newUserIds)) {
                $newUsers = User::whereIn('id', $newUserIds)->get();
                $this->taskAssignedNotification->send($task, ['newUsers' => $newUsers->all()]);
            }
        }

        return $task->fresh(['project', 'user', 'users']);
    }

    private function applyFilters(Builder $query, TaskFilterData $filters): Builder
    {
        if ($filters->status) {
            $query->where('status', $filters->status->value);
        }

        if ($filters->priority) {
            $query->where('priority', $filters->priority->value);
        }

        if ($filters->projectId) {
            $query->where('project_id', $filters->projectId);
        }

        if ($filters->userId) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('user_id', $filters->userId)
                    ->orWhereHas('users', fn (Builder $sub) => $sub->where('user_id', $filters->userId));
            });
        }

        if ($filters->search) {
            $query->where('title', 'like', "%{$filters->search}%");
        }

        return $query;
    }

    private function normalizePayload(array $validated, ?int $defaultUserId = null, ?Task $task = null): array
    {
        $data = $validated;

        // Set user_id if not provided and we have a default
        if (!isset($data['user_id']) && $defaultUserId) {
            $data['user_id'] = $defaultUserId;
        }

        // Convert estimated_hours to estimate_minutes if provided
        if (isset($data['estimated_hours']) && !isset($data['estimate_minutes'])) {
            $data['estimate_minutes'] = (int) ($data['estimated_hours'] * 60);
        }

        // Remove estimated_hours as it's not a database column
        unset($data['estimated_hours']);

        $statusValue = $data['status'] ?? null;
        $status = $statusValue instanceof TaskStatus ? $statusValue : ($statusValue ? TaskStatus::from($statusValue) : null);

        if ($status === TaskStatus::COMPLETED) {
            $data['completed_at'] = $task?->completed_at ?? Carbon::now();
        } elseif ($status !== null && $status !== TaskStatus::COMPLETED) {
            $data['completed_at'] = null;
        }

        return $data;
    }
}
