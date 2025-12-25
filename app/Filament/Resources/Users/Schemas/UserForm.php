<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Le nom est obligatoire.',
                        'max' => 'Le nom ne peut pas dépasser 255 caractères.',
                    ]),
                TextInput::make('email')
                    ->label('Adresse email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(table: 'users', column: 'email', ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'L\'adresse email est obligatoire.',
                        'email' => 'L\'adresse email doit être valide.',
                        'unique' => 'Cette adresse email est déjà utilisée.',
                        'max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
                    ]),
                DateTimePicker::make('email_verified_at')
                    ->label('Email vérifié le'),
                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->validationMessages([
                        'required' => 'Le mot de passe est obligatoire.',
                        'min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                    ]),
            ]);
    }
}
