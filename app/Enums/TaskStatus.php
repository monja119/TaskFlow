<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case BLOCKED = 'blocked';

    public static function labels(): array
    {
        return [
            self::TODO->value => 'En attente',
            self::IN_PROGRESS->value => 'En cours',
            self::COMPLETED->value => 'Terminé',
            self::BLOCKED->value => 'Bloqué',
        ];
    }
}
