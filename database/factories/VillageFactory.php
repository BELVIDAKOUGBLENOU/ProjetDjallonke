<?php

namespace Database\Factories;

use App\Models\SubDistrict;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Village>
 */
class VillageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'local_code' => fake()->unique()->numerify('V###'),
            'sub_district_id' => SubDistrict::inRandomOrder()->first()->id ?? SubDistrict::factory(),
        ];
    }
}
