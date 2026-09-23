<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'reservation_number' => 'RSV-' . strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'purpose' => fake()->sentence(6),
            'status' => ReservationStatus::Menunggu,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Disetujui,
            'approved_at' => now(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Berlangsung,
            'approved_at' => now()->subHour(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::Ditolak,
            'rejection_reason' => 'Fasilitas tidak dapat digunakan untuk kegiatan komersial.',
            'rejected_at' => now(),
        ]);
    }
}
