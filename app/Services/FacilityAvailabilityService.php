<?php

namespace App\Services;

use App\DTOs\AvailabilityResultDTO;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\CarbonImmutable;

final readonly class FacilityAvailabilityService
{
    /**
     * Memeriksa ketersediaan slot waktu fasilitas pada tanggal tertentu secara server-side.
     * Mengisolasi data pemohon untuk menjamin perlindungan privasi (BR-PG-08 & Zero PII Leak).
     */
    public function check(
        Facility $facility,
        CarbonImmutable|string $date,
        string $startTime,
        string $endTime
    ): AvailabilityResultDTO {
        // 1. Validasi Status Operasional Fasilitas
        if ($facility->operational_status === OperationalStatus::DalamPerbaikan) {
            return new AvailabilityResultDTO(
                isAvailable: false,
                message: 'Fasilitas sedang dalam perbaikan dan tidak dapat direservasi untuk tanggal apa pun.',
                reason: 'dalam_perbaikan'
            );
        }

        if (! $facility->is_active) {
            return new AvailabilityResultDTO(
                isAvailable: false,
                message: 'Fasilitas tidak aktif atau dinonaktifkan oleh administrator.',
                reason: 'non_aktif'
            );
        }

        // Standardisasi format waktu ke H:i:s
        $formattedStartTime = strlen($startTime) === 5 ? "{$startTime}:00" : $startTime;
        $formattedEndTime = strlen($endTime) === 5 ? "{$endTime}:00" : $endTime;
        $dateString = is_string($date) ? $date : $date->toDateString();

        // 2. Deteksi Bentrokan Waktu Terhadap Status Terkunci (disetujui & berlangsung)
        // Interval bentrok: (start_lama < end_baru) AND (end_lama > start_baru)
        $lockedStatuses = [
            ReservationStatus::Disetujui->value,
            ReservationStatus::Berlangsung->value,
        ];

        $hasConflict = Reservation::query()
            ->where('facility_id', $facility->id)
            ->whereDate('date', $dateString)
            ->whereIn('status', $lockedStatuses)
            ->where('start_time', '<', $formattedEndTime)
            ->where('end_time', '>', $formattedStartTime)
            ->exists();

        if ($hasConflict) {
            return new AvailabilityResultDTO(
                isAvailable: false,
                message: 'Jadwal yang Anda pilih tidak tersedia karena telah terisi oleh kegiatan lain. Silakan pilih waktu atau tanggal lain.',
                reason: 'bentrok'
            );
        }

        return new AvailabilityResultDTO(
            isAvailable: true,
            message: 'Jadwal yang Anda pilih tersedia untuk digunakan.'
        );
    }
}
