<?php

namespace App\Enums;

enum FacilityType: string
{
    case Kelas = 'kelas';
    case Aula = 'aula';
    case Laboratorium = 'laboratorium';
    case Alat = 'alat';
    case Lapangan = 'lapangan';

    public function label(): string
    {
        return match ($this) {
            self::Kelas => 'Ruang Kelas',
            self::Aula => 'Aula',
            self::Laboratorium => 'Laboratorium',
            self::Alat => 'Alat',
            self::Lapangan => 'Lapangan',
        };
    }
}
