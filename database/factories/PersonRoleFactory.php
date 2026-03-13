<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Person;
use App\Models\PersonRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PersonRoleFactory extends Factory
{
    protected $model = PersonRole::class;

    public function definition()
    {
        $person = Person::inRandomOrder()->first() ?? Person::factory()->create();
        $animal = Animal::inRandomOrder()->first() ?? Animal::factory()->create();

        return [
            'uid' => (string) Str::uuid(),
            'version' => 1,
            'person_id' => $person->id,
            'animal_id' => $animal->id,
            'role_type' => $this->faker->randomElement(['OWNER', 'DEALER', 'TRANSPORTER']),
        ];
    }
}
