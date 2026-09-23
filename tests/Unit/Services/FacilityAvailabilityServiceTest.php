<?php

namespace Tests\Unit\Services;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\FacilityAvailabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FacilityAvailabilityServiceTest extends TestCase
{
    use DatabaseTransactions;

    private FacilityAvailabilityService $service;

    private Facility $facility;

    private string $testDate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FacilityAvailabilityService();
        $this->testDate = '2026-10-15';

        $this->facility = Facility::factory()->create([
            'name' => 'Ruang Teater Komputer',
            'type' => FacilityType::Laboratorium,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'is_active' => true,
        ]);

        // Buat reservasi terkunci (disetujui) jam 09:00:00 s.d. 11:30:00
        Reservation::factory()->approved()->create([
            'facility_id' => $this->facility->id,
            'date' => $this->testDate,
            'start_time' => '09:00:00',
            'end_time' => '11:30:00',
        ]);
    }

    public function test_slot_immediately_before_locked_schedule_is_available(): void
    {
        // 07:30 s.d. 09:00 bersebelahan persis di batas mulai 09:00 -> HARUS TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '07:30', '09:00');

        $this->assertTrue($result->isAvailable);
    }

    public function test_slot_immediately_after_locked_schedule_is_available(): void
    {
        // 11:30 s.d. 13:00 bersebelahan persis di batas akhir 11:30 -> HARUS TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '11:30', '13:00');

        $this->assertTrue($result->isAvailable);
    }

    public function test_slot_overlapping_start_boundary_is_not_available(): void
    {
        // 08:30 s.d. 09:30 menabrak awal jadwal (09:00-11:30) -> TIDAK TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '08:30', '09:30');

        $this->assertFalse($result->isAvailable);
        $this->assertSame('bentrok', $result->reason);
    }

    public function test_slot_inside_locked_schedule_is_not_available(): void
    {
        // 09:30 s.d. 10:30 berada persis di dalam jadwal (09:00-11:30) -> TIDAK TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '09:30', '10:30');

        $this->assertFalse($result->isAvailable);
        $this->assertSame('bentrok', $result->reason);
    }

    public function test_slot_overlapping_end_boundary_is_not_available(): void
    {
        // 11:00 s.d. 12:30 menabrak akhir jadwal (09:00-11:30) -> TIDAK TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '11:00', '12:30');

        $this->assertFalse($result->isAvailable);
        $this->assertSame('bentrok', $result->reason);
    }

    public function test_pending_status_reservation_does_not_lock_schedule(): void
    {
        // Buat reservasi dengan status menunggu pada 14:00 s.d. 16:00
        Reservation::factory()->create([
            'facility_id' => $this->facility->id,
            'date' => $this->testDate,
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'status' => ReservationStatus::Menunggu,
        ]);

        // Cek pada jam yang sama (14:00 - 16:00) -> Status Menunggu TIDAK MENGUNCI (BR-PG-05) -> HARUS TERSEDIA
        $result = $this->service->check($this->facility, $this->testDate, '14:00', '16:00');

        $this->assertTrue($result->isAvailable);
    }

    public function test_facility_in_repair_is_never_available(): void
    {
        $repairFacility = Facility::factory()->inRepair()->create([
            'name' => 'Lab Rusak Total',
            'is_active' => true,
        ]);

        $result = $this->service->check($repairFacility, $this->testDate, '14:00', '16:00');

        $this->assertFalse($result->isAvailable);
        $this->assertSame('dalam_perbaikan', $result->reason);
    }
}
