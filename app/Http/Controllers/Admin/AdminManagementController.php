<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin_users',
            'password' => 'required|string|min:8|confirmed',
        ]);

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
    }
}