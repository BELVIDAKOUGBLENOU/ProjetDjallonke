<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Premise>
 */
class PremiseFactory extends Factory
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
            'village_id' => Village::inRandomOrder()->first()->id ?? Village::factory(),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'community_id' => Community::inRandomOrder()->first()->id ?? Community::factory(),
            'code' => fake()->unique()->bothify('PR-####'),
            'address' => fake()->address(),
            'gps_coordinates' => fake()->latitude() . ',' . fake()->longitude(),
            'type' => fake()->randomElement(['FARM', 'MARKET', 'SLAUGHTERHOUSE', 'PASTURE', 'TRANSPORT']),
            'health_status' => fake()->randomElement(['Healthy', 'Quarantined', 'Unknown']),
        ];
    }
}
