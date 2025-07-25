<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        
        // Load the user with relationships
        if ($guard === 'web') {
            $user = \App\Models\User::with(['sbus', 'sites'])->find(Auth::guard($guard)->id());
        } else {
            $user = \App\Models\Admin::with(['sbus', 'sites'])->find(Auth::guard($guard)->id());
        }
        
        return view('auth.passwords.profile', compact('isAdmin', 'user'));
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

        // Log activity for password change
        activity()
            ->event('updated')
            ->causedBy($user)
            ->withProperties([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'change_type' => 'password'
            ])
            ->log('Password has been updated');

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

        // Store old values for logging
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'contact_number' => $user->contact_number
        ];

        $newValues = [
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number
        ];

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

        // Log activity for profile update
        activity()
            ->event('updated')
            ->causedBy($user)
            ->withProperties([
                'user_id' => $user->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'change_type' => 'profile'
            ])
            ->log('Users personal info has been updated');

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

    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ]);

            $guard = Auth::guard('admin')->check() ? 'admin' : 'web';
            $user = $guard === 'admin'
                ? \App\Models\Admin::find(Auth::guard($guard)->id())
                : \App\Models\User::find(Auth::guard($guard)->id());

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Update user avatar field
            $user->avatar = $avatarPath;
            $user->updated_at = now();
            $user->save();

            // Log activity for avatar update
            activity()
                ->event('updated')
                ->causedBy($user)
                ->withProperties([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'change_type' => 'avatar',
                    'avatar_path' => $avatarPath
                ])
                ->log('Profile picture has been updated');

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'avatar_url' => asset('storage/' . $avatarPath)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Avatar upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar. Please try again.'
            ], 500);
        }
    }
}