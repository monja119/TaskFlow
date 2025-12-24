<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->placeholder('-'),
                    
                TextEntry::make('title')
                    ->label('Titre de la tâche')
                    ->placeholder('-'),

                TextEntry::make('description')
                    ->label('Description')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('status')
                    ->label('Statut')
                    ->placeholder('-'),

                TextEntry::make('priority')
                    ->label('Priorité')
                    ->placeholder('-'),

                TextEntry::make('start_date')
                    ->label('Date de début')
                    ->date()
                    ->placeholder('-'),

                TextEntry::make('user.name')
                    ->label('Utilisateur')
                    ->placeholder('-'),

                TextEntry::make('project.name')
                    ->label('Projet')
                    ->placeholder('-'),
            ]);
    }
}
 