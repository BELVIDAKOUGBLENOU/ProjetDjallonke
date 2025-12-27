<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SetCommunityContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $communityId = session('selected_community');
        $communityIdFromUser = $request->has('community_id') ? $request->input('community_id') : null;
        $user = auth()->user();
        // Vérifie si l'utilisateur a au moins un rôle global (team 0)
        $hasGlobalRole = false;
        if ($user) {
            setPermissionsTeamId(0);
            $hasGlobalRole = DB::table('model_has_roles')
                ->where('model_type', get_class($user))
                ->where('model_id', $user->id)
                ->where('community_id', 0)
                ->exists();
            // dd($hasGlobalRole);
            setPermissionsTeamId(null);
        }
        if ($hasGlobalRole) {
            session(['selected_community' => 0]);
            $communityId = 0;
            setPermissionsTeamId(0);
            return $next($request);
        }

        if ($communityIdFromUser !== null) {
            $communityId = $communityIdFromUser;
            session(['selected_community' => $communityIdFromUser]);
        }

        $user = auth()->user();

        // If no community selected, or user is not a member, select first
        if (!$communityId) {
            self::selectFirst();
        } elseif ($user && $user->communities()->where('communities.id', $communityId)->exists()) {
            setPermissionsTeamId($communityId);
        } else {
            // User is not a member of the selected community anymore
            session()->forget('selected_community');
            self::selectFirst();
        }

        return $next($request);
    }
    public static function selectFirst()
    {

        if (auth()->check()) {
            $user = auth()->user();
            $firstCommunity = $user->communities()->first();
            if ($firstCommunity) {
                session(['selected_community' => $firstCommunity->id]);
                setPermissionsTeamId($firstCommunity->id);
            }
        }
    }
}
