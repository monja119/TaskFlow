<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $invitationUrl,
        private readonly ?Project $project = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Bonjour '.($notifiable->name ?? ''));

        if ($this->project) {
            $message->line('Vous avez été ajouté au projet "'.$this->project->name.'".')
                ->line('**Description** : '.($this->project->description ?? 'Aucune description'))
                ->line('**Statut** : '.$this->project->status->getLabel());
        } else {
            $message->line('Vous avez été invité à rejoindre la plateforme TaskFlow.');
        }

        return $message
            ->action($this->getActionText(), $this->invitationUrl)
            ->line('Si vous n\'attendiez pas ce message, vous pouvez l\'ignorer.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project?->id,
            'project_name' => $this->project?->name,
            'invitation_url' => $this->invitationUrl,
        ];
    }

    private function getSubject(): string
    {
        return $this->project
            ? 'Vous avez été ajouté au projet : '.$this->project->name
            : 'Invitation à rejoindre la plateforme';
    }

    private function getActionText(): string
    {
        return $this->project
            ? 'Voir le projet'
            : 'Accéder à la plateforme';
    }
}
