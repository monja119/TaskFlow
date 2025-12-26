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

                # Utilisateurs assignés au projet
                Select::make('users')
                    ->label('Utilisateurs assignés')
                    ->multiple()
                    ->options(fn () => \App\Models\User::pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->helperText('Sélectionnez les utilisateurs à ajouter à ce projet'),   

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