<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case MEMBER = 'member';

    public static function labels(): array
    {
        return [
            self::ADMIN->value => 'Admin',
            self::MANAGER->value => 'Manager',
            self::MEMBER->value => 'Membre',
        ];
    }
}
