<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Community>
 */
class CommunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'creation_date' => fake()->date(),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'country_id' => Country::inRandomOrder()->first()->id ?? Country::factory(),
        ];
    }
}
