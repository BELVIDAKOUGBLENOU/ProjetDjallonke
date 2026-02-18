<?php

namespace Database\Factories;

use App\Models\MilkRecord;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MilkRecord>
 */
class MilkRecordFactory extends Factory
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
            'volume_liters' => fake()->randomFloat(2, 0.5, 4.0),
            'period' => fake()->randomElement(['MORNING', 'EVENING']),
        ];
    }
}
