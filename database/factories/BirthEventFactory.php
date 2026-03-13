<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BirthEvent>
 */
class BirthEventFactory extends Factory
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
            'mother_id' => Animal::factory(),
            'father_id' => Animal::factory(),
            'nb_alive' => fake()->numberBetween(1, 3),
            'nb_dead' => fake()->numberBetween(0, 1),
        ];
    }
}
