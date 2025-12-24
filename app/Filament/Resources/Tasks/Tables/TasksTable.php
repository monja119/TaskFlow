<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

class TasksTable
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

                TextColumn::make('title')
                    ->label('Titre de la tâche')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->searchable(),

                TextColumn::make('priority')
                    ->label('Priorité')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Date d\'échéance')
                    ->date()
                    ->sortable(),

                TextColumn::make('project.name')
                    ->label('Projet')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TaskStatus::labels()),
                SelectFilter::make('priority')
                    ->options(TaskPriority::labels()),
                Filter::make('overdue')
                    ->label('En retard')
                    ->query(fn ($query) => $query->overdue()),
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
