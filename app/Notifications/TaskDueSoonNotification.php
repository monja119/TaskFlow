<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification implements ShouldQueue
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
            ->subject('Tâche à échéance proche : '.$this->task->title)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('La tâche "'.$this->task->title.'" arrive à échéance bientôt.')
            ->line('**Date d\'échéance** : '.$this->task->due_date->format('d/m/Y'))
            ->line('**Priorité** : '.$this->task->priority->getLabel())
            ->line('**Projet** : '.$this->task->project->name)
            ->action('Voir la tâche', url('/admin/tasks/'.$this->task->id))
            ->line('Merci d\'utiliser TaskFlow !');
    }

    public function toArray($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_date' => $this->task->due_date,
            'priority' => $this->task->priority->value,
            'project_name' => $this->task->project->name,
        ];
    }
}
