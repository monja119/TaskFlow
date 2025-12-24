<?php

namespace App\Filament\Widgets;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectsChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): ?string
    {
        return 'Répartition des projets par statut';
    }

    protected function getData(): array
    {
        $user = auth()->user();
        
        $statuses = [
            ProjectStatus::PENDING->value => 'En attente',
            ProjectStatus::IN_PROGRESS->value => 'En cours',
            ProjectStatus::COMPLETED->value => 'Terminé',
            ProjectStatus::BLOCKED->value => 'Bloqué',
        ];
        
        $data = [];
        $labels = [];
        
        foreach ($statuses as $value => $label) {
            $count = Project::query()
                ->where('status', $value)
                ->when($user && $user->isMember(), fn ($q) => $q->where('user_id', $user->id))
                ->count();
                
            $data[] = $count;
            $labels[] = $label;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Projets',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',  // Blue - En attente
                        'rgba(34, 197, 94, 0.5)',   // Green - En cours
                        'rgba(168, 85, 247, 0.5)',  // Purple - Terminé
                        'rgba(239, 68, 68, 0.5)',   // Red - Bloqué
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(168, 85, 247)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
