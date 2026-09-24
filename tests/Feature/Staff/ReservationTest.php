<?php

namespace Tests\Feature\Staff;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_staff_reservations(): void
    {
        $this->get(route('staff.reservations.index'))
            ->assertRedirect(route('login'));

        $this->get(route('staff.reservations.history'))
            ->assertRedirect(route('login'));
    }

    public function test_non_petugas_cannot_access_staff_reservations(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('staff.reservations.index'))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('staff.reservations.index'))
            ->assertForbidden();
    }

    public function test_petugas_can_view_active_reservations_queue(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $pending = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReservationStatus::Menunggu,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $finished = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReservationStatus::Selesai,
            'date' => now()->subDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $response = $this->actingAs($petugas)->get(route('staff.reservations.index'));

        $response->assertOk()
            ->assertViewIs('staff.reservations.index')
            ->assertSee($pending->reservation_number)
            ->assertDontSee($finished->reservation_number);
    }

    public function test_petugas_can_view_final_reservations_history(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create();
        $user = User::factory()->create();

        $pending = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReservationStatus::Menunggu,
            'date' => now()->addDays(2)->toDateString(),
        ]);

        $rejected = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => ReservationStatus::Ditolak,
            'rejection_reason' => 'Tidak memenuhi syarat',
            'date' => now()->addDays(1)->toDateString(),
        ]);

        $response = $this->actingAs($petugas)->get(route('staff.reservations.history'));

        $response->assertOk()
            ->assertViewIs('staff.reservations.history')
            ->assertSee($rejected->reservation_number)
            ->assertDontSee($pending->reservation_number);
    }

    public function test_petugas_can_view_reservation_details(): void
    {
        $petugas = User::factory()->petugas()->create();
        $reservation = Reservation::factory()->create([
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        $this->actingAs($petugas)
            ->get(route('staff.reservations.show', $reservation))
            ->assertOk()
            ->assertSee($reservation->reservation_number)
            ->assertSee($reservation->facility->name);
    }

    public function test_petugas_can_approve_pending_reservation_and_auto_rejects_conflicting_pending(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $targetDate = now()->addDays(3)->toDateString();

        $res1 = Reservation::factory()->create([
            'user_id' => $user1->id,
            'facility_id' => $facility->id,
            'date' => $targetDate,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        // Conflicting pending reservation
        $res2 = Reservation::factory()->create([
            'user_id' => $user2->id,
            'facility_id' => $facility->id,
            'date' => $targetDate,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        // Non-conflicting pending reservation
        $res3 = Reservation::factory()->create([
            'user_id' => $user2->id,
            'facility_id' => $facility->id,
            'date' => $targetDate,
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reservations.approve', $res1));

        $response->assertRedirect(route('staff.reservations.index'));

        // res1 approved
        $res1->refresh();
        $this->assertEquals(ReservationStatus::Disetujui, $res1->status);
        $this->assertEquals($petugas->id, $res1->approved_by_id);
        $this->assertNotNull($res1->approved_at);

        // res2 auto-rejected
        $res2->refresh();
        $this->assertEquals(ReservationStatus::Ditolak, $res2->status);
        $this->assertStringContainsString($res1->reservation_number, $res2->rejection_reason);

        // res3 remains pending
        $res3->refresh();
        $this->assertEquals(ReservationStatus::Menunggu, $res3->status);
    }

    public function test_approval_fails_when_facility_is_in_maintenance_or_inactive(): void
    {
        $petugas = User::factory()->petugas()->create();
        $facility = Facility::factory()->create([
            'is_active' => true,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $res = Reservation::factory()->create([
            'facility_id' => $facility->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $this->actingAs($petugas)
            ->from(route('staff.reservations.show', $res))
            ->post(route('staff.reservations.approve', $res))
            ->assertRedirect(route('staff.reservations.show', $res))
            ->assertSessionHasErrors('approve');

        $this->assertEquals(ReservationStatus::Menunggu, $res->fresh()->status);
    }

    public function test_petugas_can_reject_pending_reservation_with_reason(): void
    {
        $petugas = User::factory()->petugas()->create();
        $res = Reservation::factory()->create([
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reservations.reject', $res), [
                'rejection_reason' => 'Ruangan akan disterilisasi untuk persiapan akreditasi.',
            ]);

        $response->assertRedirect(route('staff.reservations.index'));

        $res->refresh();
        $this->assertEquals(ReservationStatus::Ditolak, $res->status);
        $this->assertEquals('Ruangan akan disterilisasi untuk persiapan akreditasi.', $res->rejection_reason);
        $this->assertEquals($petugas->id, $res->rejected_by_id);
        $this->assertNotNull($res->rejected_at);
    }

    public function test_rejection_fails_without_reason(): void
    {
        $petugas = User::factory()->petugas()->create();
        $res = Reservation::factory()->create([
            'date' => now()->addDays(2)->toDateString(),
            'status' => ReservationStatus::Menunggu,
        ]);

        $this->actingAs($petugas)
            ->post(route('staff.reservations.reject', $res), [
                'rejection_reason' => '',
            ])
            ->assertSessionHasErrors('rejection_reason');
    }

    public function test_petugas_can_cancel_urgent_approved_reservation_before_start_time(): void
    {
        $petugas = User::factory()->petugas()->create();
        $res = Reservation::factory()->create([
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => ReservationStatus::Disetujui,
        ]);

        $response = $this->actingAs($petugas)
            ->post(route('staff.reservations.cancel', $res), [
                'cancellation_reason' => 'Terjadi kebocoran pipa air mendadak di atas panggung.',
            ]);

        $response->assertRedirect(route('staff.reservations.index'));

        $res->refresh();
        $this->assertEquals(ReservationStatus::DibatalkanOlehPetugas, $res->status);
        $this->assertEquals('Terjadi kebocoran pipa air mendadak di atas panggung.', $res->cancellation_reason);
        $this->assertEquals($petugas->id, $res->cancelled_by_id);
        $this->assertNotNull($res->cancelled_at);
    }

    public function test_urgent_cancellation_fails_for_pending_or_already_cancelled_reservation(): void
    {
        $petugas = User::factory()->petugas()->create();
        $res = Reservation::factory()->create([
            'date' => now()->addDays(2)->toDateString(),
            'status' => ReservationStatus::Menunggu,
        ]);

        $this->actingAs($petugas)
            ->from(route('staff.reservations.show', $res))
            ->post(route('staff.reservations.cancel', $res), [
                'cancellation_reason' => 'Alasan darurat',
            ])
            ->assertRedirect(route('staff.reservations.show', $res))
            ->assertSessionHasErrors('cancel');
    }
}
