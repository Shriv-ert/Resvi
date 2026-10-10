<?php

namespace App\Services;

use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffReservationService
{
    /**
     * Update automatic time-based statuses for reservations.
     */
    public function syncTimeBasedStatuses(): void
    {
        Reservation::syncTimeBasedStatuses();
    }

    /**
     * Get active queue: Menunggu, Disetujui, Berlangsung.
     * Ordered by: status group (Menunggu first, Disetujui, Berlangsung), then created_at ASC (FIFO).
     */
    public function getActiveQueue(?string $search = null, ?string $type = null, ?string $status = null): Collection
    {
        $this->syncTimeBasedStatuses();

        $query = Reservation::query()
            ->with(['facility', 'user'])
            ->whereIn('status', [
                ReservationStatus::Menunggu,
                ReservationStatus::Disetujui,
                ReservationStatus::Berlangsung,
            ]);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('facility', fn ($fq) => $fq->where('name', 'like', "%{$term}%"));
            });
        }

        if (! empty($type)) {
            $query->whereHas('facility', fn ($fq) => $fq->where('type', $type));
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query
            ->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'berlangsung') ASC")
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get final reservations history: Selesai, Ditolak, Dibatalkan pengguna/petugas, Kedaluwarsa.
     * Ordered by: created_at ASC (FIFO).
     */
    public function getHistory(?string $search = null, ?string $type = null, ?string $status = null): Collection
    {
        $this->syncTimeBasedStatuses();

        $query = Reservation::query()
            ->with(['facility', 'user', 'cancelledBy', 'rejectedBy', 'approvedBy'])
            ->whereIn('status', [
                ReservationStatus::Selesai,
                ReservationStatus::Ditolak,
                ReservationStatus::DibatalkanOlehPengguna,
                ReservationStatus::DibatalkanOlehPetugas,
                ReservationStatus::Kedaluwarsa,
            ]);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('facility', fn ($fq) => $fq->where('name', 'like', "%{$term}%"));
            });
        }

        if (! empty($type)) {
            $query->whereHas('facility', fn ($fq) => $fq->where('type', $type));
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        return $query
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Approve reservation (FR-PTG-03 & BR-PTG-03).
     */
    public function approve(Reservation $reservation, string $staffId): Reservation
    {
        $approvedReservation = DB::transaction(function () use ($reservation, $staffId): ?Reservation {
            $lockedReservation = Reservation::query()
                ->where('id', $reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReservation->status !== ReservationStatus::Menunggu) {
                throw ValidationException::withMessages([
                    'approve' => 'Hanya permohonan dengan status Menunggu yang dapat disetujui.',
                ]);
            }

            if (! $lockedReservation->start_date_time->isFuture()) {
                $lockedReservation->update([
                    'status' => ReservationStatus::Kedaluwarsa,
                    'expiration_reason' => 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',
                ]);

                return null;
            }

            $lockedFacility = Facility::query()
                ->where('id', $lockedReservation->facility_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedFacility->is_active) {
                throw ValidationException::withMessages([
                    'approve' => 'Fasilitas tidak aktif pada master data sehingga tidak dapat disetujui.',
                ]);
            }

            if ($lockedFacility->operational_status !== OperationalStatus::DapatDigunakan) {
                throw ValidationException::withMessages([
                    'approve' => 'Fasilitas sedang dalam perbaikan sehingga tidak dapat disetujui.',
                ]);
            }

            // Conflict check with other approved or ongoing reservations
            $hasConflict = Reservation::query()
                ->where('id', '!=', $lockedReservation->id)
                ->where('facility_id', $lockedReservation->facility_id)
                ->where('date', $lockedReservation->date)
                ->whereIn('status', [ReservationStatus::Disetujui, ReservationStatus::Berlangsung])
                ->where('start_time', '<', $lockedReservation->end_time)
                ->where('end_time', '>', $lockedReservation->start_time)
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'approve' => 'Jadwal bentrok dengan reservasi lain yang telah disetujui pada fasilitas ini.',
                ]);
            }

            // Approve this reservation
            $lockedReservation->update([
                'status' => ReservationStatus::Disetujui,
                'approved_by_id' => $staffId,
                'approved_at' => now(),
            ]);

            // Auto-reject any other pending reservations conflicting with this slot
            $conflictingPending = Reservation::query()
                ->where('id', '!=', $lockedReservation->id)
                ->where('facility_id', $lockedReservation->facility_id)
                ->where('date', $lockedReservation->date)
                ->where('status', ReservationStatus::Menunggu)
                ->where('start_time', '<', $lockedReservation->end_time)
                ->where('end_time', '>', $lockedReservation->start_time)
                ->get();

            $now = now();
            foreach ($conflictingPending as $pending) {
                $pending->update([
                    'status' => ReservationStatus::Ditolak,
                    'rejection_reason' => 'Jadwal fasilitas telah diberikan kepada pengajuan lain (' . $lockedReservation->reservation_number . ').',
                    'rejected_by_id' => $staffId,
                    'rejected_at' => $now,
                ]);
            }

            return $lockedReservation->fresh(['facility', 'user', 'approvedBy']);
        });

        if ($approvedReservation === null) {
            throw ValidationException::withMessages([
                'approve' => 'Waktu mulai permohonan reservasi telah tercapai. Pengajuan menjadi kedaluwarsa.',
            ]);
        }

        return $approvedReservation;
    }

    /**
     * Reject reservation (FR-PTG-04 & BR-PTG-02).
     */
    public function reject(Reservation $reservation, string $reason, string $staffId): Reservation
    {
        $rejectedReservation = DB::transaction(function () use ($reservation, $reason, $staffId): ?Reservation {
            $lockedReservation = Reservation::query()
                ->where('id', $reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReservation->status !== ReservationStatus::Menunggu) {
                throw ValidationException::withMessages([
                    'reject' => 'Hanya permohonan dengan status Menunggu yang dapat ditolak.',
                ]);
            }

            if (! $lockedReservation->start_date_time->isFuture()) {
                $lockedReservation->update([
                    'status' => ReservationStatus::Kedaluwarsa,
                    'expiration_reason' => 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',
                ]);

                return null;
            }

            $lockedReservation->update([
                'status' => ReservationStatus::Ditolak,
                'rejection_reason' => trim($reason),
                'rejected_by_id' => $staffId,
                'rejected_at' => now(),
            ]);

            return $lockedReservation->fresh(['facility', 'user', 'rejectedBy']);
        });

        if ($rejectedReservation === null) {
            throw ValidationException::withMessages([
                'reject' => 'Waktu mulai permohonan reservasi telah terlampaui. Pengajuan telah menjadi kedaluwarsa.',
            ]);
        }

        return $rejectedReservation;
    }

    /**
     * Urgent cancellation by staff (FR-PTG-05 & BR-PTG-02).
     */
    public function cancelUrgent(Reservation $reservation, string $reason, string $staffId): Reservation
    {
        return DB::transaction(function () use ($reservation, $reason, $staffId): Reservation {
            $lockedReservation = Reservation::query()
                ->where('id', $reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedReservation->status !== ReservationStatus::Disetujui) {
                throw ValidationException::withMessages([
                    'cancel' => 'Hanya reservasi dengan status Disetujui yang dapat dibatalkan mendesak.',
                ]);
            }

            if (! $lockedReservation->start_date_time->isFuture()) {
                throw ValidationException::withMessages([
                    'cancel' => 'Reservasi yang sudah berlangsung atau selesai tidak dapat dibatalkan.',
                ]);
            }

            $lockedReservation->update([
                'status' => ReservationStatus::DibatalkanOlehPetugas,
                'cancellation_reason' => trim($reason),
                'cancelled_by_id' => $staffId,
                'cancelled_at' => now(),
            ]);

            return $lockedReservation->fresh(['facility', 'user', 'cancelledBy']);
        });
    }
}
