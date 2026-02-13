<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Auth\RemoteAuthController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                RemoteAuthController::storeRedirectPath();
                if (RemoteAuthController::isRedirectionAvailable()) {
                    return RemoteAuthController::redirectToExtern(Auth::user());
                }
                return redirect()->intended($this->redirectPath());
            }
            return $next($request);
        })->only('showLoginForm');

        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        RemoteAuthController::storeRedirectPath();
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {


        if (RemoteAuthController::isRedirectionAvailable()) {
            return RemoteAuthController::redirectToExtern(Auth::user());
        }

        // Redirection par défaut pour les autres cas (navigateurs web, etc.)
        return redirect()->intended($this->redirectPath());
    }
}
