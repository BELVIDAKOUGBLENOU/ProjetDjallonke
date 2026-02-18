<?php

namespace Database\Factories;

use App\Models\ReproductionEvent;
use App\Models\Event;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReproductionEvent>
 */
class ReproductionEventFactory extends Factory
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
            'repro_type' => fake()->randomElement(ReproductionEvent::REPRO_TYPES),
            'mother_id' => Animal::factory(),
            'father_id' => Animal::factory(),
        ];
    }
}
