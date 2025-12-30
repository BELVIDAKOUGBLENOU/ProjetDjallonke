<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Person;
use App\Models\Premise;
use App\Models\Animal;
use Illuminate\Support\Str;

class PushEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        // create a country and a community and attach the user so middleware passes
        $country = \App\Models\Country::create(['name' => 'Testland', 'code_iso' => 'TL']);
        $community = \App\Models\Community::create([
            'name' => 'Test Community',
            'creation_date' => now()->toDateString(),
            'created_by' => $user->id,
            'country_id' => $country->id,
        ]);
        $user->communities()->attach($community->id, ['role' => 'FARMER', 'added_at' => now()]);
        // Seed Persons
        Person::factory(5)->create();

        // Seed Premises
        Premise::factory(5)->create();

        // Seed Animals
        Animal::factory(5)->create();

        $this->withHeaders(['X-Community-ID' => $community->id]);
        // disable middleware in tests to avoid permission complexity
        $this->withoutMiddleware();
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($community->id);
        }
        return $user;
    }

    public function test_push_premises_returns_json()
    {
        $this->authenticate();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'code' => 'PUSH1', 'address' => 'Addr', 'gps_coordinates' => '0,0', 'type' => 'FARM']]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/premises?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Premises push response ", $response->json());
    }

    public function test_push_animals_returns_json()
    {
        $this->authenticate();
        $prem = Premise::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'premise_uid' => $prem->uid]]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/animals?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Animals push response ", $response->json());
    }

    public function test_push_animals_identifiers_returns_json()
    {
        $this->authenticate();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'animal_uid' => $animal->uid, 'type' => 'tag', 'code' => 'T123']]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/animals-identifiers?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Animal identifiers push response ", $response->json());
    }

    public function test_push_persons_returns_json()
    {
        $this->authenticate();
        $personUid = (string) Str::uuid();
        $payload = [
            'data' => [
                [
                    'uid' => $personUid,
                    'version' => 1,
                    'name' => 'Test Person',
                    'address' => 'Addr',
                    'phone' => '000',
                    'nationalId' => 'NID123'
                ]
            ]
        ];
        $response = $this->postJson('/api/push/persons', $payload);
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertTrue(array_key_exists('status', $json) || array_key_exists('statut', $json));
        $this->assertArrayHasKey('applied', $json);
        $this->assertArrayHasKey('conflicts', $json);
        $this->assertArrayHasKey('errors', $json);
        Log::info("Persons push response ", $json);
    }

    public function test_push_person_roles_returns_json()
    {
        $this->authenticate();
        $person = Person::first();
        $animal = Animal::first();
        $uid = (string) Str::uuid();
        $payload = ['data' => [['uid' => $uid, 'version' => 1, 'person_uid' => $person->uid, 'animal_uid' => $animal->uid, 'role_type' => 'OWNER']]];
        $query = http_build_query($payload);
        $response = $this->getJson('/api/push/person-roles?' . $query);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'applied', 'conflicts', 'errors']);
        Log::info("Person roles push response ", $response->json());
    }
}
