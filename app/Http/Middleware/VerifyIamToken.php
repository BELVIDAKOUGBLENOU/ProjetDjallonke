<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\IamGrpcService;
use App\Services\IamM2M;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyIamToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = IamM2M::verifyJWT($token);

        if (!$user) {
            $iamhost = '';
            if (env("APP_ENV") === "local") {
                $iamhost = IamM2M::getIamHost();

            }
            return response()->json(['error' => 'Invalid token' . $iamhost], 401);
        }
        $user = User::firstOrCreate(
            ['email' => $user['email']],
            [
                'name' => $user['name'] ?? $user['email'],
                'uid' => $user['uid'] ?? null,
                'email_verified_at' => $user['email_verified_at'] ?? null,
                'created_at' => $user['created_at'] ?? now(),
                'updated_at' => $user['updated_at'] ?? now(),
                'fcm_token' => $user['fcm_token'] ?? null,
            ]
        );
        // Injecte l'utilisateur dans la requête
        $request->merge(['auth_user' => $user]);
        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        Auth::login($user);
        // return response()->json(['error' => 'Invalid token'], 401);


        return $next($request);
    }
}
