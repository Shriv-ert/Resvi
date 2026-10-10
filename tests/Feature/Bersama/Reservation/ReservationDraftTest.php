<?php

namespace Tests\Feature\Bersama\Reservation;

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

    public function test_guest_can_open_reservation_form_for_an_active_facility(): void
    {
        $response = $this->get(route('reservations.create', ['facility_id' => $this->facility->id]));

        $response->assertOk()
            ->assertViewIs('reservations.create')
            ->assertSee($this->facility->name)
            ->assertSee('Cek Ketersediaan Server')
            ->assertSee('Simpan Draft & Masuk / Login', false);
    }

    public function test_reservation_form_without_facility_redirects_to_catalog(): void
    {
        $this->get(route('reservations.create'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('warning');
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

    public function test_admin_login_clears_draft_and_redirects_to_admin_facilities(): void
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

        // Admin masuk ke entry point modul Admin dan draft pengunjung dibuang.
        $loginResponse->assertRedirect(route('admin.facilities.index'));
        $this->assertFalse(session()->has(GuestReservationDraftService::SESSION_KEY));
    }

    public function test_petugas_login_clears_draft_and_redirects_to_reservation_queue(): void
    {
        $password = 'Petugas-Secret-123!';
        $petugas = User::factory()->petugas()->create([
            'password' => bcrypt($password),
        ]);

        $this->withSession([
            GuestReservationDraftService::SESSION_KEY => [
                'facility_id' => $this->facility->id,
                'date' => $this->targetDate,
                'start_time' => '09:00',
                'end_time' => '11:00',
            ],
        ]);

        $loginResponse = $this->post('/login', [
            'email' => $petugas->email,
            'password' => $password,
        ]);

        $loginResponse->assertRedirect(route('staff.reservations.index'));
        $this->assertFalse(session()->has(GuestReservationDraftService::SESSION_KEY));
    }
}
