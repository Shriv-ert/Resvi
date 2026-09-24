<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserReservationService
{
    public function __construct(
        protected FacilityAvailabilityService $availabilityService
    ) {}

    /**
     * Membuat reservasi baru oleh Pengguna dengan verifikasi ketersediaan dan duplikasi.
     */
    public function createReservation(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            /** @var Facility $facility */
            $facility = Facility::where('id', $data['facility_id'])->lockForUpdate()->firstOrFail();

            if (! $facility->is_active) {
                throw ValidationException::withMessages([
                    'facility_id' => ['Fasilitas yang dipilih tidak aktif.'],
                ]);
            }

            if ($facility->operational_status->value === 'dalam_perbaikan') {
                throw ValidationException::withMessages([
                    'facility_id' => ['Fasilitas sedang dalam perbaikan dan tidak dapat direservasi.'],
                ]);
            }

            // 1. Cek bentrokan jadwal server-side
            $availability = $this->availabilityService->check(
                facility: $facility,
                date: $data['date'],
                startTime: $data['start_time'],
                endTime: $data['end_time']
            );

            if (! $availability->isAvailable) {
                throw ValidationException::withMessages([
                    'date' => [$availability->message],
                ]);
            }

            // 2. Cek duplikasi reservasi Pengguna yang sama (BR-PGN-03)
            $formattedStart = strlen($data['start_time']) === 5 ? "{$data['start_time']}:00" : $data['start_time'];
            $formattedEnd = strlen($data['end_time']) === 5 ? "{$data['end_time']}:00" : $data['end_time'];

            $isDuplicate = Reservation::where('user_id', $user->id)
                ->where('facility_id', $facility->id)
                ->whereDate('date', $data['date'])
                ->where('start_time', $formattedStart)
                ->where('end_time', $formattedEnd)
                ->whereIn('status', [ReservationStatus::Menunggu->value, ReservationStatus::Disetujui->value])
                ->exists();

            if ($isDuplicate) {
                throw ValidationException::withMessages([
                    'purpose' => ['Anda telah memiliki pengajuan reservasi yang sama pada jadwal ini yang masih berstatus Menunggu atau Disetujui.'],
                ]);
            }

            // 3. Generate Nomor Reservasi Unik: RSV-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $countToday = Reservation::whereDate('created_at', today())->count();
            $uniqueCode = str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);
            $reservationNumber = "RSV-{$datePrefix}-{$uniqueCode}";

            // Safety loop for unique number
            while (Reservation::where('reservation_number', $reservationNumber)->exists()) {
                $uniqueCode = str_pad((string) (random_int(1000, 9999)), 4, '0', STR_PAD_LEFT);
                $reservationNumber = "RSV-{$datePrefix}-{$uniqueCode}";
            }

            // 4. Buat Reservation
            return Reservation::create([
                'reservation_number' => $reservationNumber,
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'date' => $data['date'],
                'start_time' => $formattedStart,
                'end_time' => $formattedEnd,
                'purpose' => $data['purpose'],
                'status' => ReservationStatus::Menunggu,
            ]);
        });
    }

    /**
     * Membatalkan reservasi oleh Pengguna (BR-PGN-06).
     */
    public function cancelReservation(User $user, Reservation $reservation, string $reason): Reservation
    {
        if ($reservation->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan reservasi ini.');
        }

        // Cek status yang boleh dibatalkan
        if (! in_array($reservation->status, [ReservationStatus::Menunggu, ReservationStatus::Disetujui], true)) {
            throw ValidationException::withMessages([
                'cancellation_reason' => ['Hanya reservasi berstatus Menunggu atau Disetujui yang dapat dibatalkan.'],
            ]);
        }

        // Cek waktu mulai belum tercapai
        $dateStr = $reservation->date instanceof \DateTimeInterface ? $reservation->date->format('Y-m-d') : (string) $reservation->date;
        $startDateTime = Carbon::parse($dateStr . ' ' . $reservation->start_time);

        if (now()->gte($startDateTime)) {
            throw ValidationException::withMessages([
                'cancellation_reason' => ['Reservasi tidak dapat dibatalkan karena waktu mulai penggunaan telah tercapai atau terlewat.'],
            ]);
        }

        $reservation->update([
            'status' => ReservationStatus::DibatalkanOlehPengguna,
            'cancellation_reason' => $reason,
            'cancelled_by_id' => $user->id,
            'cancelled_at' => now(),
        ]);

        return $reservation;
    }
}
