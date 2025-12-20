<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class MobileAuthController extends Controller
{
    static function isRedirectionAvailable(): bool
    {
        return (Session::has('redirect_to') && !empty(Session::get('redirect_to')));

    }
    static function redirectForFlutter(User $user, $default = null)
    {
        if (Session::has('redirect_to')) {
            $redirectTo = Session::get('redirect_to');
            // Session::forget('redirect_to');
            // dd($redirectTo);
            $token = $user->createToken('Google')->plainTextToken;
            $redirectTo .= "?token=$token";
            // Session::forget('redirect_to');
            // auth()->login($user);
            // return response('', 302)
            //     ->header('Location', $redirectTo);
            // return redirect()->away($redirectTo);

            return response()->view('auth.google-redirect', ['redirect_url' => $redirectTo, "user" => $user]);
        }

    }
    //
    static function storeRedirectPath()
    {

        if (request()->has('redirect_uri')) {
            Session::put('redirect_to', request()->get('redirect_uri'));
        }
        if (request()->has('redirect_to')) {
            Session::put('redirect_to', request()->get('redirect_to'));
        }
    }
}
