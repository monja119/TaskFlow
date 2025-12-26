<?php

namespace App\Services\Notification;

use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Database\Eloquent\Model;

class TaskDueSoonNotificationService implements NotificationServiceInterface
{
    /**
     * Send notification to users about tasks due soon
     *
     * @param Task $subject
     */
    public function send(Model $subject, array $context = []): void
    {
        if (!$subject instanceof Task) {
            throw new \InvalidArgumentException('Subject must be an instance of Task');
        }

        // Notify all users assigned to the task
        foreach ($subject->users as $user) {
            $user->notify(new TaskDueSoonNotification($subject));
        }
    }
}
