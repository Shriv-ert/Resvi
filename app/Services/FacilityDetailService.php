<?php

namespace App\Services;

use App\Models\Facility;

final readonly class FacilityDetailService
{
    /**
     * Mengambil detail fasilitas aktif beserta galeri fotonya.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getFacility(string $id): Facility
    {
        return Facility::query()
            ->active()
            ->with(['photos'])
            ->findOrFail($id);
    }
}
