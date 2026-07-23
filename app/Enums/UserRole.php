<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Ingenieur = 'ingenieur';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
