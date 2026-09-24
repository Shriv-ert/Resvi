<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FacilityController as PublicFacilityController;
use App\Http\Controllers\ReservationFlowController;
use App\Http\Controllers\Staff\FacilityController as StaffFacilityController;
use App\Http\Controllers\Staff\ReportController as StaffReportController;
use App\Http\Controllers\Staff\ReservationController as StaffReservationController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\ReservationController as UserReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Comprehensive Mock Repository for Pure UI Presentation (Staff, User Mock Prototype)
if (! class_exists('MockData')) {
class MockData
{
    public static function facilities()
    {
        return [
            [
                'id' => 'fac-01',
                'name' => 'Ruang Kuliah Teori A-101',
                'type' => 'kelas',
                'location' => 'Gedung A (Utama)',
                'capacity' => 45,
                'description' => 'Ruang kuliah ber-AC dilengkapi proyektor laser resolusi tinggi, whiteboard magnetik, 45 kursi kuliah ergonomis, serta koneksi internet kampus.',
                'operational_status' => 'dapat_digunakan',
                'is_active' => true,
                'photo' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 'fac-02',
                'name' => 'Laboratorium Komputer & AI 301',
                'type' => 'laboratorium',
                'location' => 'Gedung B (Laboratorium)',
                'capacity' => 40,
                'description' => 'Laboratorium komputasi tingkat lanjut dengan 40 PC workstation berspesifikasi GPU tinggi, LAN Gigabit, AC sentral, dan sistem audio terpadu.',
                'operational_status' => 'dapat_digunakan',
                'is_active' => true,
                'photo' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 'fac-03',
                'name' => 'Aula Utama Sasana Krida Kampus',
                'type' => 'aula',
                'location' => 'Gedung A (Utama)',
                'capacity' => 350,
                'description' => 'Aula pertemuan serbaguna untuk seminar nasional, workshop, dan wisuda dengan panggung utama, sound system 10.000 watt, dan lighting panggung.',
                'operational_status' => 'dapat_digunakan',
                'is_active' => true,
                'photo' => 'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 'fac-04',
                'name' => 'Proyektor Cinema & Sound Portabel',
                'type' => 'alat',
                'location' => 'Gedung B (Laboratorium)',
                'capacity' => null,
                'description' => 'Paket proyektor 5.000 lumens dan sistem pengeras suara wireless yang menetap di ruang audiovisual untuk kegiatan presentasi khusus.',
                'operational_status' => 'dalam_perbaikan',
                'is_active' => true,
                'photo' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&auto=format&fit=crop&q=80',
                'maintenance_reason' => 'Penggantian lampu optik dan kalibrasi lensa fokus resolusi 4K oleh vendor resmi.',
                'maintenance_date' => '2026-09-22 14:30',
                'maintenance_by' => 'Budi Santoso (Petugas)',
            ],
            [
                'id' => 'fac-05',
                'name' => 'Lapangan Futsal & Basket Outdoor',
                'type' => 'lapangan',
                'location' => 'Area Terbuka Kampus',
                'capacity' => 50,
                'description' => 'Lapangan olahraga multifungsi berstandar nasional dengan permukaan interlock modern, jaring pembatas, dan lampu sorot penerangan malam.',
                'operational_status' => 'dapat_digunakan',
                'is_active' => true,
                'photo' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800&auto=format&fit=crop&q=80',
            ],
            [
                'id' => 'fac-06',
                'name' => 'Ruang Seminar Eksekutif C-204',
                'type' => 'kelas',
                'location' => 'Gedung C (Fakultas)',
                'capacity' => 30,
                'description' => 'Ruang rapat dan seminar model U-shape dengan mikrofon meja individual, smart TV interaktif 75 inci, dan fasilitas video conference.',
                'operational_status' => 'dapat_digunakan',
                'is_active' => false,
                'photo' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&auto=format&fit=crop&q=80',
            ],
        ];
    }

    public static function reservations()
    {
        return [
            [
                'id' => 'res-01',
                'reservation_number' => 'RSV-2026-0901',
                'applicant_name' => 'Ahmad Fauzi',
                'applicant_category' => 'Mahasiswa',
                'applicant_email' => 'mahasiswa@ppk.test',
                'facility_id' => 'fac-01',
                'facility_name' => 'Ruang Kuliah Teori A-101',
                'facility_type' => 'kelas',
                'facility_location' => 'Gedung A (Utama)',
                'date' => '2026-09-24',
                'start_time' => '08:30',
                'end_time' => '10:30',
                'purpose' => 'Kegiatan bimbingan belajar persiapan UTS mata kuliah Pemrograman Berorientasi Objek bersama Himpunan Mahasiswa.',
                'status' => 'menunggu',
                'created_at' => '2026-09-23 09:15',
                'updated_at' => '2026-09-23 09:15',
            ],
            [
                'id' => 'res-02',
                'reservation_number' => 'RSV-2026-0900',
                'applicant_name' => 'Dr. Hendra Gunawan',
                'applicant_category' => 'Dosen',
                'applicant_email' => 'dosen@ppk.test',
                'facility_id' => 'fac-02',
                'facility_name' => 'Laboratorium Komputer & AI 301',
                'facility_type' => 'laboratorium',
                'facility_location' => 'Gedung B (Laboratorium)',
                'date' => '2026-09-24',
                'start_time' => '13:00',
                'end_time' => '15:30',
                'purpose' => 'Praktikum Pengenalan Deep Learning dan Machine Vision untuk mahasiswa angkatan 2023.',
                'status' => 'disetujui',
                'created_at' => '2026-09-22 11:20',
                'updated_at' => '2026-09-22 14:00',
            ],
            [
                'id' => 'res-03',
                'reservation_number' => 'RSV-2026-0899',
                'applicant_name' => 'Siti Nurhaliza',
                'applicant_category' => 'Mahasiswa',
                'applicant_email' => 'siti@ppk.test',
                'facility_id' => 'fac-03',
                'facility_name' => 'Aula Utama Sasana Krida Kampus',
                'facility_type' => 'aula',
                'facility_location' => 'Gedung A (Utama)',
                'date' => '2026-09-23',
                'start_time' => '14:00',
                'end_time' => '17:00',
                'purpose' => 'Gladi resik persiapan pembukaan Pekan Olahraga Mahasiswa Fakultas.',
                'status' => 'berlangsung',
                'created_at' => '2026-09-21 08:30',
                'updated_at' => '2026-09-23 14:00',
            ],
            [
                'id' => 'res-04',
                'reservation_number' => 'RSV-2026-0880',
                'applicant_name' => 'Ahmad Fauzi',
                'applicant_category' => 'Mahasiswa',
                'applicant_email' => 'mahasiswa@ppk.test',
                'facility_id' => 'fac-05',
                'facility_name' => 'Lapangan Futsal & Basket Outdoor',
                'facility_type' => 'lapangan',
                'facility_location' => 'Area Terbuka Kampus',
                'date' => '2026-09-20',
                'start_time' => '16:00',
                'end_time' => '18:00',
                'purpose' => 'Latihan rutin tim futsal angkatan 2024.',
                'status' => 'selesai',
                'created_at' => '2026-09-18 10:00',
                'updated_at' => '2026-09-20 18:00',
            ],
            [
                'id' => 'res-05',
                'reservation_number' => 'RSV-2026-0875',
                'applicant_name' => 'Rina Kartika',
                'applicant_category' => 'Staf',
                'applicant_email' => 'rina@ppk.test',
                'facility_id' => 'fac-01',
                'facility_name' => 'Ruang Kuliah Teori A-101',
                'facility_type' => 'kelas',
                'facility_location' => 'Gedung A (Utama)',
                'date' => '2026-09-19',
                'start_time' => '09:00',
                'end_time' => '11:00',
                'purpose' => 'Rapat internal koordinasi dosen wali.',
                'status' => 'ditolak',
                'rejection_reason' => 'Jadwal telah terlebih dahulu disetujui untuk permohonan kuliah pengganti dosen bersangkutan.',
                'created_at' => '2026-09-18 09:12',
                'updated_at' => '2026-09-18 13:00',
            ],
            [
                'id' => 'res-06',
                'reservation_number' => 'RSV-2026-0860',
                'applicant_name' => 'Ahmad Fauzi',
                'applicant_category' => 'Mahasiswa',
                'applicant_email' => 'mahasiswa@ppk.test',
                'facility_id' => 'fac-02',
                'facility_name' => 'Laboratorium Komputer & AI 301',
                'facility_type' => 'laboratorium',
                'facility_location' => 'Gedung B (Laboratorium)',
                'date' => '2026-09-15',
                'start_time' => '10:00',
                'end_time' => '12:00',
                'purpose' => 'Diskusi kelompok tugas akhir.',
                'status' => 'dibatalkan_oleh_pengguna',
                'cancellation_reason' => 'Anggota kelompok memiliki jadwal kuis dadakan di kelas lain.',
                'created_at' => '2026-09-14 15:00',
                'updated_at' => '2026-09-15 08:00',
            ],
        ];
    }

    public static function reports()
    {
        return [
            [
                'id' => 'rep-01',
                'report_number' => 'LAP-2026-0301',
                'reporter_name' => 'Ahmad Fauzi',
                'facility_id' => 'fac-01',
                'facility_name' => 'Ruang Kuliah Teori A-101',
                'facility_type' => 'kelas',
                'facility_location' => 'Gedung A (Utama)',
                'category' => 'kerusakan_elektronik',
                'description' => 'Pendingin ruangan (AC) di sisi depan sebelah kiri mengeluarkan bunyi berderik keras dan hembusan angin terasa kurang dingin.',
                'photo_path' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=800&auto=format&fit=crop&q=80',
                'status' => 'baru',
                'created_at' => '2026-09-23 08:30',
                'updated_at' => '2026-09-23 08:30',
            ],
            [
                'id' => 'rep-02',
                'report_number' => 'LAP-2026-0298',
                'reporter_name' => 'Bambang Irawan',
                'facility_id' => 'fac-04',
                'facility_name' => 'Proyektor Cinema & Sound Portabel',
                'facility_type' => 'alat',
                'facility_location' => 'Gedung B (Laboratorium)',
                'category' => 'kerusakan_elektronik',
                'description' => 'Lampu indikator proyektor berkedip merah dan gambar proyektor mati mendadak setelah dinyalakan selama 10 menit.',
                'photo_path' => null,
                'status' => 'diproses',
                'created_at' => '2026-09-22 10:15',
                'updated_at' => '2026-09-22 14:00',
            ],
            [
                'id' => 'rep-03',
                'report_number' => 'LAP-2026-0280',
                'reporter_name' => 'Ahmad Fauzi',
                'facility_id' => 'fac-02',
                'facility_name' => 'Laboratorium Komputer & AI 301',
                'facility_type' => 'laboratorium',
                'facility_location' => 'Gedung B (Laboratorium)',
                'category' => 'kerusakan_fisik',
                'description' => 'Stopkontak di baris PC nomor 12 longgar dan mengeluarkan percikan kecil saat kabel dicolokkan.',
                'photo_path' => null,
                'status' => 'selesai',
                'resolution_notes' => 'Teknisi listrik telah mengganti rumah stopkontak baru dengan standar industri berpelindung grounding.',
                'created_at' => '2026-09-17 14:20',
                'updated_at' => '2026-09-18 10:00',
            ],
        ];
    }
}
}

