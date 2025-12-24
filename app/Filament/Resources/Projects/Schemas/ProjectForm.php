<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
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
                    ->options(ProjectStatus::labels())
                    ->required()
                    ->default('pending'),

                TextInput::make('progress')
                    ->label('Progression (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0),

                TextInput::make('risk_score')
                    ->label('Score de risque')
                    ->numeric()
                    ->step(0.1)
                    ->minValue(0)
                    ->maxValue(100)
                    ->nullable(),

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