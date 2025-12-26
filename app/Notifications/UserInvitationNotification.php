<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $invitationUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation à rejoindre la plateforme')
            ->greeting('Bonjour ' . ($notifiable->name ?? ''))
            ->line('Vous avez été invité à rejoindre la plateforme TaskFlow.')
            ->action('Accéder à la plateforme', $this->invitationUrl)
            ->line('Si vous n’attendiez pas ce message, vous pouvez l’ignorer.');
    }
}
