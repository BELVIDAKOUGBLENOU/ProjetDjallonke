<?php

namespace Database\Factories;

use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnimalIdentifier>
 */
class AnimalIdentifierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->unique()->uuid(),
            'animal_id' => Animal::factory(),
            'type' => fake()->randomElement(['VISUAL', 'BRAND', 'TATTOO', 'RFID_EAR_TAG', 'RFID_INJECTABLE', 'RFID_BOLUS']),
            'code' => fake()->unique()->bothify('ID-####-####'),
            'active' => true,
        ];
    }
}
