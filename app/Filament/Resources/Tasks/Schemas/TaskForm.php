<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre de la tâche')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Le titre de la tâche est obligatoire.',
                        'max' => 'Le titre ne peut pas dépasser 255 caractères.',
                    ]),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->required()
                    ->validationMessages([
                        'required' => 'La description est obligatoire.',
                    ]),

                Select::make('project_id')
                    ->label('Projet')
                    ->relationship('project', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('users', []);
                    })
                    ->validationMessages([
                        'required' => 'Le projet est obligatoire.',
                    ]),

                Select::make('users')
                    ->label('Utilisateurs assignés')
                    ->multiple()
                    ->options(fn ($get) => self::getUsersForProject($get('project_id')))
                    ->preload()
                    ->searchable()
                    ->helperText('Sélectionnez les utilisateurs assignés à cette tâche (uniquement les utilisateurs du projet)'),

                Select::make('priority')
                    ->label('Priorité')
                    ->options(TaskPriority::labels())
                    ->required()
                    ->default('medium')
                    ->validationMessages([
                        'required' => 'La priorité est obligatoire.',
                    ]),

                Select::make('status')
                    ->label('Statut')
                    ->options(TaskStatus::labels())
                    ->required()
                    ->default(TaskStatus::TODO->value)
                    ->validationMessages([
                        'required' => 'Le statut est obligatoire.',
                    ]),

                DatePicker::make('start_date')
                    ->label('Date de début')
                    ->displayFormat('d/m/Y')
                    ->nullable(),

                DatePicker::make('due_date')
                    ->label('Date d\'échéance')
                    ->displayFormat('d/m/Y')
                    ->nullable(),

            ]);
    }

    private static function getUsersForProject(?int $projectId): array
    {
        if (! $projectId) {
            return [];
        }

        return User::query()
            ->whereHas('assignedProjects', function ($query) use ($projectId) {
                $query->where('projects.id', $projectId);
            })
            ->select('users.id', 'users.name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
