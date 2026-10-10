<?php

namespace Database\Seeders;

use App\Enums\FacilityStatusChangeType;
use App\Enums\OperationalStatus;
use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Models\Facility;
use App\Models\FacilityStatusLog;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Menjalankan seeder laporan kerusakan (8 laporan) dan log status fasilitas terkait.
     * Sesuai Opsi A: Studio Rekaman berstatus 'Diproses' agar dapat diselesaikan saat demo.
     */
    public function run(): void
    {
        $facilities = Facility::query()->pluck('id', 'name');
        $users = User::query()->pluck('id', 'email');

        $facility = fn (string $name) => $facilities->get($name) ?? throw new \RuntimeException("Facility not found: {$name}");
        $user = fn (string $email) => $users->get($email) ?? throw new \RuntimeException("User not found: {$email}");

        $reports = [
            // =========================================================================
            // GROUP 1: LAPORAN BARU (3 Laporan)
            // Tujuan: Antrean aktif petugas (staff/reports) yang siap dibuka dan mulai diproses saat demo.
            // =========================================================================
            [
                'report_number' => 'LAP-20261011-0001',
                'reporter_id' => $user('mhs.dimas@ppk.test'),
                'facility_id' => $facility('Ruang Kelas Teori A.101'),
                'category' => ReportCategory::KerusakanElektronik,
                'description' => "Proyektor plafon di Ruang A.101 berkedip terus-menerus dan lampu indikator error berkedip merah saat disambungkan ke laptop melalui HDMI. Tampilan layar presentasi tidak muncul.",
                'photo_path' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&auto=format&fit=crop&q=80',
                'status' => ReportStatus::Baru,
                'created_at' => Carbon::parse('2026-10-11 08:30:00'),
            ],
            [
                'report_number' => 'LAP-20261011-0002',
                'reporter_id' => $user('dosen.irwan@ppk.test'),
                'facility_id' => $facility('Ruang Kelas Teori A.102'),
                'category' => ReportCategory::KerusakanFisik,
                'description' => "Kait engsel jendela sisi barat patah sehingga jendela tidak dapat dikunci rapat saat hujan lebat. Selain itu, penutup stopkontak meja dosen terlepas dan kawat terbuka, berpotensi bahaya listrik.",
                'photo_path' => null,
                'status' => ReportStatus::Baru,
                'created_at' => Carbon::parse('2026-10-11 10:15:00'),
            ],
            [
                'report_number' => 'LAP-20261012-0003',
                'reporter_id' => $user('staf.bambang@ppk.test'),
                'facility_id' => $facility('Aula Graha Utama Nusantara'),
                'category' => ReportCategory::Kebersihan,
                'description' => "Sisa konsumsi dan botol plastik dari kegiatan kemahasiswaan kemarin belum dibersihkan di deretan tribun penonton barat. Karpet panggung utama juga berdebu tebal dan perlu disedot vakum.",
                'photo_path' => null,
                'status' => ReportStatus::Baru,
                'created_at' => Carbon::parse('2026-10-12 09:00:00'),
            ],

            // =========================================================================
            // GROUP 2: LAPORAN SEDANG DIPROSES (2 Laporan)
            // Tujuan: Antrean petugas yang sedang ditangani aktif.
            // Termasuk Studio Rekaman yang saat ini 'Dalam Perbaikan' agar siap didemokan penyelesaiannya.
            // =========================================================================
            [
                'report_number' => 'LAP-20261011-0004',
                'reporter_id' => $user('mhs.reza@ppk.test'),
                'facility_id' => $facility('Laboratorium Komputer & Jaringan'),
                'category' => ReportCategory::KerusakanElektronik,
                'description' => "Port switch nomor 12 sampai 16 pada rak server mini mati total, menyebabkan 5 unit PC praktikum tidak mendapat koneksi LAN. Perlu penggantian switch atau crimping ulang kabel trunk.",
                'photo_path' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
                'status' => ReportStatus::Diproses,
                'processed_by_id' => $user('petugas.lab@ppk.test'),
                'processed_at' => Carbon::parse('2026-10-11 11:00:00'),
                'created_at' => Carbon::parse('2026-10-11 10:30:00'),
            ],
            [
                'report_number' => 'LAP-20261010-0005',
                'reporter_id' => $user('mhs.nadhira@ppk.test'),
                'facility_id' => $facility('Studio Rekaman & Podcast Mahasiswa'),
                'category' => ReportCategory::KerusakanFisik,
                'description' => "Panel busa peredam akustik pada dinding sisi kanan studio terlepas dan berlubang. Suara dari luar gedung bocor masuk saat rekaman podcast mahasiswa. Fasilitas perlu ditutup sementara untuk renovasi panel.",
                'photo_path' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800&auto=format&fit=crop&q=80',
                'status' => ReportStatus::Diproses,
                'processed_by_id' => $user('petugas.audiovisual@ppk.test'),
                'processed_at' => Carbon::parse('2026-10-10 14:00:00'),
                'created_at' => Carbon::parse('2026-10-10 13:15:00'),
            ],

            // =========================================================================
            // GROUP 3: LAPORAN SELESAI (1 Laporan)
            // Tujuan: Riwayat laporan yang telah tuntas ditangani dengan catatan resolusi teknis.
            // =========================================================================
            [
                'report_number' => 'LAP-20261008-0006',
                'reporter_id' => $user('dosen.ratna@ppk.test'),
                'facility_id' => $facility('Laboratorium Rekayasa Perangkat Lunak'),
                'category' => ReportCategory::KerusakanElektronik,
                'description' => "Unit pendingin udara (AC) split sisi utara ruangan lab tidak dingin dan meneteskan air ke lantai dekat workstation nomor 08, berisiko terhadap kelistrikan PC.",
                'photo_path' => null,
                'status' => ReportStatus::Selesai,
                'processed_by_id' => $user('petugas.lab@ppk.test'),
                'processed_at' => Carbon::parse('2026-10-08 14:00:00'),
                'resolved_by_id' => $user('petugas.sarpras@ppk.test'),
                'resolved_at' => Carbon::parse('2026-10-09 16:30:00'),
                'resolution_notes' => "Unit AC telah diservis oleh teknisi rekanan sarpras. Dilakukan pembersihan filter udara, perbaikan jalur pembuangan kondensasi air, dan pengisian freon R410A. Suhu ruangan kini stabil di 20°C.",
                'created_at' => Carbon::parse('2026-10-08 13:30:00'),
            ],

            // =========================================================================
            // GROUP 4: LAPORAN DITOLAK (2 Laporan)
            // Tujuan: Riwayat penolakan laporan dengan alasan penolakan petugas yang jelas sesuai SRS.
            // =========================================================================
            [
                'report_number' => 'LAP-20261009-0007',
                'reporter_id' => $user('mhs.kevin@ppk.test'),
                'facility_id' => $facility('Proyektor Laser Epson EB-L520U'),
                'category' => ReportCategory::KerusakanElektronik,
                'description' => "Proyektor tidak mau menampilkan gambar saat disambungkan ke laptop saya menggunakan port USB-C ke HDMI.",
                'photo_path' => null,
                'status' => ReportStatus::Ditolak,
                'processed_by_id' => $user('petugas.sarpras@ppk.test'),
                'processed_at' => Carbon::parse('2026-10-09 10:00:00'),
                'rejected_by_id' => $user('petugas.sarpras@ppk.test'),
                'rejected_at' => Carbon::parse('2026-10-09 11:30:00'),
                'rejection_reason' => "Setelah dilakukan pemeriksaan teknis, port HDMI dan unit proyektor laser berfungsi normal. Masalah disebabkan oleh dongle/adapter konverter milik pribadi pelapor yang tidak kompatibel.",
                'created_at' => Carbon::parse('2026-10-09 09:15:00'),
            ],
            [
                'report_number' => 'LAP-20261010-0008',
                'reporter_id' => $user('mhs.cantika@ppk.test'),
                'facility_id' => $facility('Lapangan Basket Barat'),
                'category' => ReportCategory::Keamanan,
                'description' => "Dua buah bola basket latihan hilang dari keranjang penyimpanan di pinggir lapangan setelah sesi sparing kemarin sore.",
                'photo_path' => null,
                'status' => ReportStatus::Ditolak,
                'processed_by_id' => $user('petugas.olahraga@ppk.test'),
                'processed_at' => Carbon::parse('2026-10-10 09:30:00'),
                'rejected_by_id' => $user('petugas.olahraga@ppk.test'),
                'rejected_at' => Carbon::parse('2026-10-10 11:00:00'),
                'rejection_reason' => "Sistem reservasi dan laporan hanya mengelola fasilitas fisik tetap dan inventaris terpasang kampus. Perlengkapan habis pakai seperti bola latihan merupakan tanggung jawab inventaris internal UKM Basket.",
                'created_at' => Carbon::parse('2026-10-10 08:45:00'),
            ],
        ];

        $studioReportId = null;

        foreach ($reports as $data) {
            $report = Report::query()->updateOrCreate(
                ['report_number' => $data['report_number']],
                $data
            );

            if ($data['report_number'] === 'LAP-20261010-0005') {
                $studioReportId = $report->id;
            }
        }

        // =========================================================================
        // LOG STATUS OPERASIONAL FASILITAS (Audit Trail & Rekapitulasi Admin)
        // Tujuan: Memastikan halaman 'Fasilitas Dalam Perbaikan' (staff/facilities/maintenance)
        //         dan Rekapitulasi Kerusakan Bulanan Admin (admin/recaps) terisi akurat.
        // =========================================================================
        $studioFacilityId = $facility('Studio Rekaman & Podcast Mahasiswa');
        $proyektorFacilityId = $facility('Proyektor Laser Epson EB-L520U');
        $kesenianFacilityId = $facility('Gedung Kesenian Lama (Arsip)');

        $audiovisualStaffId = $user('petugas.audiovisual@ppk.test');
        $adminSarprasId = $user('admin.sarpras@ppk.test');

        // 1. Studio Rekaman: Masuk status Dalam Perbaikan di bulan Oktober 2026
        FacilityStatusLog::query()->updateOrCreate(
            [
                'facility_id' => $studioFacilityId,
                'change_type' => FacilityStatusChangeType::OperationalStatus,
                'new_value' => OperationalStatus::DalamPerbaikan->value,
            ],
            [
                'changed_by_id' => $audiovisualStaffId,
                'old_value' => OperationalStatus::DapatDigunakan->value,
                'reason' => 'Perbaikan dan pemasangan ulang panel akustik kedap suara dinding studio yang terlepas.',
                'report_id' => $studioReportId,
                'changed_at' => Carbon::parse('2026-10-10 14:00:00'),
            ]
        );

        // 2. Proyektor Laser: Riwayat Kerusakan Masa Lalu di Bulan September 2026
        // Kejadian 2a: Ditandai Dalam Perbaikan pada 15 September 2026
        FacilityStatusLog::query()->updateOrCreate(
            [
                'facility_id' => $proyektorFacilityId,
                'change_type' => FacilityStatusChangeType::OperationalStatus,
                'changed_at' => Carbon::parse('2026-09-15 10:00:00'),
            ],
            [
                'changed_by_id' => $audiovisualStaffId,
                'old_value' => OperationalStatus::DapatDigunakan->value,
                'new_value' => OperationalStatus::DalamPerbaikan->value,
                'reason' => 'Pergantian modul pendingin laser proyektor dan pembersihan lensa optik.',
                'report_id' => null,
            ]
        );

        // Kejadian 2b: Dikembalikan Dapat Digunakan pada 18 September 2026
        FacilityStatusLog::query()->updateOrCreate(
            [
                'facility_id' => $proyektorFacilityId,
                'change_type' => FacilityStatusChangeType::OperationalStatus,
                'changed_at' => Carbon::parse('2026-09-18 15:30:00'),
            ],
            [
                'changed_by_id' => $audiovisualStaffId,
                'old_value' => OperationalStatus::DalamPerbaikan->value,
                'new_value' => OperationalStatus::DapatDigunakan->value,
                'reason' => 'Pengujian modul pendingin laser selesai dan proyektor siap digunakan kembali.',
                'report_id' => null,
            ]
        );

        // 3. Gedung Kesenian Lama: Log Penonaktifan Data Master oleh Admin (BR-ADM-03)
        FacilityStatusLog::query()->updateOrCreate(
            [
                'facility_id' => $kesenianFacilityId,
                'change_type' => FacilityStatusChangeType::IsActive,
                'new_value' => '0',
            ],
            [
                'changed_by_id' => $adminSarprasId,
                'old_value' => '1',
                'reason' => 'Fasilitas dialihfungsikan permanen menjadi gudang logistik dan arsip kampus.',
                'report_id' => null,
                'changed_at' => Carbon::parse('2026-08-15 10:00:00'),
            ]
        );
    }
}
