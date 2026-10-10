<?php

namespace Tests\Feature\Petugas;

use App\Enums\OperationalStatus;
use App\Enums\ReportStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_staff_facilities(): void
    {
        $this->get(route('staff.facilities.index'))
            ->assertRedirect(route('login'));

        $this->get(route('staff.facilities.maintenance'))
            ->assertRedirect(route('login'));
    }

    public function test_petugas_can_view_all_facilities_work_list(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'name' => 'Lab Multimedia 201',
        ]);

        $this->actingAs($petugas)
            ->get(route('staff.facilities.index'))
            ->assertOk()
            ->assertViewIs('staff.facilities.index')
            ->assertSee('Lab Multimedia 201');
    }

    public function test_petugas_can_search_and_filter_facilities_work_list(): void
    {
        $petugas = User::factory()->petugas()->create();
        Facility::factory()->create([
            'name' => 'Laboratorium Target Petugas',
            'type' => \App\Enums\FacilityType::Laboratorium,
        ]);
        $other = Facility::factory()->create([
            'name' => 'Aula Bukan Target',
            'type' => \App\Enums\FacilityType::Aula,
        ]);

        $response = $this->actingAs($petugas)->get(route('staff.facilities.index', [
            'search' => 'Target',
            'type' => 'laboratorium',
        ]));

        $response->assertOk()
            ->assertSee('Laboratorium Target Petugas')
            ->assertDontSee($other->name);
    }

    public function test_petugas_can_view_facilities_under_maintenance(): void
    {
        $petugas = User::factory()->petugas()->create();
        $goodFacility = Facility::factory()->create([
            'name' => 'Ruang Teori Normal',
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);
        $brokenFacility = Facility::factory()->create([
            'name' => 'Ruang Teori Rusak',
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $response = $this->actingAs($petugas)
            ->get(route('staff.facilities.maintenance'));

        $response->assertOk()
            ->assertViewIs('staff.facilities.maintenance')
            ->assertSee('Ruang Teori Rusak')
            ->assertDontSee('Ruang Teori Normal');
    }

    public function test_maintenance_list_shows_latest_reason_and_change_audit(): void
    {
        $this->travelTo(now()->setTime(10, 0));
        $petugas = User::factory()->petugas()->create(['name' => 'Petugas Audit']);
        $facility = Facility::factory()->create([
            'name' => 'Laboratorium Audit Perbaikan',
            'type' => \App\Enums\FacilityType::Laboratorium,
            'location' => 'Gedung Audit Lantai 2',
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $this->actingAs($petugas)
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DalamPerbaikan->value,
                'reason' => 'Sensor suhu sedang diperiksa.',
            ])
            ->assertRedirect(route('staff.facilities.maintenance'));

        $this->get(route('staff.facilities.maintenance'))
            ->assertOk()
            ->assertSeeInOrder([
                'Laboratorium Audit Perbaikan',
                'laboratorium',
                'Gedung Audit Lantai 2',
                'Sensor suhu sedang diperiksa.',
                now()->format('d/m/Y H:i'),
                'Petugas Audit',
            ]);
    }

    public function test_petugas_can_change_facility_to_maintenance_with_cascading_reservation_effects(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $user = User::factory()->create();

        // 1. Future pending reservation -> should be auto-rejected
        $pending = Reservation::factory()->create([
            'facility_id' => $facility->id,
            'user_id' => $user->id,
            'status' => ReservationStatus::Menunggu,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        // 2. Future approved reservation -> should be auto-cancelled by staff
        $approved = Reservation::factory()->create([
            'facility_id' => $facility->id,
            'user_id' => $user->id,
            'status' => ReservationStatus::Disetujui,
            'date' => now()->addDays(4)->toDateString(),
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DalamPerbaikan->value,
                'reason' => 'Perbaikan plafon dan instalasi kabel audio',
            ]);

        $response->assertRedirect(route('staff.facilities.maintenance'));

        $facility->refresh();
        $this->assertEquals(OperationalStatus::DalamPerbaikan, $facility->operational_status);

        // Assert pending was auto-rejected
        $pending->refresh();
        $this->assertEquals(ReservationStatus::Ditolak, $pending->status);
        $this->assertStringContainsString('Perbaikan plafon', $pending->rejection_reason);

        // Assert approved was auto-cancelled by staff
        $approved->refresh();
        $this->assertEquals(ReservationStatus::DibatalkanOlehPetugas, $approved->status);
        $this->assertStringContainsString('Perbaikan plafon', $approved->cancellation_reason);
        $this->assertEquals($petugas->id, $approved->cancelled_by_id);

        // Assert status log created
        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'changed_by_id' => $petugas->id,
            'change_type' => 'operational_status',
            'old_value' => 'dapat_digunakan',
            'new_value' => 'dalam_perbaikan',
        ]);
    }

    public function test_changing_to_maintenance_preserves_ongoing_reservation(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);
        $ongoing = Reservation::factory()->create([
            'facility_id' => $facility->id,
            'status' => ReservationStatus::Berlangsung,
            'date' => now()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
        ]);

        $this->actingAs($petugas)->post(route('staff.facilities.update-status', $facility), [
            'operational_status' => OperationalStatus::DalamPerbaikan->value,
            'reason' => 'Perbaikan darurat tanpa menghentikan kegiatan berjalan.',
        ])->assertRedirect(route('staff.facilities.maintenance'));

        $this->assertEquals(ReservationStatus::Berlangsung, $ongoing->fresh()->status);
    }

    public function test_status_change_requires_reason(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $this->actingAs($petugas)
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DalamPerbaikan->value,
                'reason' => '',
            ])
            ->assertSessionHasErrors('reason');

        $this->assertEquals(OperationalStatus::DapatDigunakan, $facility->fresh()->operational_status);
    }

    public function test_report_link_must_be_processing_before_status_change(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);
        $report = Report::factory()->create([
            'facility_id' => $facility->id,
            'status' => ReportStatus::Baru,
        ]);

        $this->actingAs($petugas)
            ->from(route('staff.facilities.show', $facility))
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DalamPerbaikan->value,
                'reason' => 'Menunggu pemeriksaan lanjutan.',
                'report_id' => $report->id,
            ])
            ->assertRedirect(route('staff.facilities.show', $facility))
            ->assertSessionHasErrors('report_id');

        $this->assertEquals(OperationalStatus::DapatDigunakan, $facility->fresh()->operational_status);
    }

    public function test_petugas_can_restore_facility_to_available_status_with_notes(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DapatDigunakan->value,
                'reason' => 'Perbaikan plafon telah selesai dan ruangan telah dibersihkan.',
            ]);

        $response->assertRedirect(route('staff.facilities.maintenance'));

        $facility->refresh();
        $this->assertEquals(OperationalStatus::DapatDigunakan, $facility->operational_status);

        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'changed_by_id' => $petugas->id,
            'change_type' => 'operational_status',
            'old_value' => 'dalam_perbaikan',
            'new_value' => 'dapat_digunakan',
        ]);
    }

    public function test_updating_status_linked_with_report_succeeds_when_report_is_processing(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $report = Report::factory()->create([
            'facility_id' => $facility->id,
            'status' => ReportStatus::Diproses,
            'processed_by_id' => $petugas->id,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.facilities.update-status', $facility), [
                'operational_status' => OperationalStatus::DalamPerbaikan->value,
                'reason' => 'Tindak lanjut laporan #'.$report->report_number,
                'report_id' => $report->id,
            ]);

        $response->assertRedirect(route('staff.facilities.maintenance'));

        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'report_id' => $report->id,
            'new_value' => 'dalam_perbaikan',
        ]);
    }
}
