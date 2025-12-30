<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Person;
use App\Models\Premise;
use App\Models\Animal;

class PullEndpointsTest extends TestCase
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
        Person::factory(10)->create();

        // Seed Premises (will create Villages, Communities, etc.)
        Premise::factory(10)->create();

        // Seed Animals (will create AnimalIdentifiers)
        Animal::factory(20)->create();

        $this->withHeaders(['X-Community-ID' => $community->id]);
        // disable middleware in tests to avoid permission complexity
        $this->withoutMiddleware();
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($community->id);
        }
        return $user;
    }

    public function test_pull_communities_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/community');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Community response ", $response->json());
    }

    public function test_pull_countries_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/countries');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Countries response ", $response->json());
    }

    public function test_pull_persons_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/persons');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Persons response ", $response->json());
    }

    public function test_pull_premises_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/premises');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Premises response ", $response->json());
    }

    public function test_pull_animals_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/animals');
        $response->assertStatus(200);
        Log::info("Animal response ", $response->json());
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_animals_identifiers_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/animals-identifiers');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Animal identifiers response ", $response->json());
    }

    public function test_pull_person_roles_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/person-roles');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Person roles response ", $response->json());
    }
}
