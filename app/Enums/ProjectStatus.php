<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case BLOCKED = 'blocked';

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'En attente',
            self::IN_PROGRESS->value => 'En cours',
            self::COMPLETED->value => 'Terminé',
            self::BLOCKED->value => 'Bloqué',
        ];
    }

    public function getLabel(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }
}
