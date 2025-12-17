<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if ($communityIdFromUser) {
            $communityId = $communityIdFromUser;
            session(['selected_community' => $communityIdFromUser]);
        }
        if ($communityId) {
            $user = auth()->user();

            if ($user && $user->communities()->where('communities.id', $communityId)->exists()) {
                // \Spatie\Permission\PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.' . $communityId;
                // // If using Spatie Permission with teams/tenants
                // // setPermissionsTeamId($communityId);
                // // Since the user mentioned setPermissionsTeamId specifically:
                // if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($communityId);
                // } elseif (method_exists(app(\Spatie\Permission\PermissionRegistrar::class), 'setPermissionsTeamId')) {
                //     app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($communityId);
                // }
            } else {
                // User is not a member of the selected community anymore
                session()->forget('selected_community');
                self::selectFirst();
            }
        } else {
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
