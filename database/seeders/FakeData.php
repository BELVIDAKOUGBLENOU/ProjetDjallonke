<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Person;
use App\Models\Premise;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FakeData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Seed Persons
        Person::factory(10)->create();

        // Seed Premises (will create Villages, Communities, etc.)
        Premise::factory(10)->create();

        // Seed Animals (will create AnimalIdentifiers)
        Animal::factory(20)->create();
    }
}
