<?php

namespace App\Services\Notification;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Database\Eloquent\Model;

class TaskAssignedNotificationService implements NotificationServiceInterface
{
    /**
     * Send notification to users assigned to a task
     *
     * @param  Task  $subject
     * @param  array{newUsers: array<int, User>}  $context
     */
    public function send(Model $subject, array $context = []): void
    {
        if (! $subject instanceof Task) {
            throw new \InvalidArgumentException('Subject must be an instance of Task');
        }

        $newUsers = $context['newUsers'] ?? [];

        if (empty($newUsers)) {
            return;
        }

        foreach ($newUsers as $user) {
            if ($user instanceof User) {
                $user->notify(new TaskAssignedNotification($subject));
            }
        }
    }
}
