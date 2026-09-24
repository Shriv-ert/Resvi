<?php

namespace App\Services;

use App\Enums\FacilityStatusChangeType;
use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use App\Models\FacilityStatusLog;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final readonly class FacilityService
{
    /**
     * Get paginated facilities with search and filters.
     */
    public function getPaginatedFacilities(
        ?string $search = null,
        ?string $type = null,
        ?string $masterStatus = null,
        ?string $operationalStatus = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Facility::query()
            ->with(['photos'])
            ->search($search)
            ->ofType($type);

        if ($masterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($masterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        if (! empty($operationalStatus)) {
            $query->where('operational_status', $operationalStatus);
        }

        return $query->latest('created_at')->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new facility with default active and operational status.
     *
     * @param  array{name: string, type: string|FacilityType, location: string, capacity?: ?int, description: string}  $data
     */
    public function createFacility(array $data, ?UploadedFile $photo, string $adminId): Facility
    {
        return DB::transaction(function () use ($data, $photo, $adminId): Facility {
            $type = $data['type'] instanceof FacilityType ? $data['type'] : FacilityType::from($data['type']);
            $capacity = $type === FacilityType::Alat ? null : (! empty($data['capacity']) ? (int) $data['capacity'] : null);

            $facility = Facility::query()->create([
                'name' => trim($data['name']),
                'type' => $type,
                'location' => trim($data['location']),
                'capacity' => $capacity,
                'description' => trim($data['description']),
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ]);

            if ($photo instanceof UploadedFile) {
                $photoPath = $photo->store('facilities', 'public');

                FacilityPhoto::query()->create([
                    'facility_id' => $facility->id,
                    'photo_path' => $photoPath,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            FacilityStatusLog::query()->create([
                'facility_id' => $facility->id,
                'changed_by_id' => $adminId,
                'change_type' => FacilityStatusChangeType::IsActive,
                'old_value' => '0',
                'new_value' => '1',
                'reason' => 'Pendaftaran fasilitas baru',
                'changed_at' => now(),
            ]);

            return $facility;
        });
    }

    /**
     * Update an existing facility's master attributes without changing operational status.
     *
     * @param  array{name: string, type: string|FacilityType, location: string, capacity?: ?int, description: string}  $data
     */
    public function updateFacility(Facility $facility, array $data, ?UploadedFile $photo): Facility
    {
        return DB::transaction(function () use ($facility, $data, $photo): Facility {
            $type = $data['type'] instanceof FacilityType ? $data['type'] : FacilityType::from($data['type']);
            $capacity = $type === FacilityType::Alat ? null : (! empty($data['capacity']) ? (int) $data['capacity'] : null);

            $facility->update([
                'name' => trim($data['name']),
                'type' => $type,
                'location' => trim($data['location']),
                'capacity' => $capacity,
                'description' => trim($data['description']),
            ]);

            if ($photo instanceof UploadedFile) {
                $photoPath = $photo->store('facilities', 'public');

                $primaryPhoto = $facility->photos()->where('is_primary', true)->first();

                if ($primaryPhoto) {
                    if (Storage::disk('public')->exists($primaryPhoto->photo_path)) {
                        Storage::disk('public')->delete($primaryPhoto->photo_path);
                    }

                    $primaryPhoto->update([
                        'photo_path' => $photoPath,
                    ]);
                } else {
                    FacilityPhoto::query()->create([
                        'facility_id' => $facility->id,
                        'photo_path' => $photoPath,
                        'is_primary' => true,
                        'sort_order' => 0,
                    ]);
                }
            }

            return $facility->fresh(['photos']);
        });
    }

    /**
     * Deactivate a facility with active reservation conflict check and auto-rejection of pending requests.
     */
    public function deactivateFacility(Facility $facility, string $reason, string $adminId): void
    {
        if (! $facility->is_active) {
            return;
        }

        // Guard Check: Check for active reservations
        $hasActiveReservations = $facility->reservations()
            ->whereIn('status', [ReservationStatus::Disetujui, ReservationStatus::Berlangsung])
            ->exists();

        if ($hasActiveReservations) {
            throw ValidationException::withMessages([
                'deactivate' => 'Fasilitas tidak dapat dinonaktifkan karena masih memiliki reservasi yang telah disetujui atau sedang berlangsung.',
            ]);
        }

        DB::transaction(function () use ($facility, $reason, $adminId): void {
            $facility->update([
                'is_active' => false,
            ]);

            // Auto-reject future pending reservations; mark past pending ones as expired
            $pendingReservations = $facility->reservations()
                ->where('status', ReservationStatus::Menunggu)
                ->get();

            $now = now();

            foreach ($pendingReservations as $reservation) {
                $dateString = $reservation->date instanceof Carbon ? $reservation->date->format('Y-m-d') : (string) $reservation->date;
                $startDateTime = Carbon::parse($dateString.' '.$reservation->start_time);

                if ($startDateTime->isFuture()) {
                    $reservation->update([
                        'status' => ReservationStatus::Ditolak,
                        'rejection_reason' => 'Fasilitas dinonaktifkan oleh administrator sistem: '.trim($reason),
                        'rejected_by_id' => $adminId,
                        'rejected_at' => $now,
                    ]);
                } else {
                    $reservation->update([
                        'status' => ReservationStatus::Kedaluwarsa,
                        'expiration_reason' => 'Jadwal telah dimulai sebelum persetujuan.',
                    ]);
                }
            }

            FacilityStatusLog::query()->create([
                'facility_id' => $facility->id,
                'changed_by_id' => $adminId,
                'change_type' => FacilityStatusChangeType::IsActive,
                'old_value' => '1',
                'new_value' => '0',
                'reason' => trim($reason),
                'changed_at' => $now,
            ]);
        });
    }

    /**
     * Reactivate a deactivated facility while preserving its operational status.
     */
    public function reactivateFacility(Facility $facility, string $adminId): void
    {
        if ($facility->is_active) {
            return;
        }

        DB::transaction(function () use ($facility, $adminId): void {
            $facility->update([
                'is_active' => true,
            ]);

            FacilityStatusLog::query()->create([
                'facility_id' => $facility->id,
                'changed_by_id' => $adminId,
                'change_type' => FacilityStatusChangeType::IsActive,
                'old_value' => '0',
                'new_value' => '1',
                'reason' => 'Fasilitas diaktifkan kembali oleh administrator sistem.',
                'changed_at' => now(),
            ]);
        });
    }
}
