<?php

namespace Database\Factories;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'report_number' => 'LAP-' . strtoupper(Str::random(10)),
            'reporter_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'category' => ReportCategory::KerusakanFisik,
            'description' => fake()->paragraph(),
            'photo_path' => null,
            'status' => ReportStatus::Baru,
        ];
    }

    public function inProcess(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Diproses,
            'processed_at' => now(),
            'processed_by_id' => User::factory(),
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Selesai,
            'resolution_notes' => 'Telah diperbaiki oleh teknisi.',
            'resolved_at' => now(),
            'resolved_by_id' => User::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Ditolak,
            'rejection_reason' => 'Bukan merupakan kerusakan fasilitas.',
            'rejected_at' => now(),
            'rejected_by_id' => User::factory(),
        ]);
    }
}
