<?php

namespace Database\Factories;

use App\Models\WeightRecord;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeightRecord>
 */
class WeightRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'weight' => fake()->randomFloat(2, 2.5, 60.0),
            'age_days' => fake()->numberBetween(1, 3000),
            'measure_method' => fake()->randomElement(['SCALE', 'TAPE']),
        ];
    }
}
