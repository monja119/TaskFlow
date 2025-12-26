<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTasksWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Tâches à venir (7 prochains jours)';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(
                Task::query()
                    ->with(['project', 'user'])
                    ->whereNotNull('due_date')
                    ->where('status', '!=', TaskStatus::COMPLETED)
                    ->whereBetween('due_date', [now(), now()->addDays(7)])
                    ->when($user && $user->isMember(), function (Builder $query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                                ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                        });
                    })
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Projet')
                    ->sortable()
                    ->limit(30)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priorité')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (Task $record) => $record->due_date->isPast() ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->placeholder('Non assigné')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('due_date', 'asc');
    }
}
