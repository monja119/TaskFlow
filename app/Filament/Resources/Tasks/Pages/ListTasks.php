<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

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

        if ($user && ! $user->isAdmin() && ! $user->isManager()) {
            // Les membres ne voient que les tâches des projets auxquels ils sont assignés
            $query->whereHas('project', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('users', function ($sub) use ($user) {
                        $sub->where('user_id', $user->id);
                    });
            });
        }

        return $query;
    }
}
