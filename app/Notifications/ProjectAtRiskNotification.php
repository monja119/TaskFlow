<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAtRiskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Project $project
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('⚠️ Projet à risque : '.$this->project->name)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Le projet "'.$this->project->name.'" nécessite votre attention.')
            ->line('**Score de risque** : '.$this->project->risk_score.'/100')
            ->line('**Statut** : '.$this->project->status->getLabel())
            ->line('**Progression** : '.$this->project->progress.'%')
            ->when($this->project->end_date, function ($mail) {
                return $mail->line('**Date de fin prévue** : '.$this->project->end_date->format('d/m/Y'));
            })
            ->action('Voir le projet', url('/admin/projects/'.$this->project->id))
            ->line('Prenez les mesures nécessaires pour réduire les risques.');
    }

    public function toArray($notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'risk_score' => $this->project->risk_score,
            'status' => $this->project->status->value,
            'progress' => $this->project->progress,
            'end_date' => $this->project->end_date?->format('Y-m-d'),
        ];
    }
}
