<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        TextEntry::make('title')
                            ->label('Titre de la tâche')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('project.name')
                            ->label('Projet')
                            ->placeholder('-'),
                    ]),

                Section::make('Statut et priorité')
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        TextEntry::make('status')
                            ->label('Statut')
                            ->placeholder('-')
                            ->formatStateUsing(fn ($state) => \App\Enums\TaskStatus::labels()[$state->value] ?? $state->value)
                            ->badge()
                            ->color(fn ($state) => match ($state->value) {
                                'todo' => 'danger',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'blocked' => 'gray',
                                default => 'gray',
                            }),

                        TextEntry::make('priority')
                            ->label('Priorité')
                            ->placeholder('-')
                            ->formatStateUsing(fn ($state) => \App\Enums\TaskPriority::labels()[$state->value] ?? $state->value)
                            ->badge()
                            ->color(fn ($state) => match ($state->value) {
                                'high' => 'danger',
                                'medium' => 'warning',
                                'low' => 'success',
                                default => 'gray',
                            }),
                    ]),

                Section::make('Planification')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        TextEntry::make('start_date')
                            ->label('Date de début')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('due_date')
                            ->label('Date d\'échéance')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('completed_at')
                            ->label('Complétée le')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('actual_minutes')
                            ->label('Temps réel (minutes)')
                            ->placeholder('-')
                            ->suffix(' min'),
                    ]),

                Section::make('Équipe')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->components([
                        RepeatableEntry::make('users')
                            ->label('Utilisateurs assignés')
                            ->columns(3)
                            ->components([
                                TextEntry::make('name')
                                    ->label('Nom')
                                    ->placeholder('-'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('-'),

                                TextEntry::make('role')
                                    ->label('Rôle')
                                    ->placeholder('-')
                                    ->badge(),
                            ]),
                    ]),

                Section::make('Métadonnées')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        TextEntry::make('created_at')
                            ->label('Créé le')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Mis à jour le')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
