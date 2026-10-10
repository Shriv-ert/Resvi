<?php

namespace Tests\Feature\Pengguna;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Facility $facility;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $this->facility = Facility::factory()->create([
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_reports(): void
    {
        $response = $this->get(route('user.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_pengguna_cannot_access_my_reports(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('user.reports.index'));
        $response->assertForbidden();
    }

    public function test_user_can_view_own_reports_and_not_others(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);

        $myReport = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'description' => 'Laporan Kerusakan AC Saya',
        ]);

        $otherReport = Report::factory()->create([
            'reporter_id' => $otherUser->id,
            'facility_id' => $this->facility->id,
            'description' => 'Laporan Kerusakan Pintu Orang Lain',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reports.index'));

        $response->assertOk();
        $response->assertSee($myReport->report_number);
        $response->assertDontSee($otherReport->report_number);
    }

    public function test_user_can_view_create_report_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.reports.create'));

        $response->assertOk();
        $response->assertSee($this->facility->name);
    }

    public function test_user_can_open_report_form_with_facility_preselected_from_detail_flow(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.reports.create', [
            'facility_id' => $this->facility->id,
        ]));

        $response->assertOk()
            ->assertSee('name="facility_id" value="'.$this->facility->id.'"', false)
            ->assertSee($this->facility->name);
    }

    public function test_user_can_submit_valid_report_without_photo(): void
    {
        $response = $this->actingAs($this->user)->post(route('user.reports.store'), [
            'facility_id' => $this->facility->id,
            'category' => ReportCategory::KerusakanElektronik->value,
            'description' => 'Layar proyektor berkedip dan padam mendadak saat presentasi.',
        ]);

        $response->assertRedirect(route('user.reports.index', ['as_user' => 1]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'category' => ReportCategory::KerusakanElektronik->value,
            'status' => ReportStatus::Baru->value,
            'description' => 'Layar proyektor berkedip dan padam mendadak saat presentasi.',
            'photo_path' => null,
        ]);
    }

    public function test_user_can_submit_valid_report_with_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('rusak.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('user.reports.store'), [
            'facility_id' => $this->facility->id,
            'category' => ReportCategory::KerusakanFisik->value,
            'description' => 'Gagang pintu ruangan rusak dan tidak dapat dikunci dari dalam.',
            'photo' => $file,
        ]);

        $response->assertRedirect(route('user.reports.index', ['as_user' => 1]));
        $response->assertSessionHas('success');

        $report = Report::where('reporter_id', $this->user->id)->first();
        $this->assertNotNull($report);
        $this->assertNotNull($report->photo_path);
        Storage::disk('public')->assertExists($report->photo_path);
    }

    public function test_report_submission_fails_with_invalid_facility_or_missing_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('user.reports.store'), [
            'facility_id' => 'non-existent-uuid',
            'category' => 'invalid-category',
            'description' => 'abc',
        ]);

        $response->assertSessionHasErrors(['facility_id', 'category', 'description']);
    }

    public function test_user_can_view_own_report_details(): void
    {
        $report = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'description' => 'Detail masalah AC berisik',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reports.show', $report));

        $response->assertOk();
        $response->assertSee($report->report_number);
        $response->assertSee('Detail masalah AC berisik');
    }

    public function test_user_report_details_show_resolution_or_rejection_reason(): void
    {
        $resolvedReport = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'status' => ReportStatus::Selesai,
            'resolution_notes' => 'Kabel daya telah diganti dan diuji.',
        ]);
        $rejectedReport = Report::factory()->create([
            'reporter_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'status' => ReportStatus::Ditolak,
            'rejection_reason' => 'Laporan tidak dapat diverifikasi di lokasi.',
        ]);

        $this->actingAs($this->user)
            ->get(route('user.reports.show', $resolvedReport))
            ->assertOk()
            ->assertSee('Kabel daya telah diganti dan diuji.');

        $this->get(route('user.reports.show', $rejectedReport))
            ->assertOk()
            ->assertSee('Laporan tidak dapat diverifikasi di lokasi.');
    }

    public function test_user_cannot_view_other_users_report_details(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);
        $report = Report::factory()->create([
            'reporter_id' => $otherUser->id,
            'facility_id' => $this->facility->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reports.show', $report));
        $response->assertForbidden();
    }
}
