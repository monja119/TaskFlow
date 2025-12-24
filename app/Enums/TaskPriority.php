<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public static function labels(): array
    {
        return [
            self::Low->value => 'Basse',
            self::Medium->value => 'Moyenne',
            self::High->value => 'Haute',
        ];
    }
}
