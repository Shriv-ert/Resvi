<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityPhoto>
 */
class FacilityPhotoFactory extends Factory
{
    protected $model = FacilityPhoto::class;

    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'photo_path' => 'facilities/sample-' . fake()->numberBetween(1, 10) . '.jpg',
            'is_primary' => false,
            'sort_order' => fake()->numberBetween(0, 5),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }
}
