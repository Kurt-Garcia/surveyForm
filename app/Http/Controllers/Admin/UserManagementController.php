<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // Show the form to create a new user
    public function create()
    {
        return view('admin.users.create');
    }

    // Store the new user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name'),
                Rule::unique('admin_users', 'name'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('admin_users', 'email'),
            ],
            'password' => 'required|string|min:8|confirmed',
            'contact_number' => [
                'required',
                'string',
                'max:13',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+63|09|9)\d+$/', $value)) {
                        $fail('The contact number must start with +63, 09, or 9.');
                    }
                },
            ],
            'sbu' => 'required|string|in:FDC,FUI',
            'site' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Format contact number
        $contactNumber = $request->contact_number;
        if (str_starts_with($contactNumber, '+63')) {
            // Keep as is
        } elseif (str_starts_with($contactNumber, '09')) {
            $contactNumber = '+63' . substr($contactNumber, 1);
        } elseif (str_starts_with($contactNumber, '9')) {
            $contactNumber = '+63' . $contactNumber;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $contactNumber,
            'sbu' => $request->sbu,
            'site' => $request->site,
        ]);

        return redirect()->route('admin.users.create')->with('success', 'User created successfully!');
    }
}