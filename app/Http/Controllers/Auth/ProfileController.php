<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web,admin']);
    }

    public function showProfileForm()
    {
        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $isAdmin = $guard === 'admin';
        return view('auth.passwords.profile', compact('isAdmin'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $user = $guard === 'admin'
            ? \App\Models\Admin::find(Auth::guard($guard)->id())
            : \App\Models\User::find(Auth::guard($guard)->id());
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password using the user instance and save
        $user->password = Hash::make($request->password);
        $user->updated_at = now();
        $user->save();

        // Optionally, re-login the user to refresh session
        Auth::guard($guard)->login($user);

        return back()->with('success', 'Password changed successfully!');
    }

    public function updateProfile(Request $request)
    {
        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $user = Auth::guard($guard)->user();

        $adminTable = (new \App\Models\Admin)->getTable();
        $userTable = (new \App\Models\User)->getTable();
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:' . ($guard === 'admin' ? $adminTable : $userTable) . ',email,' . $user->id,
            'contact_number' => 'required|string|max:20',
        ]);

        if ($guard === 'admin') {
            \App\Models\Admin::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'updated_at' => now(),
            ]);
        } else {
            \App\Models\User::where('id', $user->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function checkCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);
        $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
        $user = $guard === 'admin'
            ? \App\Models\Admin::find(Auth::guard($guard)->id())
            : \App\Models\User::find(Auth::guard($guard)->id());
        $isValid = \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password);
        return response()->json(['valid' => $isValid]);
    }
}