<?php

namespace Database\Factories;

use App\Enums\UserCategory;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Mahasiswa,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin,
            'user_category' => null,
        ]);
    }

    /**
     * Indicate that the user is a petugas.
     */
    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Petugas,
            'user_category' => null,
        ]);
    }

    /**
     * Indicate that the user is a dosen.
     */
    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Dosen,
        ]);
    }

    /**
     * Indicate that the user is a staf.
     */
    public function staf(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Pengguna,
            'user_category' => UserCategory::Staf,
        ]);
    }
}
