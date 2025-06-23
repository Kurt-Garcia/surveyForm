<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            $admin = Auth::guard('admin')->user();
            
            // Check if admin account is disabled
            if ($admin && $admin->status == 0) {
                Auth::guard('admin')->logout();
                // Store redirect flag in session
                session(['redirect_to_disabled' => true]);
                return false;
            }
            
            session(['is_admin' => true]);
            return true;
        }

        // If admin auth fails, try regular user authentication
        if (Auth::guard('web')->attempt(['name' => $request->name, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::guard('web')->user();
            
            // Check if user account is disabled
            if ($user && $user->status == 0) {
                // Store user info in cache temporarily (5 minutes expiration)
                Cache::put('disabled_user_' . $user->id, [
                    'disabled_reason' => $user->disabled_reason,
                    'account_type' => 'User'
                ], 300);
                
                // Store user info in a global cache as backup
                Cache::put('disabled_user_backup', [
                    'id' => $user->id,
                    'disabled_reason' => $user->disabled_reason,
                    'account_type' => 'User'
                ], 300);
                
                Auth::guard('web')->logout();
                // Store redirect flag in session
                session(['redirect_to_disabled' => true, 'disabled_user_id' => $user->id]);
                return false;
            }
            
            session(['is_admin' => false]);
            return true;
        }

        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        // Safety check - if user is null, something went wrong
        if (!$user) {
            return redirect('/');
        }
        
        // Admin redirects are handled in the login method, so this should only handle regular users
        if (session('is_admin') === true) {
            // This shouldn't happen with our new flow, but just in case
            return redirect('/admin/dashboard');
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

    /**
     * Get the failed login response instance with custom message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request, $message = null)
    {
        // Check if this is a disabled account
        if (session('redirect_to_disabled')) {
            $userId = session('disabled_user_id');
            session()->forget(['redirect_to_disabled', 'disabled_user_id']);
            
            if ($userId) {
                return redirect()->route('account.disabled', ['uid' => $userId]);
            } else {
                return redirect()->route('account.disabled');
            }
        }
        
        $errors = $message ? [$this->username() => $message] : [$this->username() => trans('auth.failed')];
        
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            // Check if this was an admin login
            if (session('is_admin') === true) {
                return redirect()->intended('/admin/dashboard');
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
