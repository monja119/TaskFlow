<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Définir automatiquement l'utilisateur créateur
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): \App\Models\Project
    {
        // Extraire les utilisateurs avant la création
        $users = $data['users'] ?? [];
        unset($data['users']);

        // Créer le projet via le modèle directement
        $project = $this->getModel()::create($data);

        // Attacher les utilisateurs via le service (envoie les notifications)
        if (!empty($users)) {
            $projectService = app(ProjectService::class);
            $projectService->attachUsers($project, $users);
        }

        return $project;
    }
}
