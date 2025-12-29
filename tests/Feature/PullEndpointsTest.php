<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

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
    }

    public function test_pull_countries_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/countries');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_persons_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/persons');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_premises_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/premises');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_animals_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/animals');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_animals_identifiers_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/animals-identifiers');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_pull_person_roles_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/person-roles');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
