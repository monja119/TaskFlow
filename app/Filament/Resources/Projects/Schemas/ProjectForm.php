<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom du projet')
                    ->required(),
                    
                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->required(),
                    
                # select status
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'in_progress' => 'En cours',
                        'completed' => 'Terminé',
                    ])
                    ->required()
                    ->default('pending'),

                # user id to "Utilisateur"
                Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->required(),   

                # start and end date  format
                DatePicker::make('start_date')
                    ->label('Date de début')
                    ->displayFormat('d/m/Y')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Date de fin')
                    ->displayFormat('d/m/Y')
                    ->required(),
            ]);
    }
}