<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                    ->searchable()
                    ->limit(50),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => TaskStatus::labels()[$state->value] ?? $state->value)
                    ->colors([
                        'danger' => TaskStatus::TODO->value,
                        'warning' => TaskStatus::IN_PROGRESS->value,
                        'success' => TaskStatus::COMPLETED->value,
                        'gray' => TaskStatus::BLOCKED->value,
                    ]),

                BadgeColumn::make('priority')
                    ->label('Priorité')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => TaskPriority::labels()[$state->value] ?? $state->value)
                    ->colors([
                        'danger' => TaskPriority::HIGH->value,
                        'warning' => TaskPriority::MEDIUM->value,
                        'success' => TaskPriority::LOW->value,
                    ]),

                TextColumn::make('due_date')
                    ->label('Date d\'échéance')
                    ->date()
                    ->sortable(),

                TextColumn::make('project.name')
                    ->label('Projet')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('users.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => collect($state)->implode(', ') ?: 'Non assigné'),
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
