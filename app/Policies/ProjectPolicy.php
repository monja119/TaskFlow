<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isManager() || $user->isMember();
    }

    public function view(User $user, Project $project): bool
    {
        // Managers can view all projects; creators can view their own projects.
        if ($user->isManager() || $project->user_id === $user->id) {
            return true;
        }

        // Allow viewing if the user is assigned to the project (many-to-many relation).
        return $project->users()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isManager();
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isManager();
    }
}
