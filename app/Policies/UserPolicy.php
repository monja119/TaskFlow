<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        // Seul l'administrateur peut gérer les utilisateurs
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false; // Géré par before()
    }

    public function view(User $user, User $model): bool
    {
        return false; // Géré par before()
    }

    public function create(User $user): bool
    {
        return false; // Géré par before()
    }

    public function update(User $user, User $model): bool
    {
        return false; // Géré par before()
    }

    public function delete(User $user, User $model): bool
    {
        // L'admin ne peut pas se supprimer lui-même
        if ($user->id === $model->id) {
            return false;
        }

        return false; // Géré par before()
    }

    public function restore(User $user, User $model): bool
    {
        return false; // Géré par before()
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false; // Géré par before()
    }
}
