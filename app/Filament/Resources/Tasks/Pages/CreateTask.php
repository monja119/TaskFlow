<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    private ?array $usersToAttach = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Stocker les utilisateurs pour traitement après la création
        $this->usersToAttach = $data['users'] ?? [];
        unset($data['users']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attacher les utilisateurs à la tâche créée
        if (!empty($this->usersToAttach)) {
            $this->record->users()->attach($this->usersToAttach);
            
            // Envoyer les notifications
            $assignedUsers = \App\Models\User::whereIn('id', $this->usersToAttach)->get();
            $notificationService = app(\App\Services\Notification\TaskAssignedNotificationService::class);
            $notificationService->send($this->record, ['newUsers' => $assignedUsers->all()]);
        }
    }
}
