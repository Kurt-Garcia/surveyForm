<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Services\UserLogService;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            
            // Check if admin account is disabled
            if ($admin && $admin->status == 0) {
                Log::info('Admin login attempt - account disabled:', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
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
                
                // Store admin ID in session for additional fallback
                $request->session()->put('disabled_admin_id', $admin->id);
                
                Log::info('Admin disabled cache set:', [
                    'cache_key' => 'disabled_admin_' . $admin->id,
                    'backup_cache' => 'disabled_admin_backup',
                    'session_key' => 'disabled_admin_id'
                ]);
                
                Auth::guard('admin')->logout();
                return redirect()->route('account.disabled', ['aid' => $admin->id]);
            }
            
            $request->session()->regenerate();
            // Log admin login
            UserLogService::logLogin($admin, 'admin', $request);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Log logout before actually logging out
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            UserLogService::logLogout($admin, 'admin', $request);
        }
        
        // Preserve disabled admin session data before invalidating session
        $disabledAdminId = $request->session()->get('disabled_admin_id');
        
        // If there's a disabled admin ID, refresh the cache before logout
        if ($disabledAdminId) {
            $admin = Admin::find($disabledAdminId);
            if ($admin && $admin->status == 0) {
                // Refresh cache with current DB data
                Cache::put('disabled_admin_' . $admin->id, [
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 900);
                
                Cache::put('disabled_admin_backup', [
                    'id' => $admin->id,
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 900);
                
                Log::info('Refreshed admin cache during logout:', [
                    'admin_id' => $admin->id,
                    'disabled_reason' => $admin->disabled_reason
                ]);
            }
        }
        
        Auth::guard('admin')->logout();
        
        // Clear all authentication-related session data to prevent cross-contamination
        $request->session()->forget([
            'is_admin',
            'user_site_ids', 
            'rating_type'
        ]);
        
        // Only regenerate token, don't invalidate entire session to preserve other guards
        $request->session()->regenerateToken();
        
        // Restore disabled admin session data if it existed
        if ($disabledAdminId) {
            $request->session()->put('disabled_admin_id', $disabledAdminId);
            Log::info('Preserved disabled admin session data after logout:', ['admin_id' => $disabledAdminId]);
        }
        
        return redirect()->route('login');
    }
}