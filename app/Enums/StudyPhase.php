<?php

namespace App\Enums;

enum StudyPhase: string
{
    case APS = 'APS';
    case APD = 'APD';
    case PRO = 'PRO';
    case EXE = 'EXE';
    case REC = 'REC';
    case FIN = 'FIN';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public function order(): int
    {
        return match ($this) {
            self::APS => 1,
            self::APD => 2,
            self::PRO => 3,
            self::EXE => 4,
            self::REC => 5,
            self::FIN => 6,
        };
    }
}
