<?php

namespace App\Http\Controllers\Auth;

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

    use AuthenticatesUsers {
        username as getUsernameField;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/index';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    protected function attemptLogin(Request $request)
    {
        // Clear any existing sessions to prevent guard conflicts
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // First try admin authentication
        if (Auth::guard('admin')->attempt(['name' => $request->name, 'password' => $request->password])) {
            session(['is_admin' => true]);
            return true;
        }

        // If admin auth fails, try regular user authentication
        if (Auth::guard('web')->attempt(['name' => $request->name, 'password' => $request->password], $request->filled('remember'))) {
            session(['is_admin' => false]);
            return true;
        }

        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        if (session('is_admin') === true) {
            return redirect()->intended('/admin/dashboard');
        }

        // For regular users, set user_site_ids and rating type in session
        $userSiteIds = $user->sites->pluck('id')->toArray();
        session(['user_site_ids' => $userSiteIds]);
        
        // Set rating type for the user
        $ratingType = rand(0, 1) ? 'radio' : 'star';
        session(['rating_type' => $ratingType]);
        
        return redirect()->intended('/index');
    }

    public function logout(Request $request)
    {
        // Logout from all guards
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Always redirect to welcome page
        return redirect('/');
    }
}
