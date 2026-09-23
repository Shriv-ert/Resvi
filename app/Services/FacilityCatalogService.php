<?php

namespace App\Services;

use App\Enums\FacilityType;
use App\Models\Facility;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class FacilityCatalogService
{
    /**
     * Mengambil daftar fasilitas aktif yang telah disaring dan dipaginasi.
     *
     * @param  array{q?: ?string, type?: ?string, location?: ?string, capacity?: ?int|string}  $filters
     */
    public function getPaginatedFacilities(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Facility::query()
            ->active()
            ->with(['primaryPhoto'])
            ->when(! empty($filters['q']), function ($query) use ($filters) {
                $keyword = trim((string) $filters['q']);
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->when(! empty($filters['type']), function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when(! empty($filters['location']), function ($query) use ($filters) {
                $query->where('location', $filters['location']);
            })
            ->when(! empty($filters['capacity']), function ($query) use ($filters) {
                $query->whereNotNull('capacity')
                    ->where('capacity', '>=', (int) $filters['capacity']);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mengambil daftar lokasi unik dari fasilitas aktif untuk opsi filter.
     *
     * @return array<int, string>
     */
    public function getAvailableLocations(): array
    {
        return Facility::query()
            ->active()
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->values()
            ->all();
    }

    /**
     * Mengambil daftar enum tipe fasilitas untuk opsi filter.
     *
     * @return array<int, FacilityType>
     */
    public function getAvailableTypes(): array
    {
        return FacilityType::cases();
    }
}
