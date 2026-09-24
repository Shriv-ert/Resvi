<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Comprehensive Mock Repository for Pure UI Presentation
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
                'capacity' => null, // SRS: null capacity for tools
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
                'is_active' => false, // Inactive master data for admin preview
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
                'reservation_number' => 'RSV-2026-0902',
                'applicant_name' => 'Dr. Hendra Gunawan',
                'applicant_category' => 'Dosen',
                'applicant_email' => 'dosen@ppk.test',
                'facility_id' => 'fac-02',
                'facility_name' => 'Laboratorium Komputer & AI 301',
                'facility_type' => 'laboratorium',
                'facility_location' => 'Gedung B (Laboratorium)',
                'date' => '2026-09-25',
                'start_time' => '13:00',
                'end_time' => '15:30',
                'purpose' => 'Sesi praktikum tambahan dan uji coba model deep learning untuk kelas kecerdasan buatan.',
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

    public static function users()
    {
        return [
            [
                'id' => 'usr-01',
                'name' => 'Ahmad Fauzi',
                'email' => 'mahasiswa@ppk.test',
                'role' => 'pengguna',
                'user_category' => 'mahasiswa',
                'created_at' => '2026-08-10',
            ],
            [
                'id' => 'usr-02',
                'name' => 'Dr. Hendra Gunawan',
                'email' => 'dosen@ppk.test',
                'role' => 'pengguna',
                'user_category' => 'dosen',
                'created_at' => '2026-08-12',
            ],
            [
                'id' => 'usr-03',
                'name' => 'Rina Kartika',
                'email' => 'rina@ppk.test',
                'role' => 'pengguna',
                'user_category' => 'staf',
                'created_at' => '2026-08-15',
            ],
            [
                'id' => 'usr-04',
                'name' => 'Budi Santoso',
                'email' => 'petugas@ppk.test',
                'role' => 'petugas',
                'user_category' => null,
                'created_at' => '2026-08-01',
            ],
            [
                'id' => 'usr-05',
                'name' => 'Administrator PPK',
                'email' => 'admin@ppk.test',
                'role' => 'admin',
                'user_category' => null,
                'created_at' => '2026-07-20',
            ],
        ];
    }
}

// -------------------------------------------------------------
// Public & Shared Routes (Pengunjung & Pengguna)
// -------------------------------------------------------------

Route::get('/', function (Request $request) {
    $facilities = MockData::facilities();

    // Filter by type if provided
    if ($request->filled('type')) {
        $facilities = array_filter($facilities, fn($f) => $f['type'] === $request->type);
    }
    // Filter by location if provided
    if ($request->filled('location')) {
        $facilities = array_filter($facilities, fn($f) => $f['location'] === $request->location);
    }
    // Filter by capacity if provided
    if ($request->filled('capacity')) {
        $facilities = array_filter($facilities, fn($f) => !empty($f['capacity']) && $f['capacity'] >= (int)$request->capacity);
    }
    // Search by name if provided
    if ($request->filled('search')) {
        $query = strtolower(trim($request->search));
        $facilities = array_filter($facilities, fn($f) => str_contains(strtolower($f['name']), $query));
    }

    // Filter out inactive master data for public visitors
    $facilities = array_filter($facilities, fn($f) => ($f['is_active'] ?? true));

    return view('facilities.index', ['facilities' => $facilities]);
})->name('facilities.index');
Route::redirect('/home', '/')->name('home');

Route::get('/facilities/{facility}', function ($facilityId) {
    $facilities = MockData::facilities();
    $found = collect($facilities)->firstWhere('id', $facilityId) ?? $facilities[0];

    return view('facilities.show', ['facility' => $found]);
})->name('facilities.show');

Route::get('/reservations/create', function (Request $request) {
    $facilities = MockData::facilities();
    $facilityId = $request->get('facility_id', 'fac-01');
    $facility = collect($facilities)->firstWhere('id', $facilityId) ?? $facilities[0];

    return view('reservations.create', ['facility' => $facility]);
})->name('reservations.create');

// Auth routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    // If logging in from draft reservation
    if ($request->filled('draft_facility')) {
        return redirect()->route('reservations.create', ['facility_id' => $request->draft_facility, 'as_user' => 1]);
    }
    return redirect()->route('facilities.index', ['as_user' => 1]);
})->name('login.store');

Route::post('/logout', function () {
    return redirect()->route('facilities.index');
})->name('logout');


// -------------------------------------------------------------
// Pengguna (User) Routes
// -------------------------------------------------------------

