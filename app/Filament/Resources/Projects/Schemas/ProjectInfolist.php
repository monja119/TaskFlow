<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
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
                    
                TextEntry::make('name')
                    ->label('Nom du projet')
                    ->placeholder('-'),

                TextEntry::make('description')
                    ->label('Description')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('status')
                    ->label('Statut')
                    ->placeholder('-'),

                TextEntry::make('start_date')
                    ->label('Date de début')
                    ->date()
                    ->placeholder('-'),

                TextEntry::make('end_date')
                    ->label('Date de fin')
                    ->date()
                    ->placeholder('-'),

                TextEntry::make('user.name')
                    ->label('Utilisateur')
                    ->placeholder('-'),
                    
            ]);
    }
}
