<?php

namespace Tests\Feature\Bersama\Reservation;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\StaffReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeBasedStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_time_based_statuses_change_at_exact_boundaries_and_leave_future_and_final_records_unchanged(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 10)->setTime(9, 0));
        $user = \App\Models\User::factory()->create();
        $facility = \App\Models\Facility::factory()->create();
        $today = now()->toDateString();

        $pendingAtStart = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => $today,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);
        $pendingBeforeStart = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => $today,
            'start_time' => '09:30:00',
            'end_time' => '10:30:00',
            'status' => ReservationStatus::Menunggu,
        ]);
        $approvedAtStart = Reservation::factory()->approved()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => $today,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);
        $ongoingAtEnd = Reservation::factory()->inProgress()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => $today,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
        ]);
        $finalReservation = Reservation::factory()->rejected()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => $today,
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
        ]);

        app(StaffReservationService::class)->syncTimeBasedStatuses();

        $this->assertSame(ReservationStatus::Kedaluwarsa, $pendingAtStart->fresh()->status);
        $this->assertNotEmpty($pendingAtStart->fresh()->expiration_reason);
        $this->assertSame(ReservationStatus::Menunggu, $pendingBeforeStart->fresh()->status);
        $this->assertSame(ReservationStatus::Berlangsung, $approvedAtStart->fresh()->status);
        $this->assertSame(ReservationStatus::Selesai, $ongoingAtEnd->fresh()->status);
        $this->assertSame(ReservationStatus::Ditolak, $finalReservation->fresh()->status);
    }
}
