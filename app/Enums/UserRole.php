<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Member = 'member';

    public static function labels(): array
    {
        return [
            self::Admin->value => 'Admin',
            self::Manager->value => 'Manager',
            self::Member->value => 'Membre',
        ];
    }
}
