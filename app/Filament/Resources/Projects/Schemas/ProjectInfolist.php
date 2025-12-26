<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Services\ProjectStatusFormatter;

class ProjectInfolist
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
                        TextEntry::make('name')
                            ->label('Nom du projet')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Statut et progression')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(2)
                    ->collapsible()
                    ->components([
                        TextEntry::make('status')
                            ->label('Statut')
                            ->formatStateUsing(fn ($state) => ProjectStatusFormatter::format($state))
                            ->placeholder('-')
                            ->badge()
                            ->color(fn ($state) => match (ProjectStatusFormatter::format($state)) {
                                'En attente' => 'warning',
                                'En cours' => 'info',
                                'Terminé' => 'success',
                                'Bloqué' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('progress')
                            ->label('Progression (%)')
                            ->placeholder('-')
                            ->suffix('%'),

                        TextEntry::make('risk_score')
                            ->label('Score de risque')
                            ->placeholder('-'),
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

                        TextEntry::make('end_date')
                            ->label('Date de fin')
                            ->date()
                            ->placeholder('-'),
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
