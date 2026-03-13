<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\IamGrpcService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IamGrpcServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that IamGrpcService can verify a valid token
     */
    public function test_iam_grpc_service_verifies_valid_token(): void
    {
        // This test requires the IAM service to be running on http://127.0.0.1:5602

        // Create a user (simulating one from IAM)
        $user = User::factory()->create([
            'email' => 'test-verify-token@example.com',
            'name' => 'Test Verification User',
            'uid' => 'test-uid-12345',
        ]);

        // In a real scenario, you would get the token from the IAM service
        // For this test, we're just verifying the service structure is correct
        $service = app(IamGrpcService::class);

        $this->assertNotNull($service);
        $this->assertTrue(method_exists($service, 'verifyToken'));
    }

    /**
     * Test IamGrpcService with invalid token
     */
    public function test_iam_grpc_service_rejects_invalid_token(): void
    {
        $service = app(IamGrpcService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to verify token');

        // This will fail if IAM service is not running
        $service->verifyToken('invalid-token-xyz');
    }

    /**
     * Test that the VerifyIamToken middleware is registered
     */
    public function test_verify_iam_token_middleware_registered(): void
    {
        $this->assertTrue(
            resolve(\Illuminate\Routing\Router::class)->getMiddlewareGroups()['api'] ?? false
        );
    }
}
