<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    private ?array $usersToSync = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Charger les IDs des utilisateurs existants
        $data['users'] = $this->record->users()->pluck('users.id')->toArray();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Stocker les utilisateurs pour traitement après la sauvegarde
        $this->usersToSync = $data['users'] ?? [];
        unset($data['users']);

        return $data;
    }

    protected function afterSave(): void
    {
        // Récupérer les utilisateurs actuels
        $currentUserIds = $this->record->users()->pluck('users.id')->toArray();
        $newUserIds = array_diff($this->usersToSync, $currentUserIds);

        // Synchroniser les utilisateurs
        $this->record->users()->sync($this->usersToSync);

        // Envoyer les notifications uniquement aux nouveaux utilisateurs assignés
        if (! empty($newUserIds)) {
            $newUsers = \App\Models\User::whereIn('id', $newUserIds)->get();
            $notificationService = app(\App\Services\Notification\TaskAssignedNotificationService::class);
            $notificationService->send($this->record, ['newUsers' => $newUsers->all()]);
        }
    }
}
