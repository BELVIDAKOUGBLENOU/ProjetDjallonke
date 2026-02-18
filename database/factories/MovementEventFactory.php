<?php

namespace Database\Factories;

use App\Models\MovementEvent;
use App\Models\Event;
use App\Models\Premise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovementEvent>
 */
class MovementEventFactory extends Factory
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
            'from_premises_id' => Premise::factory(),
            'to_premises_id' => Premise::factory(),
            'change_owner' => fake()->boolean(),
            'change_keeper' => fake()->boolean(),
        ];
    }
}