Route::prefix('my')->group(function () {
    // Riwayat Reservasi Pengguna
    Route::get('/reservations', function () {
        $reservations = MockData::reservations();
        return view('user.reservations.index', ['reservations' => $reservations]);
    })->name('user.reservations.index');

    Route::post('/reservations', function () {
        return redirect()->route('user.reservations.index', ['as_user' => 1]);
    });

    // Detail Reservasi Pengguna
    Route::get('/reservations/{reservation}', function ($resId) {
        $reservations = MockData::reservations();
        $reservation = collect($reservations)->firstWhere('id', $resId) ?? $reservations[0];
        return view('user.reservations.show', ['reservation' => $reservation]);
    })->name('user.reservations.show');

    // Cancel Reservasi Pengguna
    Route::post('/reservations/{reservation}/cancel', function ($resId) {
        return redirect()->route('user.reservations.show', ['reservation' => $resId, 'as_user' => 1]);
    })->name('user.reservations.cancel');

    // Riwayat Laporan Pengguna
    Route::get('/reports', function () {
        $reports = MockData::reports();
        return view('user.reports.index', ['reports' => $reports]);
    })->name('user.reports.index');

    Route::post('/reports', function () {
        return redirect()->route('user.reports.index', ['as_user' => 1]);
    });

    // Buat Laporan Pengguna
    Route::get('/reports/create', function () {
        $facilities = MockData::facilities();
        return view('user.reports.create', ['facilities' => $facilities]);
    })->name('user.reports.create');

    // Detail Laporan Pengguna
    Route::get('/reports/{report}', function ($repId) {
        $reports = MockData::reports();
        $report = collect($reports)->firstWhere('id', $repId) ?? $reports[0];
        return view('user.reports.show', ['report' => $report]);
    })->name('user.reports.show');
});


// -------------------------------------------------------------
// Petugas (Staff) Routes
// -------------------------------------------------------------

Route::prefix('staff')->group(function () {
    // Antrian Reservasi (Initial Staff Page as per SRS FR-PTG-01)
    Route::get('/reservations', function () {
        $all = MockData::reservations();
        // Active reservations: menunggu, disetujui, berlangsung (SRS FR-PTG-02)
        $active = array_filter($all, fn($r) => in_array($r['status'], ['menunggu', 'disetujui', 'berlangsung']));
        return view('staff.reservations.index', ['reservations' => $active]);
    })->name('staff.reservations.index');

    // Riwayat Reservasi Final (SRS FR-PTG-02)
    Route::get('/reservations/history', function () {
        $all = MockData::reservations();
        $history = array_filter($all, fn($r) => in_array($r['status'], ['selesai', 'ditolak', 'dibatalkan_oleh_pengguna', 'dibatalkan_oleh_petugas', 'kedaluwarsa']));
        return view('staff.reservations.history', ['reservations' => $history]);
    })->name('staff.reservations.history');

    // Detail Reservasi Petugas
    Route::get('/reservations/{reservation}', function ($resId) {
        $reservations = MockData::reservations();
        $reservation = collect($reservations)->firstWhere('id', $resId) ?? $reservations[0];
        return view('staff.reservations.show', ['reservation' => $reservation]);
    })->name('staff.reservations.show');

    // Approval / Reject / Cancel actions
    Route::post('/reservations/{reservation}/approve', function ($resId) {
        return redirect()->route('staff.reservations.index');
    })->name('staff.reservations.approve');

    Route::post('/reservations/{reservation}/reject', function ($resId) {
        return redirect()->route('staff.reservations.index');
    })->name('staff.reservations.reject');

    Route::post('/reservations/{reservation}/cancel', function ($resId) {
        return redirect()->route('staff.reservations.index');
    })->name('staff.reservations.cancel');

    // Antrian Laporan (SRS FR-PTG-06: Baru & Diproses)
    Route::get('/reports', function () {
        $all = MockData::reports();
        $active = array_filter($all, fn($r) => in_array($r['status'], ['baru', 'diproses']));
        return view('staff.reports.index', ['reports' => $active]);
    })->name('staff.reports.index');

    // Riwayat Laporan Final (SRS FR-PTG-06: Selesai & Ditolak)
    Route::get('/reports/history', function () {
        $all = MockData::reports();
        $history = array_filter($all, fn($r) => in_array($r['status'], ['selesai', 'ditolak']));
        return view('staff.reports.history', ['reports' => $history]);
    })->name('staff.reports.history');

    // Detail Laporan Petugas
    Route::get('/reports/{report}', function ($repId) {
        $reports = MockData::reports();
        $report = collect($reports)->firstWhere('id', $repId) ?? $reports[0];
        return view('staff.reports.show', ['report' => $report]);
    })->name('staff.reports.show');

    Route::post('/reports/{report}/start-process', function ($repId) {
        return redirect()->route('staff.reports.show', ['report' => $repId]);
    })->name('staff.reports.start-process');

    Route::post('/reports/{report}/resolve', function ($repId) {
        return redirect()->route('staff.reports.index');
    })->name('staff.reports.resolve');

    Route::post('/reports/{report}/reject', function ($repId) {
        return redirect()->route('staff.reports.index');
    })->name('staff.reports.reject');

    // Fasilitas Petugas (SRS FR-PTG-13)
    Route::get('/facilities', function () {
        $facilities = MockData::facilities();
        return view('staff.facilities.index', ['facilities' => $facilities]);
    })->name('staff.facilities.index');

    // Fasilitas Dalam Perbaikan (SRS FR-PTG-12)
    Route::get('/facilities/maintenance', function () {
        $facilities = MockData::facilities();
        $maintenance = array_values(array_filter($facilities, fn($f) => $f['operational_status'] === 'dalam_perbaikan'));
        return view('staff.facilities.maintenance', [
            'facilities' => $maintenance,
            'maintenanceFacilities' => $maintenance
        ]);
    })->name('staff.facilities.maintenance');

    // Detail Fasilitas Petugas
    Route::get('/facilities/{facility}', function ($facilityId) {
        $facilities = MockData::facilities();
        $facility = collect($facilities)->firstWhere('id', $facilityId) ?? $facilities[0];
        return view('staff.facilities.show', ['facility' => $facility]);
    })->name('staff.facilities.show');

    // Update Status Operasional (SRS FR-PTG-10 & FR-PTG-11)
    Route::post('/facilities/{facility}/update-status', function ($facilityId) {
        return redirect()->route('staff.facilities.maintenance');
    })->name('staff.facilities.update-status');
});


