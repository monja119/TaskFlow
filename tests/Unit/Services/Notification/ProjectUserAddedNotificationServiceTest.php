<?php

namespace Tests\Unit\Services\Notification;

use App\Models\Project;
use App\Models\User;
use App\Services\Notification\ProjectUserAddedNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProjectUserAddedNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_notification_to_new_users(): void
    {
        Notification::fake();

        $project = Project::factory()->create();
        $users = User::factory()->count(2)->create();

        $service = new ProjectUserAddedNotificationService;
        $service->send($project, ['users' => $users->all()]);

        Notification::assertSentTo(
            $users,
            \App\Notifications\UserInvitationNotification::class
        );
    }

    public function test_does_not_send_notification_when_no_users(): void
    {
        Notification::fake();

        $project = Project::factory()->create();

        $service = new ProjectUserAddedNotificationService;
        $service->send($project, ['users' => []]);

        Notification::assertNothingSent();
    }

    public function test_throws_exception_for_invalid_subject(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = User::factory()->create();
        $service = new ProjectUserAddedNotificationService;
        $service->send($user, ['users' => []]);
    }
}
