<?php

namespace Tests\Unit\Services\Notification;

use App\Models\Task;
use App\Models\User;
use App\Services\Notification\TaskAssignedNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskAssignedNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_notification_to_assigned_users(): void
    {
        Notification::fake();

        $task = Task::factory()->create();
        $users = User::factory()->count(2)->create();

        $service = new TaskAssignedNotificationService();
        $service->send($task, ['newUsers' => $users->all()]);

        Notification::assertSentTo(
            $users,
            \App\Notifications\TaskAssignedNotification::class
        );
    }

    public function test_does_not_send_notification_when_no_users(): void
    {
        Notification::fake();

        $task = Task::factory()->create();

        $service = new TaskAssignedNotificationService();
        $service->send($task, ['newUsers' => []]);

        Notification::assertNothingSent();
    }
}
