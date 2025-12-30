<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EventPullTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        // set a team id to allow whereHas filters to run; adjust as needed
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId(null);
        }
        return $user;
    }

    public function test_pull_health_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/health-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Health events response ", $response->json());
    }

    public function test_pull_movement_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/movement-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Movement events response ", $response->json());
    }

    public function test_pull_transaction_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/transaction-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Transaction events response ", $response->json());
    }

    public function test_pull_reproduction_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/reproduction-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Reproduction events response ", $response->json());
    }

    public function test_pull_birth_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/birth-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Birth events response ", $response->json());
    }

    public function test_pull_milk_records_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/milk-records');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Milk records response ", $response->json());
    }

    public function test_pull_death_events_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/death-events');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Death events response ", $response->json());
    }

    public function test_pull_weight_records_returns_json()
    {
        $this->authenticate();
        $response = $this->getJson('/api/pull/weight-records');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        Log::info("Weight records response ", $response->json());

    }
}
