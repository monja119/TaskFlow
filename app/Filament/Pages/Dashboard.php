<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Tableau de bord';
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\ProjectsChartWidget::class,
            \App\Filament\Widgets\UpcomingTasksWidget::class,
        ];
    }
    
    public function getColumns(): int | array
    {
        return 2;
    }
}
