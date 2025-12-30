<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PersonRole;
use App\Models\Person;
use App\Models\Animal;
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
