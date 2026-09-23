<?php

namespace App\Enums;

enum OperationalStatus: string
{
    case DapatDigunakan = 'dapat_digunakan';
    case DalamPerbaikan = 'dalam_perbaikan';

    public function label(): string
    {
        return match ($this) {
            self::DapatDigunakan => 'Dapat digunakan',
            self::DalamPerbaikan => 'Dalam perbaikan',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::DapatDigunakan => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
            self::DalamPerbaikan => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-600/20',
        };
    }
}
