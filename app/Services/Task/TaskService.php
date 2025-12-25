<?php

namespace App\Services\Task;

use App\Enums\TaskStatus;
use App\DataTransferObjects\TaskFilterData;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TaskService
{
    public function listForUser(TaskFilterData $filters, ?User $user): LengthAwarePaginator
    {
        $query = Task::query()
            ->with(['project', 'user']);

        if ($user && $user->isMember()) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('project', fn (Builder $sub) => $sub->where('user_id', $user->id));
            });
        }

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($filters->perPage);
    }

    public function create(array $validated, int $defaultUserId): Task
    {
        $data = $this->normalizePayload($validated, $defaultUserId);

        $task = Task::create($data);

        return $task->fresh(['project', 'user']);
    }

    public function update(Task $task, array $validated): Task
    {
        $data = $this->normalizePayload($validated, null, $task);

        $task->update($data);

        return $task->fresh(['project', 'user']);
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
            $query->where('user_id', $filters->userId);
        }

        if ($filters->search) {
            $query->where('title', 'like', "%{$filters->search}%");
        }

        return $query;
    }

    private function normalizePayload(array $validated, ?int $defaultUserId = null, ?Task $task = null): array
    {
        $data = $validated;

        if ($defaultUserId !== null && ! isset($data['user_id'])) {
            $data['user_id'] = $defaultUserId;
        }

        if (isset($data['estimated_hours'])) {
            $data['estimate_minutes'] = (int) round($data['estimated_hours'] * 60);
            unset($data['estimated_hours']);
        }

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
