<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IamM2M
{
    private string $iamHost;

    public static function getIamHost(): string
    {
        return env('API_GATEWAY_URL') . '/iam';
    }

    public function __construct()
    {
        $this->iamHost = self::getIamHost();
    }

    public static function getToken(): ?string
    {
        if (Cache::has('iam_access_token')) {
            return Cache::get('iam_access_token');
        }
        return static::fetchFreshToken();

    }

    public static function fetchFreshToken(): ?string
    {
        try {
            $http = new \GuzzleHttp\Client();

            $response = $http->post(self::getIamHost() . '/oauth/token', [
                'headers' => ['Accept' => 'application/json'],
                'form_params' => config('services.iam.credentials'),
            ]);
            $json = json_decode($response->getBody(), true);
            $type = $json['token_type'] ?? 'Bearer';
            $tok = $json['access_token'] ?? null;
            $expiresIn = $json['expires_in'] ?? 3600;
            Cache::put('iam_access_token', $type . ' ' . $tok, $expiresIn - 60); // Cache le token en soustrayant 60 secondes pour éviter les expirations inattendues
            return $type . ' ' . $tok;

        } catch (\Exception $e) {
            Log::error('IAM token fetch failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public static function verifyJWT(string $token): ?array
    {

        try {
            $authToken = self::getToken();
            $http = new \GuzzleHttp\Client();
            $response = $http->get(self::getIamHost() . '/api/m2m/get-user-by-jwt', [
                'headers' => [
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ],
                'query' => ['token' => $token],
            ]);
            $responseData = json_decode($response->getBody(), true);

            return $responseData ?? null;
        } catch (\Exception $e) {
            Log::error('IAM token verification failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
