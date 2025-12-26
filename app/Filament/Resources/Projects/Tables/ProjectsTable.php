<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Enums\ProjectStatus;
use App\Services\ProjectStatusFormatter;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('name')
                    ->label('Nom du projet')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state) => ProjectStatusFormatter::format($state))
                    ->searchable(),

                TextColumn::make('progress')
                    ->label('Progression')
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('risk_score')
                    ->label('Risque')
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Date de début')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Date de fin')
                    ->date()
                    ->sortable(),
                    
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::labels()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
