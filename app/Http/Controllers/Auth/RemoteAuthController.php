<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class RemoteAuthController extends Controller
{
    /**
     * Vérifie si une URL de redirection est présente en session.
     */
    static function isRedirectionAvailable(): bool
    {
        return (Session::has('redirect_to') && !empty(Session::get('redirect_to')));
    }

    /**
     * Gère la redirection vers Flutter ou VueJS après login.
     */
    static function redirectToExtern(User $user, $default = null)
    {
        // 1. Vérification des droits d'accès
        // On autorise si l'user a des communautés mobiles OU si c'est une redirection Web
        $redirectTo = Session::get('redirect_to');
        $isMobileLink = !str_starts_with($redirectTo, 'http');

        $hasMobileAccess = $user->mobileAppCommunities()->exists();

        // Si c'est une demande mobile mais que l'user n'a pas accès
        if ($isMobileLink && !$hasMobileAccess) {
            auth()->logout();
            Session::forget('redirect_to');
            return response()->view('auth.mobile-not-authorized');
        }

        if (self::isRedirectionAvailable()) {
            // 2. Génération du Token Stateless (pour Flutter ET VueJS)
            $tokenName = $isMobileLink ? 'MobileToken' : 'WebToken';
            $token = $user->createToken($tokenName)->plainTextToken;

            // 3. Construction de l'URL finale avec le token
            $separator = str_contains($redirectTo, '?') ? '&' : '?';
            $finalUrl = $redirectTo . $separator . "token=$token";

            // 4. Choix du mode de redirection
            if ($isMobileLink) {
                // Pour le Mobile : On affiche la vue avec le bouton "Continuer"
                return response()->view('auth.google-redirect', [
                    'redirect_url' => $finalUrl,
                    'user' => $user
                ]);
            } else {
                // Pour le Web (VueJS) : Redirection directe (pas besoin de bouton)
                Session::forget('redirect_to');
                return redirect()->away($finalUrl);
            }
        }

        return redirect()->intended('/home');
    }

    /**
     * Stocke l'URL de retour en session avant le login.
     */
    static function storeRedirectPath()
    {
        $url = request()->get('redirect_uri') ?? request()->get('redirect_to');
        $isMobileLink = !str_starts_with($url, 'http');
        // dd('Mobile Link: ' . ($isMobileLink ? 'Yes' : 'No') . ' | URL: ' . $url);
        if ($url) {
            Session::put('redirect_to', $url);
        }
    }
}
