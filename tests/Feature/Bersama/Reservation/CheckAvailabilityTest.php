<?php

namespace Tests\Feature\Bersama\Reservation;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckAvailabilityTest extends TestCase
{
    use DatabaseTransactions;

    private Facility $facility;

    private string $tomorrowDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tomorrowDate = CarbonImmutable::now('Asia/Jakarta')->addDays(2)->format('Y-m-d');

        $this->facility = Facility::factory()->create([
            'name' => 'Aula Serbaguna Utama',
            'type' => FacilityType::Aula,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'is_active' => true,
        ]);
    }

    public function test_can_check_and_receive_available_response(): void
    {
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        $response->assertOk();
        $response->assertSee('Jadwal Tersedia!');
        $response->assertSee('Aula Serbaguna Utama');
    }

    public function test_available_response_accepts_operational_boundary_times(): void
    {
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '07:00',
            'end_time' => '20:00',
        ]);

        $response->assertOk()
            ->assertSee('Jadwal Tersedia!')
            ->assertSee('07:00 - 20:00 WIB');
    }

    public function test_conflict_returns_unavailable_response(): void
    {
        Reservation::factory()->approved()->create([
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
        ]);

        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '13:30',
            'end_time' => '16:00',
        ]);

        $response->assertOk();
        $response->assertSee('Jadwal Tidak Tersedia');
    }

    public function test_facility_in_repair_returns_unavailable_response(): void
    {
        $repairFacility = Facility::factory()->inRepair()->create([
            'name' => 'Aula Sedang Diperbaiki',
            'is_active' => true,
        ]);

        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $repairFacility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        $response->assertOk()
            ->assertSee('Jadwal Tidak Tersedia');
    }

    public function test_inactive_facility_is_rejected_before_availability_check(): void
    {
        $inactiveFacility = Facility::factory()->inactive()->create();

        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $inactiveFacility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('facility_id');
    }

    public function test_validation_rejects_times_outside_operational_hours(): void
    {
        // 06:30 sebelum jam 07:00
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '06:30',
            'end_time' => '08:00',
        ]);

        $response->assertSessionHasErrors(['start_time']);

        // 20:30 setelah jam 20:00
        $response2 = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '18:00',
            'end_time' => '20:30',
        ]);

        $response2->assertSessionHasErrors(['end_time']);
    }

    public function test_validation_rejects_non_half_hour_slots(): void
    {
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '08:15',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    public function test_validation_rejects_end_time_not_after_start_time(): void
    {
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => $this->tomorrowDate,
            'start_time' => '10:00',
            'end_time' => '09:00',
        ]);

        $response->assertSessionHasErrors(['end_time']);
    }

    public function test_validation_rejects_past_time_when_date_is_today(): void
    {
        $this->travelTo(now()->setTime(10, 0));

        $today = now()->toDateString();

            $response = $this->post('/reservations/check-availability', [
                'facility_id' => $this->facility->id,
                'date' => $today,
                'start_time' => '07:00',
                'end_time' => '08:00',
            ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    public function test_validation_rejects_date_in_the_past(): void
    {
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $this->facility->id,
            'date' => now()->subDay()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_validation_requires_all_schedule_fields(): void
    {
        $response = $this->post('/reservations/check-availability', []);

        $response->assertSessionHasErrors(['facility_id', 'date', 'start_time', 'end_time']);
    }
}
