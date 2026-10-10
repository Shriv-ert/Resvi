<?php

namespace Tests\Feature\Pengunjung;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_view_facility_catalog_on_homepage(): void
    {
        $facility = Facility::factory()->create([
            'name' => 'Laboratorium Jaringan Komputer',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Sains Lt. 3',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Katalog Fasilitas Kampus');
        $response->assertSee('Laboratorium Jaringan Komputer');
        $response->assertSee('Gedung Sains Lt. 3');
    }

    public function test_inactive_facilities_are_hidden_from_public_catalog(): void
    {
        $activeFacility = Facility::factory()->create([
            'name' => 'Ruang Kuliah Aktif',
            'is_active' => true,
        ]);

        $inactiveFacility = Facility::factory()->inactive()->create([
            'name' => 'Ruang Arsip Non-Aktif',
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Ruang Kuliah Aktif');
        $response->assertDontSee('Ruang Arsip Non-Aktif');
    }

    public function test_search_by_name_substring(): void
    {
        Facility::factory()->create([
            'name' => 'Laboratorium Multimedia',
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Ruang Teater 201',
            'is_active' => true,
        ]);

        $response = $this->get('/?q=Multimedia');

        $response->assertOk();
        $response->assertSee('Laboratorium Multimedia');
        $response->assertDontSee('Ruang Teater 201');
    }

    public function test_filter_by_type(): void
    {
        Facility::factory()->create([
            'name' => 'Aula Graha Utama',
            'type' => FacilityType::Aula,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Lapangan Basket',
            'type' => FacilityType::Lapangan,
            'is_active' => true,
        ]);

        $response = $this->get('/?type=aula');

        $response->assertOk();
        $response->assertSee('Aula Graha Utama');
        $response->assertDontSee('Lapangan Basket');
    }

    public function test_filter_by_location(): void
    {
        Facility::factory()->create([
            'name' => 'Ruang Kelas 101',
            'location' => 'Gedung Barat Lt. 1',
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Ruang Kelas 201',
            'location' => 'Gedung Timur Lt. 2',
            'is_active' => true,
        ]);

        $response = $this->get('/?location=' . urlencode('Gedung Barat Lt. 1'));

        $response->assertOk();
        $response->assertSee('Ruang Kelas 101');
        $response->assertDontSee('Ruang Kelas 201');
    }

    public function test_filter_by_minimum_capacity(): void
    {
        Facility::factory()->create([
            'name' => 'Ruang Kapasitas Besar',
            'capacity' => 60,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Ruang Kapasitas Kecil',
            'capacity' => 20,
            'is_active' => true,
        ]);

        $response = $this->get('/?capacity=40');

        $response->assertOk();
        $response->assertSee('Ruang Kapasitas Besar');
        $response->assertDontSee('Ruang Kapasitas Kecil');
    }

    public function test_null_capacity_facilities_are_excluded_when_capacity_filter_active(): void
    {
        Facility::factory()->create([
            'name' => 'Proyektor Ruangan',
            'type' => FacilityType::Alat,
            'capacity' => null,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Ruang Kelas Besar',
            'type' => FacilityType::Kelas,
            'capacity' => 50,
            'is_active' => true,
        ]);

        // Filter kapasitas minimal 10 orang
        $response = $this->get('/?capacity=10');

        $response->assertOk();
        $response->assertSee('Ruang Kelas Besar');
        $response->assertDontSee('Proyektor Ruangan');
    }

    public function test_operational_status_badge_is_displayed_correctly(): void
    {
        Facility::factory()->create([
            'name' => 'Ruang Normal',
            'operational_status' => OperationalStatus::DapatDigunakan,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Ruang Rusak',
            'operational_status' => OperationalStatus::DalamPerbaikan,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Dapat digunakan');
        $response->assertSee('Dalam perbaikan');
    }

    public function test_validation_rejects_invalid_filter_type(): void
    {
        $response = $this->get('/?type=tipe_ngawur');

        $response->assertSessionHasErrors(['type']);
    }

    public function test_empty_state_shown_when_no_facility_matches(): void
    {
        Facility::factory()->create([
            'name' => 'Ruang Biasa',
            'is_active' => true,
        ]);

        $response = $this->get('/?q=ZzzzzzTidakAdaNamaIni');

        $response->assertOk();
        $response->assertSee('Tidak ada fasilitas yang ditemukan');
        $response->assertSee('Bersihkan Semua Filter');
    }

    public function test_search_and_multiple_filters_are_applied_together(): void
    {
        Facility::factory()->create([
            'name' => 'Aula Teknologi Informasi',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Teknologi',
            'capacity' => 200,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Aula Teknologi Informasi Kecil',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Teknologi',
            'capacity' => 50,
            'is_active' => true,
        ]);

        Facility::factory()->create([
            'name' => 'Aula Teknologi Informasi Lapangan',
            'type' => FacilityType::Lapangan,
            'location' => 'Gedung Teknologi',
            'capacity' => 200,
            'is_active' => true,
        ]);

        $response = $this->get('/?q=Teknologi&type=aula&location='.urlencode('Gedung Teknologi').'&capacity=100');

        $response->assertOk()
            ->assertSee('Aula Teknologi Informasi')
            ->assertDontSee('Aula Teknologi Informasi Kecil')
            ->assertDontSee('Aula Teknologi Informasi Lapangan');
    }

    public function test_reset_filter_link_points_to_unfiltered_catalog(): void
    {
        Facility::factory()->create([
            'name' => 'Fasilitas Hasil Filter',
            'is_active' => true,
        ]);

        $response = $this->get('/?q=Hasil');

        $response->assertOk()
            ->assertSee('Reset Filter')
            ->assertSee(route('facilities.index'));
    }
}
