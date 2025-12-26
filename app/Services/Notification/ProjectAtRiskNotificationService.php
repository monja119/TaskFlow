<?php

namespace App\Services\Notification;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAtRiskNotification;
use Illuminate\Database\Eloquent\Model;

class ProjectAtRiskNotificationService implements NotificationServiceInterface
{
    /**
     * Send notification to admins and project users about at-risk projects
     *
     * @param  Project  $subject
     */
    public function send(Model $subject, array $context = []): void
    {
        if (! $subject instanceof Project) {
            throw new \InvalidArgumentException('Subject must be an instance of Project');
        }

        // Get all admins
        $admins = User::where('role', UserRole::ADMIN)->get();

        // Get all users assigned to the project
        $projectUsers = $subject->users;

        // Merge and get unique users
        $recipients = $admins->merge($projectUsers)->unique('id');

        foreach ($recipients as $user) {
            $user->notify(new ProjectAtRiskNotification($subject));
        }
    }
}
