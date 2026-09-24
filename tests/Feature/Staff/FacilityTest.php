<?php

namespace Tests\Feature\Staff;

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
