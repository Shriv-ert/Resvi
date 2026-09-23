<?php

namespace Database\Seeders;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use App\Models\FacilityPhoto;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'name' => 'Ruang Kelas Teori A.101',
                'type' => FacilityType::Kelas,
                'location' => 'Gedung Kuliah Bersama Lantai 1',
                'capacity' => 45,
                'description' => 'Ruang kelas ber-AC dilengkapi proyektor plafon, papan tulis ganda, dan podium dosen. Cocok untuk perkuliahan reguler dan seminar kecil.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Ruang Kelas Teori A.102',
                'type' => FacilityType::Kelas,
                'location' => 'Gedung Kuliah Bersama Lantai 1',
                'capacity' => 30,
                'description' => 'Ruang kelas interaktif dengan meja melingkar (round table) untuk diskusi kelompok dan presentasi mahasiswa.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Laboratorium Komputer & Jaringan',
                'type' => FacilityType::Laboratorium,
                'location' => 'Gedung Sains & Komputer Lantai 3',
                'capacity' => 36,
                'description' => 'Laboratorium dengan 36 workstation PC berspesifikasi Core i7, gigabit ethernet switch, rak server mini, dan koneksi internet dedicated.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Laboratorium Rekayasa Perangkat Lunak',
                'type' => FacilityType::Laboratorium,
                'location' => 'Gedung Sains & Komputer Lantai 2',
                'capacity' => 40,
                'description' => 'Laboratorium pengembangan sistem dan kecerdasan buatan, dilengkapi dual-monitor display pada setiap workstation mahasiswa.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Aula Graha Utama Nusantara',
                'type' => FacilityType::Aula,
                'location' => 'Gedung Rektorat Lantai 2',
                'capacity' => 350,
                'description' => 'Aula serbaguna berkapasitas besar dengan tata suara akustik, panggung utama, ruang rias, dan pencahayaan panggung standar pementasan/wisuda.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Lapangan Futsal Terbuka',
                'type' => FacilityType::Lapangan,
                'location' => 'Kompleks Olahraga Kampus',
                'capacity' => 20,
                'description' => 'Lapangan futsal luar ruangan beralas rumput sintetis berkualitas tinggi dilengkapi lampu sorot malam hari.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Lapangan Basket Barat',
                'type' => FacilityType::Lapangan,
                'location' => 'Kompleks Olahraga Kampus',
                'capacity' => 30,
                'description' => 'Lapangan basket outdoor standar PERBASI dengan ring hidrolik dan tribun penonton sisi barat.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Proyektor Laser Epson EB-L520U',
                'type' => FacilityType::Alat,
                'location' => 'Ruang Perlengkapan Gedung A',
                'capacity' => null,
                'description' => 'Proyektor resolusi WUXGA 5.200 lumens yang menetap di trolley perlengkapan Gedung A. Tidak memiliki kapasitas orang.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Audio Mixer Yamaha TF5 & Sound Rig',
                'type' => FacilityType::Alat,
                'location' => 'Gedung Rektorat (Ruang Kontrol Audio)',
                'capacity' => null,
                'description' => 'Konsol mixer audio digital 32 channel beserta sound processor yang menetap di ruang kendali Aula Nusantara.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
            [
                'name' => 'Studio Rekaman & Podcast Mahasiswa',
                'type' => FacilityType::Laboratorium,
                'location' => 'Gedung Seni & Komunikasi Lantai 1',
                'capacity' => 10,
                'description' => 'Studio kedap suara untuk produksi audio dan siniar. Saat ini sedang dalam tahap perbaikan panel akustik.',
                'is_active' => true,
                'operational_status' => OperationalStatus::DalamPerbaikan,
            ],
            [
                'name' => 'Gedung Kesenian Lama (Arsip)',
                'type' => FacilityType::Aula,
                'location' => 'Area Belakang Kampus',
                'capacity' => 100,
                'description' => 'Fasilitas gedung non-aktif yang dialihfungsikan untuk gudang logistik dan dinonaktifkan oleh administrator.',
                'is_active' => false,
                'operational_status' => OperationalStatus::DapatDigunakan,
            ],
        ];

        foreach ($facilities as $data) {
            $facility = Facility::query()->create($data);

            // Buat foto utama jika aktif
            if ($facility->is_active) {
                FacilityPhoto::query()->create([
                    'facility_id' => $facility->id,
                    'photo_path' => 'facilities/' . strtolower($facility->type->value) . '-sample.jpg',
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
