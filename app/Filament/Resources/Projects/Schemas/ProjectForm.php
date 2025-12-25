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
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Le nom du projet est obligatoire.',
                        'max' => 'Le nom du projet ne peut pas dépasser 255 caractères.',
                    ]),
                    
                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->required()
                    ->validationMessages([
                        'required' => 'La description est obligatoire.',
                    ]),
                    
                # select status
                Select::make('status')
                    ->label('Statut')
                    ->options(ProjectStatus::labels())
                    ->required()
                    ->default('pending')
                    ->validationMessages([
                        'required' => 'Le statut est obligatoire.',
                    ]),

                TextInput::make('progress')
                    ->label('Progression (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->validationMessages([
                        'numeric' => 'La progression doit être un nombre.',
                        'min' => 'La progression doit être au moins 0.',
                        'max' => 'La progression ne peut pas dépasser 100.',
                    ]),

                TextInput::make('risk_score')
                    ->label('Score de risque')
                    ->numeric()
                    ->step(0.1)
                    ->minValue(0)
                    ->maxValue(100)
                    ->nullable()
                    ->validationMessages([
                        'numeric' => 'Le score de risque doit être un nombre.',
                        'min' => 'Le score de risque doit être au moins 0.',
                        'max' => 'Le score de risque ne peut pas dépasser 100.',
                    ]),

                # user id to "Utilisateur"
                Select::make('user_id')
                    ->label('Utilisateur')
                    ->relationship('user', 'name')
                    ->required()
                    ->validationMessages([
                        'required' => 'L\'utilisateur est obligatoire.',
                    ]),   

                # start and end date  format
                DatePicker::make('start_date')
                    ->label('Date de début')
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->validationMessages([
                        'required' => 'La date de début est obligatoire.',
                    ]),
                DatePicker::make('end_date')
                    ->label('Date de fin')
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->validationMessages([
                        'required' => 'La date de fin est obligatoire.',
                    ]),
            ]);
    }
}