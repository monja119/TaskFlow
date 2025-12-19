<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Titre de la tâche')
                    ->required(),   

                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->required(),
                    
                Select::make('project_id')
                    ->label('Projet')
                    ->relationship('project', 'name')
                    ->required(),
                
                Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->required(),

                Select::make('priority')
                    ->label('Priorité')
                    ->options([
                        'low' => 'Basse',
                        'medium' => 'Moyenne',
                        'high' => 'Haute',
                    ])
                    ->required()
                    ->default('medium'),
                
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminé',
                    ])
                    ->required()
                    ->default('pending'),
                
                DatePicker::make('due_date')
                    ->label('Date d\'échéance')
                    ->displayFormat('d/m/Y')
                    ->nullable(),

            ]);
    }
}
