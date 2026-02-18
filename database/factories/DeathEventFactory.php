<?php

namespace Database\Factories;

use App\Models\DeathEvent;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeathEvent>
 */
class DeathEventFactory extends Factory
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
            'cause' => fake()->words(3, true),
            'death_place' => fake()->city(),
        ];
    }
}
