<?php

namespace App\Services\Notification;

use App\Models\Project;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class ProjectUserAddedNotificationService implements NotificationServiceInterface
{
    /**
     * Send notification to users added to a project
     *
     * @param Project $subject
     * @param array{users: array<int, User>} $context
     */
    public function send(Model $subject, array $context = []): void
    {
        if (!$subject instanceof Project) {
            throw new \InvalidArgumentException('Subject must be an instance of Project');
        }

        $users = $context['users'] ?? [];

        if (empty($users)) {
            return;
        }

        $projectUrl = URL::to('/admin/projects/' . $subject->id);

        foreach ($users as $user) {
            if ($user instanceof User) {
                $user->notify(new UserInvitationNotification($projectUrl, $subject));
            }
        }
    }
}
