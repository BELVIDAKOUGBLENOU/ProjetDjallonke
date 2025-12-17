<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCommunity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $communityId = session('selected_community');
        if (!$communityId) {
            SetCommunityContext::selectFirst();
            $communityId = session('selected_community');
            if (!$communityId)
                return redirect()->route('my-communities')->with('error', 'Please select a community to continue.');
        }
        return $next($request);
    }
}
