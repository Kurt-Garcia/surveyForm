<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web,admin']);
    }

    public function showChangePasswordForm()
    {
        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $isAdmin = $guard === 'admin';
        return view('auth.passwords.change', compact('isAdmin'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $user = Auth::guard($guard)->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password using the appropriate model to ensure timestamps are properly managed
        if ($guard === 'admin') {
            \App\Models\Admin::where('id', $user->id)->update([
                'password' => Hash::make($request->password),
                'updated_at' => now()
            ]);
        } else {
            \App\Models\User::where('id', $user->id)->update([
                'password' => Hash::make($request->password),
                'updated_at' => now()
            ]);
        }

        if ($guard === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Password changed successfully!');
        }

        return redirect()->route('home')
            ->with('success', 'Password changed successfully!');
    }
}