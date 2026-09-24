<?php

namespace Tests\Feature\Admin;

use App\Enums\FacilityStatusChangeType;
use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Enums\ReservationStatus;
use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use App\Models\FacilityStatusLog;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FacilityTest extends TestCase
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

    public function test_guest_is_redirected_to_login_when_accessing_admin_facilities(): void
    {
        $response = $this->get(route('admin.facilities.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_admin_facilities(): void
    {
        $pengguna = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $response = $this->actingAs($pengguna)->get(route('admin.facilities.index'));

        $response->assertForbidden();
    }

    public function test_petugas_cannot_access_admin_facilities(): void
    {
        $petugas = User::query()->create([
            'name' => 'Petugas Test',
            'email' => 'petugas@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);

        $response = $this->actingAs($petugas)->get(route('admin.facilities.index'));

        $response->assertForbidden();
    }

    public function test_admin_root_redirects_to_facilities_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertRedirect(route('admin.facilities.index'));
    }

    public function test_admin_can_view_facilities_directory_with_active_and_inactive_items(): void
    {
        Facility::query()->create([
            'name' => 'Ruang Teori 101',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Kuliah Bersama Lt. 1',
            'capacity' => 40,
            'description' => 'Ruang kelas ber-AC',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        Facility::query()->create([
            'name' => 'Aula Nusantara',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Rektorat Lt. 3',
            'capacity' => 500,
            'description' => 'Aula pertemuan besar',
            'is_active' => false,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index'));

        $response->assertOk();
        $response->assertSee('Ruang Teori 101');
        $response->assertSee('Aula Nusantara');
        $response->assertSee('Aktif');
        $response->assertSee('Nonaktif');
        $response->assertSee('Dapat digunakan');
        $response->assertSee('Dalam perbaikan');
    }

    public function test_admin_can_filter_facilities_by_type(): void
    {
        Facility::query()->create([
            'name' => 'Ruang Kelas 201',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung A',
            'capacity' => 30,
            'description' => 'Kelas reguler',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        Facility::query()->create([
            'name' => 'Lab Komputer 1',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung B',
            'capacity' => 25,
            'description' => 'Lab PC i7',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index', ['type' => 'kelas']));

        $response->assertOk();
        $response->assertSee('Ruang Kelas 201');
        $response->assertDontSee('Lab Komputer 1');
    }

    public function test_admin_can_filter_facilities_by_master_status(): void
    {
        Facility::query()->create([
            'name' => 'Fasilitas Aktif',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung A',
            'capacity' => 30,
            'description' => 'Aktif',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        Facility::query()->create([
            'name' => 'Fasilitas Nonaktif',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung B',
            'capacity' => 30,
            'description' => 'Nonaktif',
            'is_active' => false,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index', ['master_status' => 'inactive']));

        $response->assertOk();
        $response->assertSee('Fasilitas Nonaktif');
        $response->assertDontSee('Fasilitas Aktif');
    }

    public function test_admin_can_filter_facilities_by_operational_status(): void
    {
        Facility::query()->create([
            'name' => 'Fasilitas Siap Pakai',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung A',
            'capacity' => 30,
            'description' => 'Siap',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        Facility::query()->create([
            'name' => 'Fasilitas Rusak',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung B',
            'capacity' => 30,
            'description' => 'Rusak',
            'is_active' => true,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index', ['operational_status' => 'dalam_perbaikan']));

        $response->assertOk();
        $response->assertSee('Fasilitas Rusak');
        $response->assertDontSee('Fasilitas Siap Pakai');
    }

    public function test_admin_can_search_facilities_by_name_or_location(): void
    {
        Facility::query()->create([
            'name' => 'Ruang Multimedia',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Sayap Timur',
            'capacity' => 35,
            'description' => 'Lab Multimedia',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        Facility::query()->create([
            'name' => 'Lapangan Tenis',
            'type' => FacilityType::Lapangan,
            'location' => 'Area Olahraga Outdoor',
            'capacity' => 10,
            'description' => 'Lapangan semen',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.index', ['search' => 'Multimedia']));

        $response->assertOk();
        $response->assertSee('Ruang Multimedia');
        $response->assertDontSee('Lapangan Tenis');

        $locationSearch = $this->actingAs($this->admin)->get(route('admin.facilities.index', ['search' => 'Outdoor']));

        $locationSearch->assertOk();
        $locationSearch->assertSee('Lapangan Tenis');
        $locationSearch->assertDontSee('Ruang Multimedia');
    }

    public function test_admin_can_view_create_facility_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.facilities.create'));

        $response->assertOk();
        $response->assertSee('Tambah Fasilitas Baru');
        $response->assertSee('Daftarkan Fasilitas');
    }

    public function test_admin_can_create_facility_with_default_active_and_operational_statuses(): void
    {
        $payload = [
            'name' => 'Ruang Kuliah 305',
            'type' => 'kelas',
            'location' => 'Gedung C Lt. 3',
            'capacity' => 45,
            'description' => 'Ruang kelas dengan proyektor interaktif dan whiteboard.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), $payload);

        $response->assertRedirect(route('admin.facilities.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('facilities', [
            'name' => 'Ruang Kuliah 305',
            'type' => 'kelas',
            'location' => 'Gedung C Lt. 3',
            'capacity' => 45,
            'is_active' => true,
            'operational_status' => 'dapat_digunakan',
        ]);

        $facility = Facility::query()->where('name', 'Ruang Kuliah 305')->firstOrFail();

        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive->value,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Pendaftaran fasilitas baru',
        ]);
    }

    public function test_admin_can_create_facility_with_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('classroom.jpg', 100, 'image/jpeg');

        $payload = [
            'name' => 'Studio Gambar Arsitektur',
            'type' => 'laboratorium',
            'location' => 'Gedung Teknik Lt. 2',
            'capacity' => 25,
            'description' => 'Studio gambar lengkap dengan meja arsitek.',
            'photo' => $file,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), $payload);

        $response->assertRedirect(route('admin.facilities.index'));

        $facility = Facility::query()->where('name', 'Studio Gambar Arsitektur')->firstOrFail();

        $this->assertDatabaseHas('facility_photos', [
            'facility_id' => $facility->id,
            'is_primary' => true,
        ]);

        $photo = FacilityPhoto::query()->where('facility_id', $facility->id)->firstOrFail();
        Storage::disk('public')->assertExists($photo->photo_path);
    }

    public function test_admin_can_create_tool_with_null_capacity(): void
    {
        $payload = [
            'name' => 'Proyektor Epson EB-X450 Unit 1',
            'type' => 'alat',
            'location' => 'Ruang Perlengkapan BAAK',
            'capacity' => null,
            'description' => 'Proyektor portable 3600 ANSI lumens.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), $payload);

        $response->assertRedirect(route('admin.facilities.index'));

        $this->assertDatabaseHas('facilities', [
            'name' => 'Proyektor Epson EB-X450 Unit 1',
            'type' => 'alat',
            'capacity' => null,
            'is_active' => true,
        ]);
    }

    public function test_facility_creation_fails_when_non_tool_has_invalid_capacity(): void
    {
        $payload = [
            'name' => 'Ruang Seminar Tanpa Kapasitas',
            'type' => 'aula',
            'location' => 'Gedung A',
            'capacity' => 0,
            'description' => 'Deskripsi aula',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), $payload);

        $response->assertSessionHasErrors('capacity');
        $this->assertDatabaseMissing('facilities', ['name' => 'Ruang Seminar Tanpa Kapasitas']);
    }

    public function test_facility_creation_fails_with_missing_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), [
            'name' => '',
            'type' => '',
            'location' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'type', 'location', 'description']);
    }

    public function test_admin_can_view_facility_detail_page(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lab Jaringan Komputer',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Lab Lt. 3',
            'capacity' => 30,
            'description' => 'Dilengkapi dengan 30 PC dan rack Cisco switch.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        FacilityStatusLog::query()->create([
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Pendaftaran fasilitas baru',
            'changed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.show', $facility));

        $response->assertOk();
        $response->assertSee('Lab Jaringan Komputer');
        $response->assertSee('Gedung Lab Lt. 3');
        $response->assertSee('Pendaftaran fasilitas baru');
        $response->assertSee('Admin PPK');
    }

    public function test_admin_can_view_edit_facility_page(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Ruang Diskusi 1',
            'type' => FacilityType::Kelas,
            'location' => 'Perpustakaan Lt. 2',
            'capacity' => 8,
            'description' => 'Ruang rapat kelompok kecil.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.facilities.edit', $facility));

        $response->assertOk();
        $response->assertSee('Ubah Data Master Fasilitas');
        $response->assertSee('Ruang Diskusi 1');
    }

    public function test_admin_can_update_facility_master_data_without_altering_operational_status(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lab Kimia Dasar',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung MIPA Lt. 1',
            'capacity' => 20,
            'description' => 'Lab sedang dalam masa perbaikan wastafel.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $payload = [
            'name' => 'Lab Kimia Dasar & Terapan',
            'type' => 'laboratorium',
            'location' => 'Gedung MIPA Sayap Barat Lt. 1',
            'capacity' => 24,
            'description' => 'Deskripsi diperbarui.',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.facilities.update', $facility), $payload);

        $response->assertRedirect(route('admin.facilities.show', $facility));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('facilities', [
            'id' => $facility->id,
            'name' => 'Lab Kimia Dasar & Terapan',
            'location' => 'Gedung MIPA Sayap Barat Lt. 1',
            'capacity' => 24,
            'operational_status' => 'dalam_perbaikan',
        ]);
    }

    public function test_admin_can_update_facility_photo(): void
    {
        Storage::fake('public');

        $facility = Facility::query()->create([
            'name' => 'Lapangan Futsal',
            'type' => FacilityType::Lapangan,
            'location' => 'Outdoor Kompleks Olahraga',
            'capacity' => 20,
            'description' => 'Lapangan futsal rumput sintetis.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $initialFile = UploadedFile::fake()->create('old_photo.jpg', 50, 'image/jpeg');
        $oldPath = $initialFile->store('facilities', 'public');

        $photoRecord = FacilityPhoto::query()->create([
            'facility_id' => $facility->id,
            'photo_path' => $oldPath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $newFile = UploadedFile::fake()->create('new_photo.jpg', 50, 'image/jpeg');

        $payload = [
            'name' => 'Lapangan Futsal Arena',
            'type' => 'lapangan',
            'location' => 'Outdoor Kompleks Olahraga',
            'capacity' => 20,
            'description' => 'Lapangan futsal rumput sintetis baru.',
            'photo' => $newFile,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.facilities.update', $facility), $payload);

        $response->assertRedirect(route('admin.facilities.show', $facility));

        $photoRecord->refresh();
        $this->assertNotEquals($oldPath, $photoRecord->photo_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($photoRecord->photo_path);
    }

    public function test_deactivation_is_prevented_when_approved_or_ongoing_reservation_exists(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Aula Garuda',
            'type' => FacilityType::Aula,
            'location' => 'Gedung Utama Lt. 2',
            'capacity' => 300,
            'description' => 'Aula representatif.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $user = User::query()->create([
            'name' => 'Dosen Pemohon',
            'email' => 'dosen@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);

        Reservation::query()->create([
            'reservation_number' => 'RES-20260923-0001',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Seminar Nasional PPK',
            'status' => ReservationStatus::Disetujui,
            'approved_by_id' => $this->admin->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.deactivate', $facility), [
            'reason' => 'Perbaikan struktur atap',
        ]);

        $response->assertRedirect(route('admin.facilities.show', $facility));
        $response->assertSessionHas('error');

        $facility->refresh();
        $this->assertTrue($facility->is_active);
    }

    public function test_deactivation_succeeds_and_auto_rejects_future_pending_reservations(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Ruang Presentasi 102',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung D Lt. 1',
            'capacity' => 40,
            'description' => 'Ruang presentasi multimedia.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $user = User::query()->create([
            'name' => 'Mahasiswa Pemohon',
            'email' => 'mahasiswa.pemohon@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $futureReservation = Reservation::query()->create([
            'reservation_number' => 'RES-20260923-0002',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'purpose' => 'Latihan Sidang Skripsi',
            'status' => ReservationStatus::Menunggu,
        ]);

        $deactivationReason = 'Renovasi interior lantai dan plafon gedung';

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.deactivate', $facility), [
            'reason' => $deactivationReason,
        ]);

        $response->assertRedirect(route('admin.facilities.show', $facility));
        $response->assertSessionHas('status');

        $facility->refresh();
        $this->assertFalse($facility->is_active);

        $futureReservation->refresh();
        $this->assertEquals(ReservationStatus::Ditolak, $futureReservation->status);
        $this->assertStringContainsString($deactivationReason, $futureReservation->rejection_reason);
        $this->assertEquals($this->admin->id, $futureReservation->rejected_by_id);

        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive->value,
            'old_value' => '1',
            'new_value' => '0',
            'reason' => $deactivationReason,
        ]);
    }

    public function test_deactivation_marks_past_pending_reservations_as_expired(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Ruang Kuliah Lama',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Lama',
            'capacity' => 30,
            'description' => 'Ruang lama.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $user = User::query()->create([
            'name' => 'Mahasiswa Pemohon 2',
            'email' => 'mhs2@ppk.test',
            'password' => 'secret1234',
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
        ]);

        $pastReservation = Reservation::query()->create([
            'reservation_number' => 'RES-20260920-0003',
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'date' => Carbon::yesterday()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Kegiatan yang sudah lewat waktu',
            'status' => ReservationStatus::Menunggu,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.deactivate', $facility), [
            'reason' => 'Gedung dialihfungsikan',
        ]);

        $response->assertRedirect(route('admin.facilities.show', $facility));

        $pastReservation->refresh();
        $this->assertEquals(ReservationStatus::Kedaluwarsa, $pastReservation->status);
        $this->assertNotNull($pastReservation->expiration_reason);
    }

    public function test_deactivation_requires_reason(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Ruang Rapat 2',
            'type' => FacilityType::Kelas,
            'location' => 'Gedung Rektorat',
            'capacity' => 15,
            'description' => 'Ruang rapat.',
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.deactivate', $facility), [
            'reason' => '',
        ]);

        $response->assertSessionHasErrors('reason');
        $facility->refresh();
        $this->assertTrue($facility->is_active);
    }

    public function test_reactivation_succeeds_and_preserves_operational_status(): void
    {
        $facility = Facility::query()->create([
            'name' => 'Lab Instrumentasi Fisika',
            'type' => FacilityType::Laboratorium,
            'location' => 'Gedung Fisika Lt. 1',
            'capacity' => 20,
            'description' => 'Lab fisika lanjutan.',
            'is_active' => false,
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.facilities.reactivate', $facility));

        $response->assertRedirect(route('admin.facilities.show', $facility));
        $response->assertSessionHas('status');

        $facility->refresh();
        $this->assertTrue($facility->is_active);
        $this->assertEquals(OperationalStatus::DalamPerbaikan, $facility->operational_status);

        $this->assertDatabaseHas('facility_status_logs', [
            'facility_id' => $facility->id,
            'changed_by_id' => $this->admin->id,
            'change_type' => FacilityStatusChangeType::IsActive->value,
            'old_value' => '0',
            'new_value' => '1',
            'reason' => 'Fasilitas diaktifkan kembali oleh administrator sistem.',
        ]);
    }
}
