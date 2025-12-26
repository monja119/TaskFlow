<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\UserInvitationNotification;
use App\Services\User\InviteUserService;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InviteUserServiceTest extends TestCase
{
    public function test_sends_invitation_notification(): void
    {
        Notification::fake();

        $user = User::factory()->make(['email' => 'invite@example.com']);

        $service = new InviteUserService();
        $service->sendInvitation($user, 'https://example.com');

        Notification::assertSentTo($user, UserInvitationNotification::class, function ($notification, $channels) {
            return in_array('mail', $channels, true);
        });
    }
}
