<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;

final readonly class GuestReservationDraftService
{
    public const string SESSION_KEY = 'guest_reservation_draft';

    public function __construct(
        private Session $session
    ) {}

    /**
     * Menyimpan draft jadwal sementara pengunjung ke dalam session.
     */
    public function storeDraft(string $facilityId, string $date, string $startTime, string $endTime): void
    {
        $this->session->put(self::SESSION_KEY, [
            'facility_id' => $facilityId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    /**
     * Mengambil draft jadwal sementara dari session jika ada.
     *
     * @return array{facility_id: string, date: string, start_time: string, end_time: string}|null
     */
    public function getDraft(): ?array
    {
        return $this->session->get(self::SESSION_KEY);
    }

    /**
     * Memeriksa apakah session memiliki draft reservasi aktif.
     */
    public function hasDraft(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    /**
     * Menghapus draft jadwal dari session.
     */
    public function clearDraft(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }
}
