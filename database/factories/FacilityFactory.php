<?php

namespace Database\Factories;

use App\Enums\FacilityType;
use App\Enums\OperationalStatus;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        $type = fake()->randomElement(FacilityType::cases());

        return [
            'name' => 'Fasilitas ' . fake()->unique()->words(2, true),
            'type' => $type,
            'location' => 'Gedung ' . fake()->randomElement(['A', 'B', 'C', 'Pusat']) . ' Lantai ' . fake()->numberBetween(1, 4),
            'capacity' => $type === FacilityType::Alat ? null : fake()->numberBetween(20, 100),
            'description' => fake()->paragraph(),
            'is_active' => true,
            'operational_status' => OperationalStatus::DapatDigunakan,
        ];
    }

    public function inRepair(): static
    {
        return $this->state(fn (array $attributes) => [
            'operational_status' => OperationalStatus::DalamPerbaikan,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
