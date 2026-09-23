<?php

namespace Database\Seeders;

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
        User::query()->firstOrCreate(
            ['email' => 'admin@ppk.test'],
            [
                'name' => 'Administrator PPK',
                'password' => Hash::make('PPK-Admin-2026!'),
                'role' => UserRole::Admin,
                'user_category' => null,
            ],
        );
    }
}
