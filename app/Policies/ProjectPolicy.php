<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isManager() || $user->isMember();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isManager() || $project->user_id === $user->id;
    }
}
