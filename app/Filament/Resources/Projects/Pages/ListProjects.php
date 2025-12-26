<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        if ($user) {
            // Afficher les projets créés par l'utilisateur ET les projets auxquels il est assigné
            return $query->where('user_id', $user->id)
                ->orWhereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
        }

        return $query;
    }
}
