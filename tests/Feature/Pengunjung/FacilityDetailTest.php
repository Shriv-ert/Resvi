<?php

namespace Tests\Feature\Pengunjung;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FacilityDetailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_view_active_facility_detail_with_primary_photo_and_reservation_link(): void
    {
        $facility = Facility::factory()->create([
            'name' => 'Ruang Seminar Merdeka',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Rektorat Lt. 2',
            'capacity' => 120,
            'description' => 'Aula untuk seminar dan kegiatan akademik.',
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        FacilityPhoto::factory()->create([
            'facility_id' => $facility->id,
            'photo_path' => 'https://cdn.example.test/ruang-seminar-merdeka.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('facilities.show', $facility));

        $response->assertOk()
            ->assertViewIs('facilities.show')
            ->assertSee('Ruang Seminar Merdeka')
            ->assertSee('Gedung Rektorat Lt. 2')
            ->assertSee('Aula untuk seminar dan kegiatan akademik.')
            ->assertSee('Dapat digunakan')
            ->assertSee('https://cdn.example.test/ruang-seminar-merdeka.jpg')
            ->assertSee(route('reservations.create', ['facility_id' => $facility->id]));
    }

    public function test_inactive_facility_detail_is_not_publicly_accessible(): void
    {
        $facility = Facility::factory()->inactive()->create([
            'name' => 'Ruang Arsip Internal',
        ]);

        $this->get(route('facilities.show', $facility))
            ->assertNotFound();
    }

    public function test_facility_in_repair_detail_disables_reservation(): void
    {
        $facility = Facility::factory()->inRepair()->create([
            'name' => 'Laboratorium Rusak',
        ]);

        $response = $this->get(route('facilities.show', $facility));

        $response->assertOk()
            ->assertSee('Dalam Perbaikan Fisik:')
            ->assertSee('Reservasi Tidak Tersedia')
            ->assertDontSee(route('reservations.create', ['facility_id' => $facility->id]));
    }
}
