<?php

namespace App\Enums;

enum UserCategory: string
{
    case Mahasiswa = 'mahasiswa';
    case Dosen = 'dosen';
    case Staf = 'staf';

    public function label(): string
    {
        return match ($this) {
            self::Mahasiswa => 'Mahasiswa',
            self::Dosen => 'Dosen',
            self::Staf => 'Staf',
        };
    }
}
