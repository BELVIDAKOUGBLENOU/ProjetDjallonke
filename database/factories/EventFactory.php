<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->uuid(),
            'version' => 1,
            'animal_id' => Animal::factory(),
            'created_by' => User::factory(),
            'confirmed_by' => null,
            'source' => fake()->randomElement(Event::SOURCES),
            'event_date' => fake()->date(),
            'comment' => fake()->sentence(),
            'is_confirmed' => fake()->boolean(80),
        ];
    }
}
