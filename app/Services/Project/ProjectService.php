<?php

namespace App\Services\Project;

use App\Models\Project;
use App\Models\User;
use App\DataTransferObjects\ProjectFilterData;
use App\Services\Notification\ProjectUserAddedNotificationService;
use App\Services\Notification\ProjectAtRiskNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProjectService
{
    public function __construct(
        private readonly ProjectUserAddedNotificationService $projectUserAddedNotification,
        private readonly ProjectAtRiskNotificationService $projectAtRiskNotification,
    ) {}

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
        $oldRiskScore = $project->risk_score;
        
        $project->update($validated);

        // Check if project became at risk
        if ($this->shouldNotifyAtRisk($project, $oldRiskScore)) {
            $this->projectAtRiskNotification->send($project);
        }

        return $project->fresh('user');
    }

    public function attachUsers(Project $project, array $userIds): void
    {
        $existingUserIds = $project->users()->pluck('users.id')->toArray();
        $newUserIds = array_diff($userIds, $existingUserIds);

        if (empty($newUserIds)) {
            return;
        }

        $project->users()->attach($newUserIds);

        // Send notifications to newly added users
        $newUsers = User::whereIn('id', $newUserIds)->get();
        $this->projectUserAddedNotification->send($project, ['users' => $newUsers->all()]);
    }

    public function syncUsers(Project $project, array $userIds): void
    {
        $existingUserIds = $project->users()->pluck('users.id')->toArray();
        $newUserIds = array_diff($userIds, $existingUserIds);

        $project->users()->sync($userIds);

        if (!empty($newUserIds)) {
            // Send notifications to newly added users
            $newUsers = User::whereIn('id', $newUserIds)->get();
            $this->projectUserAddedNotification->send($project, ['users' => $newUsers->all()]);
        }
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

    private function shouldNotifyAtRisk(Project $project, ?float $oldRiskScore): bool
    {
        // Notify if risk score is now > 70 and was previously <= 70
        return $project->risk_score > 70 && ($oldRiskScore === null || $oldRiskScore <= 70);
    }
}

