<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\IamGrpcService;
use Closure;
use Illuminate\Http\Request;
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

        if (! $token) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = app(IamGrpcService::class)->verifyToken($token);

        if (! $user) {
            return response()->json(['error' => 'Invalid token'], 401);
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

        return $next($request);
    }
}
