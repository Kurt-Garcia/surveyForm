<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['checkEmailAvailability', 'checkNameAvailability', 'checkContactNumberAvailability']);
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

    /**
     * Check if a contact number is available (not used by any admin or user)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkContactNumberAvailability(Request $request): JsonResponse
    {
        $contactNumber = $request->query('contact_number');
        
        if (!$contactNumber) {
            return response()->json([
                'exists' => false,
                'message' => 'Contact number is required'
            ]);
        }
        
        // Generate all possible formats to check
        $formatsToCheck = [];
        
        if (str_starts_with($contactNumber, '+63')) {
            $formatsToCheck[] = $contactNumber; // +639123456789
            $formatsToCheck[] = '0' . substr($contactNumber, 3); // 09123456789
            $formatsToCheck[] = substr($contactNumber, 3); // 9123456789
        } elseif (str_starts_with($contactNumber, '09')) {
            $formatsToCheck[] = $contactNumber; // 09123456789
            $formatsToCheck[] = '+63' . substr($contactNumber, 1); // +639123456789
            $formatsToCheck[] = substr($contactNumber, 1); // 9123456789
        } elseif (str_starts_with($contactNumber, '9')) {
            $formatsToCheck[] = $contactNumber; // 9123456789
            $formatsToCheck[] = '+63' . $contactNumber; // +639123456789
            $formatsToCheck[] = '0' . $contactNumber; // 09123456789
        }
        
        // Check if any format exists in admin_users table
        $adminExists = Admin::whereIn('contact_number', $formatsToCheck)->exists();
        if ($adminExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This contact number is already registered as an admin user.'
            ]);
        }
        
        // Check if any format exists in users table
        $userExists = User::whereIn('contact_number', $formatsToCheck)->exists();
        if ($userExists) {
            return response()->json([
                'exists' => true,
                'message' => 'This contact number is already registered as a regular user.'
            ]);
        }
        
        return response()->json([
            'exists' => false,
            'message' => 'Contact number is available'
        ]);
    }

    public function create()
    {
        $sbus = \App\Models\Sbu::with('sites')->get();
        return view('admin.admins.create', compact('sbus'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'contact_number' => ['required', 'string', 'max:11', 'regex:/^(\+63|09|9)\d{9,10}$/'],
                'sbu_ids' => 'required|array|min:1',
                'sbu_ids.*' => 'exists:sbus,id',
                'site_ids' => 'required|array|min:1',
                'site_ids.*' => 'exists:sites,id',
            ]);

            // Format contact number
            $contactNumber = $request->contact_number;
            if (str_starts_with($contactNumber, '+63')) {
                // Keep as is
            } elseif (str_starts_with($contactNumber, '09')) {
                $contactNumber = '+63' . substr($contactNumber, 1);
            } elseif (str_starts_with($contactNumber, '9')) {
                $contactNumber = '+63' . $contactNumber;
            }
            
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
            
            // Check if contact number exists in any format in admin_users table
            $formatsToCheck = [];
            if (str_starts_with($contactNumber, '+63')) {
                $formatsToCheck[] = $contactNumber; // +639123456789
                $formatsToCheck[] = '0' . substr($contactNumber, 3); // 09123456789
                $formatsToCheck[] = substr($contactNumber, 3); // 9123456789
            } elseif (str_starts_with($contactNumber, '09')) {
                $formatsToCheck[] = $contactNumber; // 09123456789
                $formatsToCheck[] = '+63' . substr($contactNumber, 1); // +639123456789
                $formatsToCheck[] = substr($contactNumber, 1); // 9123456789
            } elseif (str_starts_with($contactNumber, '9')) {
                $formatsToCheck[] = $contactNumber; // 9123456789
                $formatsToCheck[] = '+63' . $contactNumber; // +639123456789
                $formatsToCheck[] = '0' . $contactNumber; // 09123456789
            }
            
            if (Admin::whereIn('contact_number', $formatsToCheck)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This contact number is already registered as an admin user.');
            }
            
            // Check if contact number exists in any format in users table
            if (User::whereIn('contact_number', $formatsToCheck)->exists()) {
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'This contact number is already registered as a regular user.');
            }

        // Check if the admin creation details match a user account
        $user = User::where('name', $request->name)
                    ->where('email', $request->email)
                    ->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'For security reasons, you cannot create an admin with these credentials as they match an existing user.');
        }

        // Create the admin
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $contactNumber,
        ]);

        // Attach all selected SBUs to the admin
        $admin->sbus()->attach($request->sbu_ids);
        
        // Attach all selected sites to the admin
        $admin->sites()->attach($request->site_ids);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Admin account created successfully with access to ' . count($request->sbu_ids) . ' SBU(s) and ' . count($request->site_ids) . ' site(s)!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'An error occurred while creating the admin account: ' . $e->getMessage());
        }
    }
}