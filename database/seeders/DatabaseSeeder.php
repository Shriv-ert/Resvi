<?php

namespace Database\Seeders;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin PPK
        User::query()->updateOrCreate(
            ['email' => 'admin@ppk.test'],
            [
                'name' => 'Administrator PPK',
                'password' => Hash::make('password123'),
                'role' => UserRole::Admin,
                'user_category' => null,
            ],
        );

        // 2. Petugas Demo
        User::query()->updateOrCreate(
            ['email' => 'petugas.demo@ppk.test'],
            [
                'name' => 'Petugas Sarpras Kampus',
                'password' => Hash::make('password123'),
                'role' => UserRole::Petugas,
                'user_category' => null,
            ],
        );

        // 3. Pengguna Demo (Mahasiswa)
        User::query()->updateOrCreate(
            ['email' => 'mahasiswa.demo@ppk.test'],
            [
                'name' => 'Ahmad Mahasiswa',
                'password' => Hash::make('password123'),
                'role' => UserRole::Pengguna,
                'user_category' => UserCategory::Mahasiswa,
            ],
        );
    }
}
