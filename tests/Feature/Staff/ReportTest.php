<?php

namespace Tests\Feature\Staff;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_staff_reports(): void
    {
        $this->get(route('staff.reports.index'))
            ->assertRedirect(route('login'));

        $this->get(route('staff.reports.history'))
            ->assertRedirect(route('login'));
    }

    public function test_non_petugas_cannot_access_staff_reports(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('staff.reports.index'))
            ->assertForbidden();
    }

    public function test_petugas_can_view_active_reports_queue(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $activeReport = Report::factory()->create([
            'reporter_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReportStatus::Baru,
        ]);

        $finishedReport = Report::factory()->create([
            'reporter_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReportStatus::Selesai,
            'resolution_notes' => 'Sudah diperbaiki.',
        ]);

        $response = $this->actingAs($petugas)->get(route('staff.reports.index'));

        $response->assertOk()
            ->assertViewIs('staff.reports.index')
            ->assertSee($activeReport->report_number)
            ->assertDontSee($finishedReport->report_number);
    }

    public function test_petugas_can_view_final_reports_history(): void
    {
        $petugas = User::factory()->petugas()->create();
        $activeReport = Report::factory()->create(['status' => ReportStatus::Baru]);
        $resolvedReport = Report::factory()->create([
            'status' => ReportStatus::Selesai,
            'resolution_notes' => 'Lampu LED diganti baru',
        ]);

        $response = $this->actingAs($petugas)->get(route('staff.reports.history'));

        $response->assertOk()
            ->assertViewIs('staff.reports.history')
            ->assertSee($resolvedReport->report_number)
            ->assertDontSee($activeReport->report_number);
    }

    public function test_petugas_can_view_report_detail(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create([
            'description' => 'Keran wastafel patah dan air mengalir deras.',
        ]);

        $this->actingAs($petugas)
            ->get(route('staff.reports.show', $report))
            ->assertOk()
            ->assertSee($report->report_number)
            ->assertSee('Keran wastafel patah');
    }

    public function test_petugas_can_start_processing_a_new_report(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create(['status' => ReportStatus::Baru]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reports.start-process', $report));

        $response->assertRedirect(route('staff.reports.show', $report));

        $report->refresh();
        $this->assertEquals(ReportStatus::Diproses, $report->status);
        $this->assertEquals($petugas->id, $report->processed_by_id);
        $this->assertNotNull($report->processed_at);
    }

    public function test_petugas_can_resolve_processing_report_with_notes(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create([
            'status' => ReportStatus::Diproses,
            'processed_by_id' => $petugas->id,
            'processed_at' => now(),
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reports.resolve', $report), [
                'resolution_notes' => 'Unit pendingin ruangan telah diisi freon dan kipas outdoor dibersihkan.',
            ]);

        $response->assertRedirect(route('staff.reports.index'));

        $report->refresh();
        $this->assertEquals(ReportStatus::Selesai, $report->status);
        $this->assertEquals('Unit pendingin ruangan telah diisi freon dan kipas outdoor dibersihkan.', $report->resolution_notes);
        $this->assertEquals($petugas->id, $report->resolved_by_id);
        $this->assertNotNull($report->resolved_at);
    }

    public function test_petugas_cannot_directly_reject_new_report_without_processing_first(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create(['status' => ReportStatus::Baru]);

        $this->actingAs($petugas)
            ->from(route('staff.reports.show', $report))
            ->post(route('staff.reports.reject', $report), [
                'rejection_reason' => 'Bukan kerusakan fasilitas.',
            ])
            ->assertRedirect(route('staff.reports.show', $report))
            ->assertSessionHasErrors('reject');

        $this->assertEquals(ReportStatus::Baru, $report->fresh()->status);
    }

    public function test_petugas_can_reject_processing_report_with_reason(): void
    {
        $petugas = User::factory()->petugas()->create();
        $report = Report::factory()->create([
            'status' => ReportStatus::Diproses,
            'processed_by_id' => $petugas->id,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reports.reject', $report), [
                'rejection_reason' => 'Setelah inspeksi lapangan oleh teknisi, kondisi unit berfungsi optimal.',
            ]);

        $response->assertRedirect(route('staff.reports.index'));

        $report->refresh();
        $this->assertEquals(ReportStatus::Ditolak, $report->status);
        $this->assertEquals('Setelah inspeksi lapangan oleh teknisi, kondisi unit berfungsi optimal.', $report->rejection_reason);
        $this->assertEquals($petugas->id, $report->rejected_by_id);
        $this->assertNotNull($report->rejected_at);
    }
}
