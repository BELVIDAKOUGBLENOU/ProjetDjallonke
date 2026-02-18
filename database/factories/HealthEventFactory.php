<?php

namespace Database\Factories;

use App\Models\HealthEvent;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthEvent>
 */
class HealthEventFactory extends Factory
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
            'health_type' => fake()->randomElement(HealthEvent::HEALTH_TYPES),
            'product' => fake()->word(),
            'result' => fake()->sentence(),
        ];
    }
}
