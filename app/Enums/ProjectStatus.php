<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Blocked = 'blocked';

    public static function labels(): array
    {
        return [
            self::Pending->value => 'En attente',
            self::InProgress->value => 'En cours',
            self::Completed->value => 'Terminé',
            self::Blocked->value => 'Bloqué',
        ];
    }
}
