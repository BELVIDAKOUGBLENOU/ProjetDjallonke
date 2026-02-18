<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetCommunityContextFrontend
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = auth()->user();
        $communityId = $request->header('X-Community-ID');

        if ($communityId && $user->communities()->where('communities.id', $communityId)->exists()) {
            setPermissionsTeamId($communityId);
        } elseif ($communityId == 0) {
            // c'est probablement une super-admin qui fait lq requete
            setPermissionsTeamId(0);
            // mais on verifie quand meme qu'il a des roles et permissions globales
            if ($user->getRoleNames()->count() == 0 || $user->getAllPermissions()->count() == 0) {
                return response()->json(['message' => 'Forbidden: you are not admin'], 403);
            }
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
