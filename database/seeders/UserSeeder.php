<?php

namespace Database\Seeders;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menyiapkan 20 akun Pengguna (Mahasiswa, Dosen, Staf) untuk simulasi transaksi dan edge cases.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        $users = [
            // ==========================================
            // Kategori: Mahasiswa (12 Akun)
            // ==========================================
            [
                'name' => 'Dimas Arya Pratama',
                'email' => 'mhs.dimas@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Anindya Putri Rahayu',
                'email' => 'mhs.anindya@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Farhan Maulana',
                'email' => 'mhs.farhan@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Nadhira Zahra',
                'email' => 'mhs.nadhira@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Reza Kurniawan',
                'email' => 'mhs.reza@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Tiara Puspitasari',
                'email' => 'mhs.tiara@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Kevin Sanjaya',
                'email' => 'mhs.kevin@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Alifia Nurul Hasanah',
                'email' => 'mhs.alifia@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Bagus Setiawan',
                'email' => 'mhs.bagus@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Cantika Dewi Lestari',
                'email' => 'mhs.cantika@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Danu Wicaksono',
                'email' => 'mhs.danu@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
            [
                'name' => 'Fathir Muhammad',
                'email' => 'mhs.fathir@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],

            // ==========================================
            // Kategori: Dosen (5 Akun)
            // ==========================================
            [
                'name' => 'Prof. Dr. Hendra Gunawan, M.T.',
                'email' => 'dosen.hendra@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Dosen,
            ],
            [
                'name' => 'Dr. Ratna Kusuma Wardani, M.Cs.',
                'email' => 'dosen.ratna@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Dosen,
            ],
            [
                'name' => 'Ir. Irwan Prasetyo, M.Eng.',
                'email' => 'dosen.irwan@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Dosen,
            ],
            [
                'name' => 'Maya Indriani, S.Kom., M.T.',
                'email' => 'dosen.maya@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Dosen,
            ],
            [
                'name' => 'Dr. Yusuf Habibie, S.T., M.Kom.',
                'email' => 'dosen.yusuf@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Dosen,
            ],

            // ==========================================
            // Kategori: Staf (3 Akun)
            // ==========================================
            [
                'name' => 'Bambang Sudiro, S.Sos.',
                'email' => 'staf.bambang@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Staf,
            ],
            [
                'name' => 'Dewi Anggraeni, S.E.',
                'email' => 'staf.dewi@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Staf,
            ],
            [
                'name' => 'Eko Prasetyawan, A.Md.',
                'email' => 'staf.eko@ppk.test',
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Staf,
            ],

            // ==========================================
            // Akun Petugas Tambahan (5 Akun)
            // ==========================================
            [
                'name' => 'Agus Setiawan, S.T.',
                'email' => 'petugas.sarpras@ppk.test',
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],
            [
                'name' => 'Rian Hidayat, A.Md.Kom.',
                'email' => 'petugas.lab@ppk.test',
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],
            [
                'name' => 'Hendri Wijaya',
                'email' => 'petugas.gedung@ppk.test',
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],
            [
                'name' => 'Bayu Pratama',
                'email' => 'petugas.olahraga@ppk.test',
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],
            [
                'name' => 'Joko Susilo, A.Md.',
                'email' => 'petugas.audiovisual@ppk.test',
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],

            // ==========================================
            // Akun Admin Tambahan (2 Akun)
            // ==========================================
            [
                'name' => 'Ir. H. Suryono, M.T.',
                'email' => 'admin.sarpras@ppk.test',
                'role' => UserRole::Admin,
                'user_category' => null,
            ],
            [
                'name' => 'Nanda Pratama, S.Kom.',
                'email' => 'admin.sistem@ppk.test',
                'role' => UserRole::Admin,
                'user_category' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword,
                    'role' => $userData['role'],
                    'user_category' => $userData['user_category'],
                ]
            );
        }
    }
}
