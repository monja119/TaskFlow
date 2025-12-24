<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle tâche assignée : ' . $this->task->title)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Une nouvelle tâche vous a été assignée.')
            ->line('**Titre** : ' . $this->task->title)
            ->line('**Priorité** : ' . $this->task->priority->getLabel())
            ->line('**Projet** : ' . $this->task->project->name)
            ->when($this->task->due_date, function ($mail) {
                return $mail->line('**Échéance** : ' . $this->task->due_date->format('d/m/Y'));
            })
            ->action('Voir la tâche', url('/admin/tasks/' . $this->task->id))
            ->line('Merci d\'utiliser TaskFlow !');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'priority' => $this->task->priority->value,
            'project_name' => $this->task->project->name,
            'due_date' => $this->task->due_date?->format('Y-m-d'),
        ];
    }
}
