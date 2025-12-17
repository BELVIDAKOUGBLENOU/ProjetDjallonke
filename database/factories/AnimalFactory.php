<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\AnimalIdentifier;
use App\Models\Premise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animal>
 */
class AnimalFactory extends Factory
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
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'premises_id' => Premise::inRandomOrder()->first()->id ?? Premise::factory(),
            'species' => fake()->randomElement(['CAPRINE', 'OVINE',]),
            'sex' => fake()->randomElement(['M', 'F']),
            'birth_date' => fake()->date(),
            'life_status' => fake()->randomElement(['Alive', 'Dead', 'Sold']),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Animal $animal) {
            AnimalIdentifier::factory()->create([
                'animal_id' => $animal->id,
            ]);
        });
    }
}
