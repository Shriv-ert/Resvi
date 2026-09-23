<?php

namespace Tests\Feature\Reservation;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PrivacyEnforcementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_conflict_response_never_exposes_applicant_or_purpose(): void
    {
        $targetDate = CarbonImmutable::now('Asia/Jakarta')->addDays(3)->format('Y-m-d');

        $facility = Facility::factory()->create([
            'name' => 'Ruang Konferensi Senat',
            'type' => FacilityType::Aula,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'is_active' => true,
        ]);

        $confidentialUser = User::factory()->create([
            'name' => 'Prof. Dr. Ir. Rahasia Negara',
            'email' => 'pejabat.rahasia@kampus.ac.id',
        ]);

        // Reservasi bentrok dengan tujuan privat
        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'user_id' => $confidentialUser->id,
            'reservation_number' => 'RSV-CONFIDENTIAL-999',
            'date' => $targetDate,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'purpose' => 'Sidang Dewan Kehormatan Tertutup dan Investigasi Internal',
        ]);

        // Pengunjung mengecek jadwal yang menabrak jam tersebut
        $response = $this->post('/reservations/check-availability', [
            'facility_id' => $facility->id,
            'date' => $targetDate,
            'start_time' => '10:00',
            'end_time' => '11:30',
        ]);

        $response->assertOk();
        $response->assertSee('Jadwal Tidak Tersedia');

        // VERIFIKASI ZERO PII EXPOSURE (BR-PG-08)
        $response->assertDontSee('Prof. Dr. Ir. Rahasia Negara');
        $response->assertDontSee('pejabat.rahasia@kampus.ac.id');
        $response->assertDontSee('RSV-CONFIDENTIAL-999');
        $response->assertDontSee('Sidang Dewan Kehormatan Tertutup dan Investigasi Internal');
    }
}
