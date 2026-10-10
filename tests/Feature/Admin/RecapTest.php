<?php

namespace Tests\Feature\Admin;

use App\Enums\FacilityStatusChangeType;
use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityStatusLog;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecapTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::query()->create([
            'name' => 'Admin PPK',
            'email' => 'admin.'.Str::uuid().'@ppk.test',
            'password' => 'PPK-Admin-2026!',
            'role' => UserRole::Admin,
            'user_category' => null,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_recaps(): void
    {
        $response = $this->get(route('admin.recaps.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_recaps(): void
    {
        $pengguna = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $response = $this->actingAs($pengguna)->get(route('admin.recaps.index'));

        $response->assertForbidden();
    }

    public function test_petugas_cannot_access_recaps(): void
    {
        $petugas = User::query()->create([
            'name' => 'Petugas Test',
            'email' => 'petugas@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        $response = $this->actingAs($petugas)->get(route('admin.recaps.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_default_recaps_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index'));

        $response->assertOk();
        $response->assertSee('Rekapitulasi Operasional & Okupansi');
        $response->assertSee('Unduh Laporan');
        $response->assertSee('Tingkat Okupansi');
        $response->assertSee('Frekuensi Kerusakan');
    }

    public function test_accurate_monthly_occupancy_calculation(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Ruang Kuliah 101',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung A',
            'capacity' => 40,
            'description' => 'Ruang kelas',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Initial setup',
            'changed_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $user = User::query()->create([
            'name' => 'Pengguna Reservasi',
            'email' => 'pemohon@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        // September 2026 has 30 days -> 30 * 13 = 390.0 hours capacity
        // Reservation 1: 08:00 - 12:00 = 4.0 hours
        Reservation::query()->create([
            'reservation_number' => 'RES-20260901-0001',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => '2026-09-10',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'purpose' => 'Kuliah Umum',
            'status' => ReservationStatus::Selesai,
        ]);

        // Reservation 2: 13:00 - 15:30 = 2.5 hours
        Reservation::query()->create([
            'reservation_number' => 'RES-20260901-0002',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => '2026-09-15',
            'start_time' => '13:00:00',
            'end_time' => '15:30:00',
            'purpose' => 'Presentasi Tugas',
            'status' => ReservationStatus::Selesai,
        ]);

        // Total used: 6.5 hours. Rate = (6.5 / 390) * 100 = 1.67%
        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('Ruang Kuliah 101');
        $response->assertSee('6,50 Jam');
        $response->assertSee('390,00 Jam');
        $response->assertSee('1,67%');
    }

    public function test_non_completed_reservations_are_excluded_from_occupancy(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lab Fisika Modern',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung MIPA',
            'capacity' => 20,
            'description' => 'Lab fisika',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $facility->created_at = Carbon::parse('2026-01-01 00:00:00');
        $facility->saveQuietly();

        $user = User::query()->create([
            'name' => 'Mahasiswa Fisika',
            'email' => 'mhs.fisika@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        // Reservations with non-selesai statuses
        $nonCompletedStatuses = [
            ReservationStatus::Menunggu,
            ReservationStatus::Disetujui,
            ReservationStatus::Berlangsung,
            ReservationStatus::Ditolak,
            ReservationStatus::DibatalkanOlehPengguna,
            ReservationStatus::Kedaluwarsa,
        ];

        $counter = 1;
        foreach ($nonCompletedStatuses as $status) {
            Reservation::query()->create([
                'reservation_number' => sprintf('RES-20260902-%04d', $counter++),
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'date' => '2026-09-12',
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'purpose' => 'Aktivitas status: '.$status->value,
                'status' => $status,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('Lab Fisika Modern');
        $response->assertSee('0,00 Jam');
        $response->assertSee('0,00%');
    }

    public function test_reservations_outside_period_are_excluded(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Aula Gedung Rektorat',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Rektorat',
            'capacity' => 200,
            'description' => 'Aula pertemuan',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $facility->created_at = Carbon::parse('2026-01-01 00:00:00');
        $facility->saveQuietly();

        $user = User::query()->create([
            'name' => 'Dosen Pemohon',
            'email' => 'dosen.pemohon@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);

        // Completed reservation in August (month 8)
        Reservation::query()->create([
            'reservation_number' => 'RES-20260810-0001',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => '2026-08-10',
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'purpose' => 'Seminar Agustus',
            'status' => ReservationStatus::Selesai,
        ]);

        // Query September (month 9)
        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('0,00 Jam');
        $response->assertSee('0,00%');
    }

    public function test_zero_denominator_displays_not_calculable(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Gedung Serbaguna Nonaktif',
            'type' => FacilityType::Aula,
            'location' => 'Area Barat Kampus',
            'capacity' => 500,
            'description' => 'Gedung nonaktif penuh',
            'is_active' => false,
            'operational_status' => OperationalStatus::DalamPerbaikan,
            'created_at' => Carbon::parse('2025-12-01 00:00:00'),
        ]);

        // Deactivated before month 9
        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '1',
            'new_value' => '0',
            'reason' => 'Renovasi menyeluruh',
            'changed_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('Gedung Serbaguna Nonaktif');
        $response->assertSee('Tidak dapat dihitung');
    }

    public function test_location_occupancy_aggregation(): void
    {
        // 2 facilities in "Gedung Kuliah Bersama"
        $facility1 = Facility::query()->create([
            'name' => 'Ruang GKB 101',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Kuliah Bersama',
            'capacity' => 30,
            'description' => 'Kelas 1',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility1->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Pendaftaran fasilitas baru',
            'changed_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $facility2 = Facility::query()->create([
            'name' => 'Ruang GKB 102',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Kuliah Bersama',
            'capacity' => 30,
            'description' => 'Kelas 2',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility2->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Pendaftaran fasilitas baru',
            'changed_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $user = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa.gkb@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        // Facility 1: 10 hours
        Reservation::query()->create([
            'reservation_number' => 'RES-20260905-0001',
            'user_id' => $user->id,
            'facility_id' => $facility1->id,
            'date' => '2026-09-05',
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'purpose' => 'Workshop IT',
            'status' => ReservationStatus::Selesai,
        ]);

        // Facility 2: 20 hours (two 10-hour days)
        Reservation::query()->create([
            'reservation_number' => 'RES-20260906-0002',
            'user_id' => $user->id,
            'facility_id' => $facility2->id,
            'date' => '2026-09-06',
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'purpose' => 'Bootcamp Day 1',
            'status' => ReservationStatus::Selesai,
        ]);
        Reservation::query()->create([
            'reservation_number' => 'RES-20260907-0003',
            'user_id' => $user->id,
            'facility_id' => $facility2->id,
            'date' => '2026-09-07',
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'purpose' => 'Bootcamp Day 2',
            'status' => ReservationStatus::Selesai,
        ]);

        // Total used = 30.0 hours. Total capacity = 2 * 390 = 780.0 hours.
        // Rate = (30 / 780) * 100 = 3.85%
        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'location',
        ]));

        $response->assertOk();
        $response->assertSee('Gedung Kuliah Bersama');
        $response->assertSee('2 Fasilitas');
        $response->assertSee('30,00 Jam');
        $response->assertSee('780,00 Jam');
        $response->assertSee('3,85%');
    }

    public function test_damage_frequency_counts_operational_transitions(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lab Elektronika',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Teknik Lt. 1',
            'capacity' => 25,
            'description' => 'Lab solder & osiloskop',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        // Incident 1 in September 2026
        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::OperationalStatus,
            'old_value' => OperationalStatus::DapatDigunakan->value,
            'new_value' => OperationalStatus::DalamPerbaikan->value,
            'reason' => 'Osiloskop short circuit',
            'changed_at' => Carbon::parse('2026-09-08 10:15:00'),
        ]);

        // Incident 2 in September 2026
        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::OperationalStatus,
            'old_value' => OperationalStatus::DapatDigunakan->value,
            'new_value' => OperationalStatus::DalamPerbaikan->value,
            'reason' => 'AC bocor merusak meja praktikum',
            'changed_at' => Carbon::parse('2026-09-22 14:30:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'damage_frequency',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('Lab Elektronika');
        $response->assertSee('2 Kali');
    }

    public function test_damage_frequency_ignores_unrelated_status_logs(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lapangan Basket',
            'type' => FacilityType::Lapangan,
            'location' => 'Outdoor Sports Center',
            'capacity' => 20,
            'description' => 'Lapangan basket',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        // Status log: repair finished (dalam_perbaikan -> dapat_digunakan) - NOT an incident
        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::OperationalStatus,
            'old_value' => OperationalStatus::DalamPerbaikan->value,
            'new_value' => OperationalStatus::DapatDigunakan->value,
            'reason' => 'Perbaikan ring basket selesai',
            'changed_at' => Carbon::parse('2026-09-10 11:00:00'),
        ]);

        // Status log: master activation/deactivation - NOT an operational incident
        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '1',
            'new_value' => '0',
            'reason' => 'Penonaktifan fasilitas',
            'changed_at' => Carbon::parse('2026-09-12 09:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'damage_frequency',
            'group_by' => 'facility',
        ]));

        $response->assertOk();
        $response->assertSee('Lapangan Basket');
        $response->assertSee('0 Kali (Stabil)');
    }

    public function test_damage_frequency_location_aggregation(): void
    {
        $facility1 = Facility::query()->create([
            'name' => 'Studio Desain 1',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Seni Rupa',
            'capacity' => 20,
            'description' => 'Studio 1',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $facility2 = Facility::query()->create([
            'name' => 'Studio Desain 2',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Seni Rupa',
            'capacity' => 20,
            'description' => 'Studio 2',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility1->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::OperationalStatus,
            'old_value' => OperationalStatus::DapatDigunakan->value,
            'new_value' => OperationalStatus::DalamPerbaikan->value,
            'reason' => 'Lampu sorot putus',
            'changed_at' => Carbon::parse('2026-09-04 09:00:00'),
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility2->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::OperationalStatus,
            'old_value' => OperationalStatus::DapatDigunakan->value,
            'new_value' => OperationalStatus::DalamPerbaikan->value,
            'reason' => 'Proyektor rusak',
            'changed_at' => Carbon::parse('2026-09-18 13:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.index', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'damage_frequency',
            'group_by' => 'location',
        ]));

        $response->assertOk();
        $response->assertSee('Gedung Seni Rupa');
        $response->assertSee('2 Fasilitas');
        $response->assertSee('2 Kali');
    }

    public function test_admin_can_export_csv_report(): void
    {
        Facility::query()->create([
            'name' => 'Ruang Teori A',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Utama',
            'capacity' => 35,
            'description' => 'Ruang kelas',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.export', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
            'format' => 'csv',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="rekap_occupancy_facility_september_2026.csv"');
    }

    public function test_admin_can_export_excel_report(): void
    {
        Facility::query()->create([
            'name' => 'Ruang Rapat Senat',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Rektorat Lt. 2',
            'capacity' => 60,
            'description' => 'Ruang rapat senat',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.export', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
            'format' => 'xlsx',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="rekap_occupancy_facility_september_2026.xlsx"');
    }

    public function test_admin_can_export_pdf_report(): void
    {
        Facility::query()->create([
            'name' => 'Lab Jaringan',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung TI Lt. 3',
            'capacity' => 30,
            'description' => 'Lab TI',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.recaps.export', [
            'month' => 9,
            'year' => 2026,
            'metric' => 'occupancy',
            'group_by' => 'facility',
            'format' => 'pdf',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