// -------------------------------------------------------------
// Admin Routes
// -------------------------------------------------------------

Route::prefix('admin')->group(function () {
    // Kelola Fasilitas Master (Initial Admin Page as per SRS FR-ADM-01)
    Route::get('/facilities', function () {
        $facilities = MockData::facilities();
        return view('admin.facilities.index', ['facilities' => $facilities]);
    })->name('admin.facilities.index');

    Route::get('/facilities/create', function () {
        return view('admin.facilities.create');
    })->name('admin.facilities.create');

    Route::post('/facilities', function () {
        return redirect()->route('admin.facilities.index');
    });

    Route::get('/facilities/{facility}', function ($facilityId) {
        $facilities = MockData::facilities();
        $facility = collect($facilities)->firstWhere('id', $facilityId) ?? $facilities[0];
        return view('admin.facilities.show', ['facility' => $facility]);
    })->name('admin.facilities.show');

    Route::put('/facilities/{facility}', function ($facilityId) {
        return redirect()->route('admin.facilities.show', ['facility' => $facilityId]);
    });

    Route::get('/facilities/{facility}/edit', function ($facilityId) {
        $facilities = MockData::facilities();
        $facility = collect($facilities)->firstWhere('id', $facilityId) ?? $facilities[0];
        return view('admin.facilities.edit', ['facility' => $facility]);
    })->name('admin.facilities.edit');

    Route::post('/facilities/{facility}/deactivate', function ($facilityId) {
        return redirect()->route('admin.facilities.show', ['facility' => $facilityId]);
    })->name('admin.facilities.deactivate');

    Route::post('/facilities/{facility}/reactivate', function ($facilityId) {
        return redirect()->route('admin.facilities.show', ['facility' => $facilityId]);
    })->name('admin.facilities.reactivate');

    // Kelola Akun (SRS FR-ADM-02)
    Route::get('/accounts', function () {
        $users = MockData::users();
        return view('admin.accounts.index', ['users' => $users]);
    })->name('admin.accounts.index');

    Route::get('/accounts/create', function () {
        return view('admin.accounts.create');
    })->name('admin.accounts.create');

    Route::post('/accounts/created', function (Request $request) {
        $account = [
            'name' => $request->get('name', 'Bintang Ramadhan'),
            'email' => $request->get('email', 'bintang@ppk.test'),
            'role' => $request->get('role', 'pengguna'),
            'user_category' => $request->get('user_category', 'mahasiswa'),
            'temporary_password' => 'PPK-' . strtoupper(substr(md5(time()), 0, 4)) . '-2026!',
        ];
        return view('admin.accounts.created', ['createdAccount' => $account]);
    })->name('admin.accounts.created');

    Route::get('/accounts/{user}', function ($userId) {
        $users = MockData::users();
        $user = collect($users)->firstWhere('id', $userId) ?? $users[0];
        return view('admin.accounts.show', ['user' => $user]);
    })->name('admin.accounts.show');

    Route::post('/accounts/{user}/reset-password', function ($userId) {
        return redirect()->route('admin.accounts.show', ['user' => $userId]);
    })->name('admin.accounts.reset-password');

    // Rekapitulasi (SRS FR-ADM-10 & FR-ADM-11)
    Route::get('/recaps', function (Request $request) {
        $recapType = $request->get('recap_type', 'okupansi');
        $groupBy = $request->get('group_by', 'fasilitas');
        $selectedMonth = (int)$request->get('month', 9);
        $selectedYear = (int)$request->get('year', 2026);

        if ($groupBy === 'lokasi') {
            if ($recapType === 'okupansi') {
                $recapData = [
                    [
                        'label' => 'Gedung A (Utama)',
                        'completed_hours' => 248,
                        'total_available_hours' => 780,
                        'total_available_slots' => 1560,
                        'occupancy_rate' => 31.8,
                    ],
                    [
                        'label' => 'Gedung B (Laboratorium)',
                        'completed_hours' => 312,
                        'total_available_hours' => 780,
                        'total_available_slots' => 1560,
                        'occupancy_rate' => 40.0,
                    ],
                    [
                        'label' => 'Area Terbuka Kampus',
                        'completed_hours' => 140,
                        'total_available_hours' => 390,
                        'total_available_slots' => 780,
                        'occupancy_rate' => 35.9,
                    ],
                ];
            } else {
                $recapData = [
                    [
                        'label' => 'Gedung A (Utama)',
                        'breakdown_count' => 1,
                        'operational_status' => 'dapat_digunakan',
                    ],
                    [
                        'label' => 'Gedung B (Laboratorium)',
                        'breakdown_count' => 3,
                        'operational_status' => 'dalam_perbaikan',
                    ],
                    [
                        'label' => 'Area Terbuka Kampus',
                        'breakdown_count' => 0,
                        'operational_status' => 'dapat_digunakan',
                    ],
                ];
            }
        } else {
            if ($recapType === 'okupansi') {
                $recapData = [
                    [
                        'label' => 'Ruang Kuliah Teori A-101',
                        'type' => 'kelas',
                        'location' => 'Gedung A (Utama)',
                        'completed_hours' => 124,
                        'total_available_hours' => 390,
                        'total_available_slots' => 780,
                        'occupancy_rate' => 31.8,
                    ],
                    [
                        'label' => 'Laboratorium Komputer & AI 301',
                        'type' => 'laboratorium',
                        'location' => 'Gedung B (Laboratorium)',
                        'completed_hours' => 208,
                        'total_available_hours' => 390,
                        'total_available_slots' => 780,
                        'occupancy_rate' => 53.3,
                    ],
                    [
                        'label' => 'Aula Utama Sasana Krida Kampus',
                        'type' => 'aula',
                        'location' => 'Gedung A (Utama)',
                        'completed_hours' => 84,
                        'total_available_hours' => 390,
                        'total_available_slots' => 780,
                        'occupancy_rate' => 21.5,
                    ],
                    [
                        'label' => 'Lapangan Futsal & Basket Outdoor',
                        'type' => 'lapangan',
                        'location' => 'Area Terbuka Kampus',
                        'completed_hours' => 140,
                        'total_available_hours' => 390,
                        'total_available_slots' => 780,
                        'occupancy_rate' => 35.9,
                    ],
                ];
            } else {
                $recapData = [
                    [
                        'label' => 'Proyektor Cinema & Sound Portabel',
                        'type' => 'alat',
                        'location' => 'Gedung B (Laboratorium)',
                        'breakdown_count' => 2,
                        'operational_status' => 'dalam_perbaikan',
                    ],
                    [
                        'label' => 'Laboratorium Komputer & AI 301',
                        'type' => 'laboratorium',
                        'location' => 'Gedung B (Laboratorium)',
                        'breakdown_count' => 1,
                        'operational_status' => 'dapat_digunakan',
                    ],
                    [
                        'label' => 'Ruang Kuliah Teori A-101',
                        'type' => 'kelas',
                        'location' => 'Gedung A (Utama)',
                        'breakdown_count' => 1,
                        'operational_status' => 'dapat_digunakan',
                    ],
                    [
                        'label' => 'Aula Utama Sasana Krida Kampus',
                        'type' => 'aula',
                        'location' => 'Gedung A (Utama)',
                        'breakdown_count' => 0,
                        'operational_status' => 'dapat_digunakan',
                    ],
                ];
            }
        }

        return view('admin.recaps.index', [
            'recapType' => $recapType,
            'groupBy' => $groupBy,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'recapData' => $recapData,
        ]);
    })->name('admin.recaps.index');
});
