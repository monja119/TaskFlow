<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;
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
            ViewAction::make(),
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
        // Synchroniser les utilisateurs via le service (envoie les notifications)
        if (!empty($this->usersToSync)) {
            $projectService = app(ProjectService::class);
            $projectService->syncUsers($this->record, $this->usersToSync);
        }
    }
}
