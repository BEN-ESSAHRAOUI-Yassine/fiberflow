<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Audited = 'audited';
    case Validated = 'validated';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public function order(): int
    {
        return match ($this) {
            self::Draft => 1,
            self::InProgress => 2,
            self::Audited => 3,
            self::Validated => 4,
            self::Archived => 5,
        };
    }
}
