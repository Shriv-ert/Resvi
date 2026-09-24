<?php

namespace App\Enums;

enum ReportCategory: string
{
    case KerusakanFisik = 'kerusakan_fisik';
    case KerusakanElektronik = 'kerusakan_elektronik';
    case Kebersihan = 'kebersihan';
    case Keamanan = 'keamanan';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::KerusakanFisik => 'Kerusakan Fisik (Pintu, Jendela, Kursi, Meja, Tembok)',
            self::KerusakanElektronik => 'Kerusakan Elektronik (AC, Proyektor, Lampu, PC, Stopkontak)',
            self::Kebersihan => 'Kebersihan Ruangan & Sanitasi',
            self::Keamanan => 'Keamanan / Kehilangan Fasilitas',
            self::Lainnya => 'Lainnya',
        };
    }
}
