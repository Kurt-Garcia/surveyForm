<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Admin;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
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
                // Store admin info in cache temporarily (5 minutes expiration)
                Cache::put('disabled_admin_' . $admin->id, [
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin'
                ], 300);
                
                // Store admin info in a global cache as backup
                Cache::put('disabled_admin_backup', [
                    'id' => $admin->id,
                    'disabled_reason' => $admin->disabled_reason,
                    'account_type' => 'Admin'
                ], 300);
                
                Auth::guard('admin')->logout();
                return redirect()->route('account.disabled', ['aid' => $admin->id]);
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}