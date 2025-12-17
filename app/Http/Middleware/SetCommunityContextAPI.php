<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCommunityContextAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $communityId = $request->header('X-Community-ID');
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = auth()->user();

        if ($communityId && $user->communities()->where('communities.id', $communityId)->exists()) {
            setPermissionsTeamId($communityId);
        } else {
            if ($communityId) {

                return response()->json(['message' => 'Forbidden: You are not a member of the selected community'], 403);
            } else {
                return response()->json(['message' => 'X-Community-ID header is missing'], 400);
            }
        }
        return $next($request);
    }
}
