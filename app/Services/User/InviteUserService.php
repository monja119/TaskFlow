<?php

namespace App\Services\User;

use App\Models\Project;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\URL;

class InviteUserService
{
    public function sendInvitation(User $user, ?string $url = null, ?Project $project = null): void
    {
        $invitationUrl = $url ?? URL::to('/admin');
        $user->notify(new UserInvitationNotification($invitationUrl, $project));
    }
}
