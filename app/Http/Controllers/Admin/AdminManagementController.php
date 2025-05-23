<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['checkEmailAvailability', 'checkNameAvailability']);
    }
    
    /**
     * Check if an email is available (not used by any admin or user)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmailAvailability(Request $request): JsonResponse
    {
        $email = $request->query('email');
        
        if (!$email) {
            return response()->json([
                'exists' => false,
                'message' => 'Email is required'
            ]);
        }
        
        // Check if email exists in admin_users table
        $adminExists = Admin::where('email', $email)->exists();
        if ($adminExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This email is already registered as an admin user.'
            ]);
        }
        
        // Check if email exists in users table
        $userExists = User::where('email', $email)->exists();
        if ($userExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This email is already registered as a regular user.'
            ]);
        }
        
        return response()->json([
            'exists' => false,
            'message' => 'Email is available'
        ]);
    }
    
    /**
     * Check if a name is available (not used by any admin or user)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkNameAvailability(Request $request): JsonResponse
    {
        $name = $request->query('name');
        
        if (!$name) {
            return response()->json([
                'exists' => false,
                'message' => 'Name is required'
            ]);
        }
        
        // Check if name exists in admin_users table
        $adminExists = Admin::where('name', $name)->exists();
        if ($adminExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This name is already registered as an admin user.'
            ]);
        }
        
        // Check if name exists in users table
        $userExists = User::where('name', $name)->exists();
        if ($userExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This name is already registered as a regular user.'
            ]);
        }
        
        return response()->json([
            'exists' => false,
            'message' => 'Name is available'
        ]);
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            // Check if name exists in admin_users table
            if (Admin::where('name', $request->name)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This name is already registered as an admin user.');
            }
            
            // Check if name exists in users table
            if (User::where('name', $request->name)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This name is already registered as a regular user.');
            }
            
            // Check if email exists in admin_users table
            if (Admin::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This email is already registered as an admin user.');
            }
            
            // Check if email exists in users table
            if (User::where('email', $request->email)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This email is already registered as a regular user.');
            }

        // Check if the admin creation details match a user account
        $user = User::where('name', $request->name)
                    ->where('email', $request->email)
                    ->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'For security reasons, you cannot create an admin with these credentials as they match an existing user.');
        }

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Admin account created successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'An error occurred while creating the admin account: ' . $e->getMessage());
        }
    }
}