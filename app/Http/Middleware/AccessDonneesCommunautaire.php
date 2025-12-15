<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CommunityMembership;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessDonneesCommunautaire
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else {

            $request->validate([
                'community_id' => [
                    'required',
                    'integer',
                    Rule::exists('community_memberships', 'community_id')
                        ->where('user_id', Auth::user()?->id)
                ],
            ]);

            # code...
        }
        return $next($request);
    }
}
