<?php

namespace App\Enums;

enum TaskPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function labels(): array
    {
        return [
            self::LOW->value => 'Basse',
            self::MEDIUM->value => 'Moyenne',
            self::HIGH->value => 'Haute',
        ];
    }
}
