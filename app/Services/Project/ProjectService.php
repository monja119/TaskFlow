<?php

namespace App\Services\Project;

use App\Models\Project;
use App\Models\User;
use App\DataTransferObjects\ProjectFilterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProjectService
{
    public function listForUser(ProjectFilterData $filters, ?User $user): LengthAwarePaginator
    {
        $query = Project::query()
            ->with('user')
            ->withCount('tasks');

        if ($user && $user->isMember()) {
            $query->where('user_id', $user->id);
        }

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($filters->perPage);
    }

    public function create(array $validated): Project
    {
        $project = Project::create($validated);

        return $project->fresh('user');
    }

    public function update(Project $project, array $validated): Project
    {
        $project->update($validated);

        return $project->fresh('user');
    }

    private function applyFilters(Builder $query, ProjectFilterData $filters): Builder
    {
        if ($filters->status) {
            $query->where('status', $filters->status->value);
        }

        if ($filters->search) {
            $query->where('name', 'like', "%{$filters->search}%");
        }

        return $query;
    }
}
