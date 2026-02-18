<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileSyncController extends Controller
{
    /**
     * Get user profile and active sessions.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $currentSessionId = session()->getId();
        $sessions = DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($request, $currentSessionId) {
                return (object) [
                    'agent' => $this->createAgent($session),
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $currentSessionId,
                    'last_active' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });

        return response()->json([
            'user' => $user,
            'sessions' => $sessions
        ]);
    }

    /**
     * Create user agent details.
     */
    protected function createAgent($session)
    {
        return [
            'is_desktop' => true,
            'platform' => $this->getPlatform($session->user_agent),
            'browser' => $this->getBrowser($session->user_agent),
        ];

    }

    protected function getPlatform($userAgent)
    {
        if (str_contains($userAgent, 'Windows'))
            return 'Windows';
        if (str_contains($userAgent, 'Mac'))
            return 'macOS';
        if (str_contains($userAgent, 'Linux'))
            return 'Linux';
        if (str_contains($userAgent, 'Android'))
            return 'Android';
        if (str_contains($userAgent, 'iPhone'))
            return 'iOS';
        return 'Unknown';
    }

    protected function getBrowser($userAgent)
    {
        if (str_contains($userAgent, 'Chrome'))
            return 'Chrome';
        if (str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome'))
            return 'Safari';
        if (str_contains($userAgent, 'Firefox'))
            return 'Firefox';
        if (str_contains($userAgent, 'Edge'))
            return 'Edge';
        return 'Browser';
    }


    /**
     * Update user profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user,
        ]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès.',
        ]);
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log user out
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Compte supprimé avec succès.',
        ]);
    }
}
