<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Person;
use App\Models\Premise;
use App\Models\PersonRole;
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
        $animals = Animal::factory(20)->create();

        // Seed PersonRoles linking persons and animals
        PersonRole::factory(30)->create();

        // Seed Events for Animals
        $animals->each(function ($animal) {
            // Health Events
            \App\Models\HealthEvent::factory(rand(0, 2))->create([
                'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
            ]);

            // Weight Records
            \App\Models\WeightRecord::factory(rand(1, 5))->create([
                'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
            ]);

            // Milk Records (if female - simplifying assumption or just random)
            if ($animal->sex === 'F') {
                \App\Models\MilkRecord::factory(rand(0, 5))->create([
                    'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
                ]);
            }

            // Movements
            if (rand(0, 10) > 7) {
                \App\Models\MovementEvent::factory()->create([
                    'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
                ]);
            }

            // Births
            if ($animal->sex === 'F' && rand(0, 1)) {
                \App\Models\BirthEvent::factory()->create([
                    'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
                ]);
            }
            // Death (only for some)
            if (rand(0, 100) > 95) {
                \App\Models\DeathEvent::factory()->create([
                    'event_id' => \App\Models\Event::factory()->state(['animal_id' => $animal->id]),
                ]);
            }
        });

        // Seed Independent Transaction Events
        \App\Models\TransactionEvent::factory(5)->create();

        // Seed independent Reproduction Events
        \App\Models\ReproductionEvent::factory(5)->create();
    }
}
