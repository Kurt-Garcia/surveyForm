<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if the user account is disabled (status = 0)
            if ($user && isset($user->status) && $user->status == 0) {
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
                
                // Logout the user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect with user ID parameter
                return redirect()->route('account.disabled', ['uid' => $user->id]);
            }
        }

        // Check if admin is authenticated
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            // Check if the admin account is disabled (status = 0)
            if ($admin && isset($admin->status) && $admin->status == 0) {
                // Store admin info in cache temporarily (5 minutes expiration)
                Cache::put('disabled_admin_' . $admin->id, [
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 300); // 5 minutes instead of 1 minute
                
                // Store admin info in a global cache as backup
                Cache::put('disabled_admin_backup', [
                    'id' => $admin->id,
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin',
                    'disabled_at' => $admin->disabled_at
                ], 300);
                
                // Logout the admin
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect with admin ID parameter
                return redirect()->route('account.disabled', ['aid' => $admin->id]);
            }
        }

        return $next($request);
    }
}
