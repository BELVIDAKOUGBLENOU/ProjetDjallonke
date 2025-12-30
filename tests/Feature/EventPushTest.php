<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Animal;
use App\Models\Person;

class EventPushTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        // create community context and seed minimal models required by events
        $country = \App\Models\Country::create(['name' => 'Testland', 'code_iso' => 'TL']);
        $community = \App\Models\Community::create([
            'name' => 'Test Community',
            'creation_date' => now()->toDateString(),
            'created_by' => $user->id,
            'country_id' => $country->id,
        ]);
        $user->communities()->attach($community->id, ['role' => 'FARMER', 'added_at' => now()]);
        // Seed Person, Premise, Animal
        \App\Models\Person::factory(3)->create();
        \App\Models\Premise::factory(3)->create();
        \App\Models\Animal::factory(5)->create();

        $this->withHeaders(['X-Community-ID' => $community->id]);
        $this->withoutMiddleware();
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($community->id);
        }
        return $user;
    }

    public function test_push_health_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'health_type' => 'vaccine']]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/health-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Health events push response ", $response->json());
    }

    public function test_push_movement_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'destination_premise_uid' => $animal->premise->uid]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/movement-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Movement events push response ", $response->json());
    }

    public function test_push_transaction_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $person = Person::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'buyer_uid' => $person->uid, 'seller_uid' => $person->uid, 'price' => 100]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/transaction-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Transaction events push response ", $response->json());
    }

    public function test_push_reproduction_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'reproduction_type' => 'AI']]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/reproduction-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Reproduction events push response ", $response->json());
    }

    public function test_push_birth_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/birth-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Birth events push response ", $response->json());
    }

    public function test_push_milk_records_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'milk_quantity' => 5]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/milk-records?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Milk records push response ", $response->json());
    }

    public function test_push_death_events_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/death-events?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Death events push response ", $response->json());
    }

    public function test_push_weight_records_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'weight' => 100]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/weight-records?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Weight records push response ", $response->json());
    }
}
