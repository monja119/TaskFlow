<?php

namespace App\Filament\Widgets;

use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $user = auth()->user();
        
        // Total projects
        $totalProjects = Project::query()
            ->when($user && $user->isMember(), fn ($q) => $q->where('user_id', $user->id))
            ->count();
            
        // Active projects
        $activeProjects = Project::query()
            ->where('status', ProjectStatus::IN_PROGRESS)
            ->when($user && $user->isMember(), fn ($q) => $q->where('user_id', $user->id))
            ->count();
            
        // Total tasks
        $totalTasks = Task::query()
            ->when($user && $user->isMember(), function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                });
            })
            ->count();
            
        // Pending tasks
        $pendingTasks = Task::query()
            ->where('status', TaskStatus::TODO)
            ->when($user && $user->isMember(), function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                });
            })
            ->count();
            
        // Completed tasks
        $completedTasks = Task::query()
            ->where('status', TaskStatus::COMPLETED)
            ->when($user && $user->isMember(), function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                });
            })
            ->count();
            
        // Tasks at risk (overdue)
        $overdueTasks = Task::query()
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->when($user && $user->isMember(), function ($q) use ($user) {
                $q->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('project', fn ($sub) => $sub->where('user_id', $user->id));
                });
            })
            ->count();
            
        // Average project progress
        $avgProgress = Project::query()
            ->when($user && $user->isMember(), fn ($q) => $q->where('user_id', $user->id))
            ->avg('progress') ?? 0;

        return [
            Stat::make('Projets totaux', $totalProjects)
                ->description($activeProjects . ' projets actifs')
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('success')
                ->chart([7, 12, 15, 18, 20, 22, $totalProjects]),
                
            Stat::make('Tâches totales', $totalTasks)
                ->description($completedTasks . ' terminées / ' . $pendingTasks . ' en attente')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary')
                ->chart([30, 45, 60, 75, 85, 92, $totalTasks]),
                
            Stat::make('Tâches en retard', $overdueTasks)
                ->description($overdueTasks > 0 ? 'Attention requise' : 'Aucun retard')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($overdueTasks > 0 ? 'danger' : 'success')
                ->chart([$overdueTasks, $overdueTasks + 2, $overdueTasks + 1, $overdueTasks]),
                
            Stat::make('Progression moyenne', round($avgProgress, 1) . '%')
                ->description('Tous les projets')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgProgress >= 75 ? 'success' : ($avgProgress >= 50 ? 'warning' : 'danger'))
                ->chart([20, 35, 48, 60, 68, round($avgProgress)]),
        ];
    }
}
