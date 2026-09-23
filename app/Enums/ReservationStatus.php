<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case Berlangsung = 'berlangsung';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case DibatalkanOlehPengguna = 'dibatalkan_oleh_pengguna';
    case DibatalkanOlehPetugas = 'dibatalkan_oleh_petugas';
    case Kedaluwarsa = 'kedaluwarsa';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu Persetujuan',
            self::Disetujui => 'Disetujui',
            self::Berlangsung => 'Sedang Berlangsung',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::DibatalkanOlehPengguna => 'Dibatalkan oleh Pengguna',
            self::DibatalkanOlehPetugas => 'Dibatalkan oleh Petugas',
            self::Kedaluwarsa => 'Kedaluwarsa',
        };
    }

    /**
     * Menentukan apakah status ini mengunci jadwal ketersediaan fasilitas (BR-PG-05).
     */
    public function locksSchedule(): bool
    {
        return match ($this) {
            self::Disetujui, self::Berlangsung => true,
            default => false,
        };
    }
}
