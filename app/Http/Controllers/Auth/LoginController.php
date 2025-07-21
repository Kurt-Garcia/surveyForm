<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\UserLogService;

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
        // Preserve disabled admin session data before clearing sessions
        $disabledAdminId = $request->session()->get('disabled_admin_id');
        
        // Clear any existing sessions to prevent guard conflicts
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Restore disabled admin session data if it existed
        if ($disabledAdminId) {
            $request->session()->put('disabled_admin_id', $disabledAdminId);
        }

        // First try admin authentication
        if (Auth::guard('admin')->attempt(['name' => $request->name, 'password' => $request->password])) {
            $admin = Auth::guard('admin')->user();
            
            // Check if admin account is disabled
            if ($admin && $admin->status == 0) {
                Log::info('Admin login attempt via LoginController - account disabled:', [
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'disabled_reason' => $admin->disabled_reason
                ]);
                
                // Store admin info in cache temporarily (15 minutes expiration for better reliability)
                Cache::put('disabled_admin_' . $admin->id, [
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 900); // 15 minutes instead of 5
                
                // Store admin info in a global cache as backup
                Cache::put('disabled_admin_backup', [
                    'id' => $admin->id,
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 900); // 15 minutes instead of 5
                
                Log::info('Admin disabled cache set via LoginController:', [
                    'cache_key' => 'disabled_admin_' . $admin->id,
                    'backup_cache' => 'disabled_admin_backup'
                ]);
                
                Auth::guard('admin')->logout();
                // Store redirect flag and admin ID in session
                session(['redirect_to_disabled' => true, 'disabled_admin_id' => $admin->id]);
                return false;
            }
            
            session(['is_admin' => true]);
            // Log admin login
            UserLogService::logLogin($admin, 'admin', $request);
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
                    'account_type' => 'User',
                    'disabled_at' => $user->disabled_at
                ], 300);
                
                // Store user info in a global cache as backup
                Cache::put('disabled_user_backup', [
                    'id' => $user->id,
                    'disabled_reason' => $user->disabled_reason,
                    'account_type' => 'User',
                    'disabled_at' => $user->disabled_at
                ], 300);
                
                Auth::guard('web')->logout();
                // Store redirect flag in session
                session(['redirect_to_disabled' => true, 'disabled_user_id' => $user->id]);
                return false;
            }
            
            session(['is_admin' => false]);
            // Log user login
            UserLogService::logLogin($user, 'user', $request);
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
        // Log logout before actually logging out
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            UserLogService::logLogout($admin, 'admin', $request);
        }
        
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            UserLogService::logLogout($user, 'user', $request);
        }
        
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
            $adminId = session('disabled_admin_id');
            session()->forget(['redirect_to_disabled', 'disabled_user_id', 'disabled_admin_id']);
            
            if ($adminId) {
                return redirect()->route('account.disabled', ['aid' => $adminId]);
            } elseif ($userId) {
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
