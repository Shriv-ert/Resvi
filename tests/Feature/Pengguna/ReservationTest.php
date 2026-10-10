<?php

namespace Tests\Feature\Pengguna;

use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
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
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_reservations(): void
    {
        $response = $this->get(route('user.reservations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_pengguna_cannot_access_my_reservations(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('user.reservations.index'));
        $response->assertForbidden();
    }

    public function test_user_can_view_own_reservations_and_not_others(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);

        $myReservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'purpose' => 'Kegiatan Mandiri Saya',
        ]);

        $otherReservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'facility_id' => $this->facility->id,
            'purpose' => 'Kegiatan Orang Lain Rahasia',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reservations.index'));

        $response->assertOk();
        $response->assertSee($myReservation->reservation_number);
        $response->assertDontSee($otherReservation->reservation_number);
    }

    public function test_user_can_submit_valid_reservation(): void
    {
        $futureDate = now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'purpose' => 'Perkuliahan pengganti Pemrograman Web',
        ]);

        $response->assertRedirect(route('user.reservations.index', ['as_user' => 1]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu->value,
            'purpose' => 'Perkuliahan pengganti Pemrograman Web',
        ]);
    }

    public function test_submission_revalidates_schedule_rules_on_server(): void
    {
        $futureDate = now()->addDays(2)->toDateString();

        $nonSlotResponse = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '08:15',
            'end_time' => '10:00',
            'purpose' => 'Jadwal dengan menit tidak valid',
        ]);

        $nonSlotResponse->assertSessionHasErrors('start_time');

        $pastDateResponse = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $this->facility->id,
            'date' => now()->subDay()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'purpose' => 'Tanggal reservasi sudah lewat',
        ]);

        $pastDateResponse->assertSessionHasErrors('date');

        $this->assertDatabaseMissing('reservations', [
            'purpose' => 'Jadwal dengan menit tidak valid',
        ]);
        $this->assertDatabaseMissing('reservations', [
            'purpose' => 'Tanggal reservasi sudah lewat',
        ]);
    }

    public function test_submission_fails_with_conflicting_approved_reservation(): void
    {
        $futureDate = now()->addDays(2)->toDateString();

        Reservation::factory()->approved()->create([
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '10:00',
            'end_time' => '12:00',
            'purpose' => 'Mencoba slot bentrok',
        ]);

        $response->assertSessionHasErrors('date');
        $this->assertDatabaseMissing('reservations', [
            'user_id' => $this->user->id,
            'purpose' => 'Mencoba slot bentrok',
        ]);
    }

    public function test_same_user_can_reserve_overlapping_times_at_different_facilities(): void
    {
        $otherFacility = Facility::factory()->create([
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);
        $date = now()->addDays(2)->toDateString();

        Reservation::factory()->approved()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => $date,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $otherFacility->id,
            'date' => $date,
            'start_time' => '09:30',
            'end_time' => '10:30',
            'purpose' => 'Kegiatan di fasilitas berbeda',
        ]);

        $response->assertRedirect(route('user.reservations.index', ['as_user' => 1]));
        $this->assertDatabaseHas('reservations', [
            'user_id' => $this->user->id,
            'facility_id' => $otherFacility->id,
            'date' => $date,
            'status' => ReservationStatus::Menunggu->value,
            'purpose' => 'Kegiatan di fasilitas berbeda',
        ]);
    }

    public function test_submission_fails_when_facility_is_in_repair(): void
    {
        $repairFacility = Facility::factory()->create([
            'is_active' => true,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $futureDate = now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $repairFacility->id,
            'date' => $futureDate,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'purpose' => 'Mencoba reservasi fasilitas rusak',
        ]);

        $response->assertSessionHasErrors('facility_id');
    }

    public function test_submission_fails_when_facility_is_inactive(): void
    {
        $inactiveFacility = Facility::factory()->inactive()->create();

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $inactiveFacility->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'purpose' => 'Mencoba fasilitas nonaktif',
        ]);

        $response->assertSessionHasErrors('facility_id');
        $this->assertDatabaseMissing('reservations', [
            'purpose' => 'Mencoba fasilitas nonaktif',
        ]);
    }

    public function test_duplicate_submission_by_same_user_is_rejected(): void
    {
        $futureDate = now()->addDays(2)->toDateString();

        Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.store'), [
            'facility_id' => $this->facility->id,
            'date' => $futureDate,
            'start_time' => '13:00',
            'end_time' => '15:00',
            'purpose' => 'Mencoba mengajukan duplikat',
        ]);

        $response->assertSessionHasErrors('purpose');
    }

    public function test_user_can_view_own_reservation_details(): void
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'purpose' => 'Detail Khusus Saya',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reservations.show', $reservation));

        $response->assertOk();
        $response->assertSee($reservation->reservation_number);
        $response->assertSee('Detail Khusus Saya');
    }

    public function test_user_cannot_view_other_users_reservation_details(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'facility_id' => $this->facility->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('user.reservations.show', $reservation));
        $response->assertForbidden();
    }

    public function test_user_can_cancel_pending_reservation_before_start_time(): void
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.cancel', $reservation), [
            'cancellation_reason' => 'Dosen pengampu berhalangan hadir',
        ]);

        $response->assertRedirect(route('user.reservations.show', ['reservation' => $reservation->id, 'as_user' => 1]));
        $response->assertSessionHas('success');

        $reservation->refresh();
        $this->assertEquals(ReservationStatus::DibatalkanOlehPengguna, $reservation->status);
        $this->assertEquals('Dosen pengampu berhalangan hadir', $reservation->cancellation_reason);
        $this->assertEquals($this->user->id, $reservation->cancelled_by_id);
    }

    public function test_user_can_cancel_approved_reservation_before_start_time(): void
    {
        $reservation = Reservation::factory()->approved()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $this->actingAs($this->user)
            ->post(route('user.reservations.cancel', $reservation), [
                'cancellation_reason' => 'Kegiatan dipindahkan ke jadwal lain',
            ])
            ->assertRedirect(route('user.reservations.show', ['reservation' => $reservation->id, 'as_user' => 1]));

        $reservation->refresh();
        $this->assertEquals(ReservationStatus::DibatalkanOlehPengguna, $reservation->status);
    }

    public function test_cancellation_requires_a_reason(): void
    {
        $reservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.cancel', $reservation), [
            'cancellation_reason' => '',
        ]);

        $response->assertSessionHasErrors('cancellation_reason');
        $reservation->refresh();
        $this->assertEquals(ReservationStatus::Menunggu, $reservation->status);
    }

    public function test_user_cannot_cancel_reservations_with_final_or_active_statuses(): void
    {
        $disallowedStatuses = [
            ReservationStatus::Berlangsung,
            ReservationStatus::Selesai,
            ReservationStatus::Ditolak,
            ReservationStatus::DibatalkanOlehPengguna,
            ReservationStatus::DibatalkanOlehPetugas,
            ReservationStatus::Kedaluwarsa,
        ];

        foreach ($disallowedStatuses as $index => $status) {
            $reservation = Reservation::factory()->create([
                'user_id' => $this->user->id,
                'facility_id' => $this->facility->id,
                'date' => now()->addDays(3)->toDateString(),
                'start_time' => sprintf('%02d:00:00', 9 + $index),
                'end_time' => sprintf('%02d:00:00', 10 + $index),
                'status' => $status,
            ]);

            $response = $this->actingAs($this->user)->post(route('user.reservations.cancel', $reservation), [
                'cancellation_reason' => 'Tidak boleh dibatalkan',
            ]);

            $response->assertSessionHasErrors('cancellation_reason');
        }
    }

    public function test_user_cannot_cancel_another_users_reservation(): void
    {
        $otherUser = User::factory()->create([
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);
        $reservation = Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'facility_id' => $this->facility->id,
            'date' => now()->addDays(3)->toDateString(),
            'status' => ReservationStatus::Menunggu,
        ]);

        $this->actingAs($this->user)
            ->post(route('user.reservations.cancel', $reservation), [
                'cancellation_reason' => 'Bukan reservasi saya',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_cancel_past_start_time_reservation(): void
    {
        $pastReservation = Reservation::factory()->create([
            'user_id' => $this->user->id,
            'facility_id' => $this->facility->id,
            'date' => now()->subDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($this->user)->post(route('user.reservations.cancel', $pastReservation), [
            'cancellation_reason' => 'Mau membatalkan yang sudah lewat',
        ]);

        $response->assertSessionHasErrors('cancellation_reason');
    }
}