// -------------------------------------------------------------
// Public & Shared Routes (Pengunjung & Pengguna)
// -------------------------------------------------------------

Route::get('/', [PublicFacilityController::class, 'index'])->name('home');
Route::get('/facilities', [PublicFacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [PublicFacilityController::class, 'show'])->name('facilities.show');

Route::get('/reservations/create', [ReservationFlowController::class, 'create'])->name('reservations.create');
Route::post('/reservations/check-availability', [ReservationFlowController::class, 'checkAvailability'])->name('reservations.check_availability');
Route::post('/reservations/draft', [ReservationFlowController::class, 'storeDraft'])->middleware('guest')->name('reservations.store_draft');

// Auth routes
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// -------------------------------------------------------------
// Pengguna (User) Routes (SRS FR-PGN)
// -------------------------------------------------------------

Route::middleware(['auth', 'role:pengguna'])->prefix('my')->group(function (): void {
    // Riwayat & Pengajuan Reservasi Pengguna
    Route::get('/reservations', [UserReservationController::class, 'index'])->name('user.reservations.index');
    Route::post('/reservations', [UserReservationController::class, 'store'])->name('user.reservations.store');
    Route::get('/reservations/{reservation}', [UserReservationController::class, 'show'])->name('user.reservations.show');
    Route::post('/reservations/{reservation}/cancel', [UserReservationController::class, 'cancel'])->name('user.reservations.cancel');

    // Riwayat & Pelaporan Kerusakan Pengguna
    Route::get('/reports', [UserReportController::class, 'index'])->name('user.reports.index');
    Route::get('/reports/create', [UserReportController::class, 'create'])->name('user.reports.create');
    Route::post('/reports', [UserReportController::class, 'store'])->name('user.reports.store');
    Route::get('/reports/{report}', [UserReportController::class, 'show'])->name('user.reports.show');
});


// -------------------------------------------------------------
// Petugas (Staff) Routes (SRS FR-PTG)
// -------------------------------------------------------------

Route::middleware(['auth', 'role:petugas'])->prefix('staff')->name('staff.')->group(function (): void {
    // Antrian & Riwayat Reservasi Petugas
    Route::get('/reservations', [StaffReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/history', [StaffReservationController::class, 'history'])->name('reservations.history');
    Route::get('/reservations/{reservation}', [StaffReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/approve', [StaffReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservations/{reservation}/reject', [StaffReservationController::class, 'reject'])->name('reservations.reject');
    Route::post('/reservations/{reservation}/cancel', [StaffReservationController::class, 'cancel'])->name('reservations.cancel');

    // Antrian & Riwayat Laporan Petugas
    Route::get('/reports', [StaffReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/history', [StaffReportController::class, 'history'])->name('reports.history');
    Route::get('/reports/{report}', [StaffReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/{report}/start-process', [StaffReportController::class, 'startProcess'])->name('reports.start-process');
    Route::post('/reports/{report}/resolve', [StaffReportController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/reject', [StaffReportController::class, 'reject'])->name('reports.reject');

    // Status Fasilitas & Dalam Perbaikan
    Route::get('/facilities', [StaffFacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/maintenance', [StaffFacilityController::class, 'maintenance'])->name('facilities.maintenance');
    Route::get('/facilities/{facility}', [StaffFacilityController::class, 'show'])->name('facilities.show');
    Route::post('/facilities/{facility}/update-status', [StaffFacilityController::class, 'updateStatus'])->name('facilities.update-status');
});


// -------------------------------------------------------------
// Administrator Routes (SRS FR-ADM)
// -------------------------------------------------------------

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.facilities.index'));

    Route::get('/facilities', [AdminFacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/create', [AdminFacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [AdminFacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}', [AdminFacilityController::class, 'show'])->name('facilities.show');
    Route::get('/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('facilities.edit');
    Route::put('/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('facilities.update');
    Route::post('/facilities/{facility}/deactivate', [AdminFacilityController::class, 'deactivate'])->name('facilities.deactivate');
    Route::post('/facilities/{facility}/reactivate', [AdminFacilityController::class, 'reactivate'])->name('facilities.reactivate');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/created', [AccountController::class, 'created'])->name('accounts.created');
    Route::get('/accounts/{user}', [AccountController::class, 'show'])->name('accounts.show');
    Route::post('/accounts/{user}/reset-password', [AccountController::class, 'resetPassword'])->name('accounts.reset-password');

    Route::get('/recaps', [RecapController::class, 'index'])->name('recaps.index');
    Route::get('/recaps/export', [RecapController::class, 'export'])->name('recaps.export');
});
