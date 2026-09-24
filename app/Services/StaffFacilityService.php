<?php

namespace App\Services;

use App\Enums\FacilityStatusChangeType;
use App\Enums\OperationalStatus;
use App\Enums\ReportStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\FacilityStatusLog;
use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffFacilityService
{
    /**
     * Get all facilities for staff work view (FR-PTG-13).
     */
    public function getAllFacilities(?string $search = null, ?string $type = null): Collection
    {
        $query = Facility::query()->with(['photos', 'statusLogs' => fn ($q) => $q->latest('changed_at')]);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        if (! empty($type)) {
            $query->where('type', $type);
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get facilities currently under maintenance (FR-PTG-12).
     */
    public function getMaintenanceFacilities(?string $search = null, ?string $type = null): Collection
    {
        $query = Facility::query()
            ->with([
                'photos',
                'statusLogs' => fn ($q) => $q->where('change_type', FacilityStatusChangeType::OperationalStatus)
                    ->latest('changed_at')
                    ->with('changedBy'),
            ])
            ->where('operational_status', OperationalStatus::DalamPerbaikan);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        if (! empty($type)) {
            $query->where('type', $type);
        }

        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Update operational status of facility (FR-PTG-10 & FR-PTG-11 & BR-PTG-05).
     */
    public function updateOperationalStatus(
        Facility $facility,
        OperationalStatus $newStatus,
        string $reason,
        string $staffId,
        ?string $reportId = null
    ): Facility {
        return DB::transaction(function () use ($facility, $newStatus, $reason, $staffId, $reportId): Facility {
            $lockedFacility = Facility::query()
                ->where('id', $facility->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedFacility->operational_status === $newStatus) {
                throw ValidationException::withMessages([
                    'operational_status' => 'Fasilitas sudah berada pada status operasional ' . $newStatus->label() . '.',
                ]);
            }

            if (! empty($reportId)) {
                $report = Report::query()->find($reportId);
                if ($report && $report->status !== ReportStatus::Diproses) {
                    throw ValidationException::withMessages([
                        'report_id' => 'Perubahan status operasional melalui laporan hanya dapat dilakukan jika laporan berstatus Diproses.',
                    ]);
                }
            }

            $oldValue = $lockedFacility->operational_status->value;
            $newValue = $newStatus->value;
            $now = now();

            $lockedFacility->update([
                'operational_status' => $newStatus,
            ]);

            FacilityStatusLog::query()->create([
                'facility_id' => $lockedFacility->id,
                'changed_by_id' => $staffId,
                'change_type' => FacilityStatusChangeType::OperationalStatus,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'reason' => trim($reason),
                'report_id' => $reportId ?: null,
                'changed_at' => $now,
            ]);

            // Cascading effects when entering maintenance (FR-PTG-10)
            if ($newStatus === OperationalStatus::DalamPerbaikan) {
                // 1. Auto-reject future pending reservations; past pending becomes expired
                $pendingReservations = $lockedFacility->reservations()
                    ->where('status', ReservationStatus::Menunggu)
                    ->get();

                foreach ($pendingReservations as $pending) {
                    if ($pending->start_date_time->isFuture()) {
                        $pending->update([
                            'status' => ReservationStatus::Ditolak,
                            'rejection_reason' => 'Fasilitas dialihkan ke status dalam perbaikan: ' . trim($reason),
                            'rejected_by_id' => $staffId,
                            'rejected_at' => $now,
                        ]);
                    } else {
                        $pending->update([
                            'status' => ReservationStatus::Kedaluwarsa,
                            'expiration_reason' => 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',
                        ]);
                    }
                }

                // 2. Auto-cancel future approved reservations
                $approvedReservations = $lockedFacility->reservations()
                    ->where('status', ReservationStatus::Disetujui)
                    ->get();

                foreach ($approvedReservations as $approved) {
                    if ($approved->start_date_time->isFuture()) {
                        $approved->update([
                            'status' => ReservationStatus::DibatalkanOlehPetugas,
                            'cancellation_reason' => 'Fasilitas dialihkan ke status dalam perbaikan: ' . trim($reason),
                            'cancelled_by_id' => $staffId,
                            'cancelled_at' => $now,
                        ]);
                    }
                }
            }

            return $lockedFacility->fresh(['photos', 'statusLogs']);
        });
    }
}
