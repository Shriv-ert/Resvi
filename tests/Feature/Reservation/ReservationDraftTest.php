<?php

namespace Tests\Feature\Reservation;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\User;
use App\Services\GuestReservationDraftService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReservationDraftTest extends TestCase
{
    use DatabaseTransactions;

    private Facility $facility;

    private string $targetDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetDate = CarbonImmutable::now('Asia/Jakarta')->addDays(4)->format('Y-m-d');

        $this->facility = Facility::factory()->create([
            'name' => 'Laboratorium Rekayasa Web',
            'type' => FacilityType::Laboratorium,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'is_active' => true,
        ]);
    }

    public function test_guest_can_store_draft_in_session_and_redirects_to_login(): void
    {
        $response = $this->post('/reservations/draft', [
            'facility_id' => $this->facility->id,
            'date' => $this->targetDate,
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas(GuestReservationDraftService::SESSION_KEY, [
            'facility_id' => $this->facility->id,
            'date' => $this->targetDate,
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);
    }

    public function test_user_role_pengguna_restores_draft_after_successful_login(): void
    {
        $password = 'Secret-Pass-123!';
        $user = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
            'password' => bcrypt($password),
        ]);

        // 1. Simpan draft sebagai pengunjung
        $this->withSession([
            GuestReservationDraftService::SESSION_KEY => [
                'facility_id' => $this->facility->id,
                'date' => $this->targetDate,
                'start_time' => '13:00',
                'end_time' => '15:30',
            ],
        ]);

        // 2. Login menggunakan kredensial Pengguna
        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // 3. Wajib dialihkan kembali ke form reservasi
        $loginResponse->assertRedirect(route('reservations.create'));

        // 4. Buka halaman reservasi create dan pastikan data draft terpampang
        $createResponse = $this->get('/reservations/create');
        $createResponse->assertOk();
        $createResponse->assertSee($this->facility->name);
        $createResponse->assertSee($this->targetDate);
        $createResponse->assertSee('13:00');
    }

    public function test_admin_login_clears_draft_and_redirects_home(): void
    {
        $password = 'Admin-Secret-123!';
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'user_category' => null,
            'password' => bcrypt($password),
        ]);

        // Simpan draft sebagai pengunjung
        $this->withSession([
            GuestReservationDraftService::SESSION_KEY => [
                'facility_id' => $this->facility->id,
                'date' => $this->targetDate,
                'start_time' => '09:00',
                'end_time' => '11:00',
            ],
        ]);

        // Login sebagai Admin
        $loginResponse = $this->post('/login', [
            'email' => $admin->email,
            'password' => $password,
        ]);

        // Admin dialihkan ke home, draft dihapus (KEPUTUSAN-GLOBAL.md 3.10)
        $loginResponse->assertRedirect(route('home'));
        $this->assertFalse(session()->has(GuestReservationDraftService::SESSION_KEY));
    }
}
