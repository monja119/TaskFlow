<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $user->isManager()
            || $task->users()->where('user_id', $user->id)->exists()
            || $task->project?->users()->where('user_id', $user->id)->exists()
            || $task->project?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id
            || $task->users()->where('user_id', $user->id)->exists()
            || $task->project?->users()->where('user_id', $user->id)->exists()
            || $task->project?->user_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isManager() || $task->project?->user_id === $user->id;
    }
}
