<?php
namespace App\Services;

use GuzzleHttp\Exception\ClientException;
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

    public static function verifyJWT(string $token, int $attempt = 0): ?array
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

            return json_decode($response->getBody(), true) ?? null;

        } catch (ClientException $e) {
            Cache::forget('iam_access_token');
            if ($attempt < 1) {
                return self::verifyJWT($token, $attempt + 1);
            }
            Log::error('IAM verifyJWT failed', ['error' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('IAM verifyJWT failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Crée un nouvel utilisateur dans le service IAM.
     *
     * @param array $data  Champs requis: uid, name, email. Optionnel: fcm_token.
     */
    public static function addNewUser(array $data, int $attempt = 0): ?array
    {
        try {
            $authToken = self::getToken();
            $http = new \GuzzleHttp\Client();
            $response = $http->post(self::getIamHost() . '/api/m2m/add-new-user', [
                'headers' => [
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ],
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true)['data'] ?? null;
        } catch (ClientException $e) {
            Cache::forget('iam_access_token');
            if ($attempt < 1) {
                return self::addNewUser($data, $attempt + 1);
            }
            Log::error('IAM addNewUser failed', ['error' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('IAM addNewUser failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Met à jour un utilisateur IAM identifié par son uid.
     *
     * @param array $data  Champs modifiables: fcm_token.
     */
    public static function updateUser(string $uid, array $data, int $attempt = 0): ?array
    {
        try {
            $authToken = self::getToken();
            $http = new \GuzzleHttp\Client();
            $response = $http->post(self::getIamHost() . '/api/m2m/update-user/' . $uid, [
                'headers' => [
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ],
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true) ?? null;
        } catch (ClientException $e) {
            Cache::forget('iam_access_token');
            if ($attempt < 1) {
                return self::updateUser($uid, $data, $attempt + 1);
            }
            Log::error('IAM updateUser failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('IAM updateUser failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Supprime un utilisateur IAM identifié par son uid.
     */
    public static function deleteUser(string $uid, int $attempt = 0): ?array
    {
        try {
            $authToken = self::getToken();
            $http = new \GuzzleHttp\Client();
            $response = $http->delete(self::getIamHost() . '/api/m2m/delete-user/' . $uid, [
                'headers' => [
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true) ?? null;
        } catch (ClientException $e) {
            Cache::forget('iam_access_token');
            if ($attempt < 1) {
                return self::deleteUser($uid, $attempt + 1);
            }
            Log::error('IAM deleteUser failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('IAM deleteUser failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Régénère le mot de passe d'un utilisateur IAM identifié par son uid.
     * L'IAM envoie les nouveaux identifiants par notification à l'utilisateur.
     */
    public static function regeneratePassword(string $uid, int $attempt = 0): ?array
    {
        try {
            $authToken = self::getToken();
            $http = new \GuzzleHttp\Client();
            $response = $http->post(self::getIamHost() . '/api/m2m/regenerate-password/' . $uid, [
                'headers' => [
                    'Authorization' => $authToken,
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true) ?? null;
        } catch (ClientException $e) {
            Cache::forget('iam_access_token');
            if ($attempt < 1) {
                return self::regeneratePassword($uid, $attempt + 1);
            }
            Log::error('IAM regeneratePassword failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            Log::error('IAM regeneratePassword failed', ['uid' => $uid, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
