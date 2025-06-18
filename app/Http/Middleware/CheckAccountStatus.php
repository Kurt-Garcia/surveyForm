<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                // Logout the user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to disabled account page
                return redirect()->route('account.disabled');
            }
        }

        // Check if admin is authenticated
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            // Check if the admin account is disabled (status = 0)
            if ($admin && isset($admin->status) && $admin->status == 0) {
                // Logout the admin
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to disabled account page
                return redirect()->route('account.disabled');
            }
        }

        return $next($request);
    }
}
