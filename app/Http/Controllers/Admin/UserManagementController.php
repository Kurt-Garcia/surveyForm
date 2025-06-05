<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Sbu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // Show the form to create a new user
    public function create()
    {
        $sbus = \App\Models\Sbu::with('sites')->get();
        return view('admin.users.create', compact('sbus'));
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
                'max:11',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+63|09|9)\d{9,10}$/', $value)) {
                        $fail('The contact number must be 10-11 digits and start with +63, 09, or 9.');
                    }
                },
            ],
            'sbu_ids' => 'required|array|min:1',
            'sbu_ids.*' => 'exists:sbus,id',
            'site_ids' => 'required|array|min:1',
            'site_ids.*' => 'exists:sites,id',
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

        // Check if contact number already exists in any format in either users or admin_users table
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

        $existingUserContact = User::whereIn('contact_number', $formatsToCheck)->exists();
        $existingAdminContact = Admin::whereIn('contact_number', $formatsToCheck)->exists();

        if ($existingUserContact || $existingAdminContact) {
            return redirect()->back()->withErrors([
                'contact_number' => 'The contact number has already been taken.'
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $contactNumber,
            'created_by' => auth('admin')->id(), // Set the current admin as creator
        ]);

        // Attach all selected SBUs to the user
        $user->sbus()->attach($request->sbu_ids);
        
        // Attach all selected sites to the user
        $user->sites()->attach($request->site_ids);

        return redirect()->route('admin.users.create')
            ->with('success', 'User created successfully with access to ' . count($request->sbu_ids) . ' SBU(s) and ' . count($request->site_ids) . ' site(s)!');
    }

    // Get sites by selected SBUs (for AJAX requests)
    public function getSitesBySbus(Request $request)
    {
        $sbuIds = $request->query('sbu_ids');
        
        if (!$sbuIds) {
            return response()->json([]);
        }
        
        $sbuIdsArray = explode(',', $sbuIds);
        
        $sites = \App\Models\Site::with('sbu')
            ->whereIn('sbu_id', $sbuIdsArray)
            ->orderBy('name')
            ->get();
        
        return response()->json($sites);
    }

    // Get data for DataTables displaying only survey users created by current admin
    public function data()
    {
        // Get only regular users (surveyors) created by the current admin
        $surveyUsers = User::with(['sbus', 'sites', 'createdBy'])
            ->where('created_by', auth('admin')->id()) // Filter by current admin
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'sbu_name' => $user->sbus->pluck('name')->join(', ') ?: 'N/A',
                    'site_name' => $user->sites->pluck('name')->join(', ') ?: 'N/A',
                    'user_type' => 'Surveyor',
                    'created_by' => $user->createdBy ? $user->createdBy->name : 'Unknown',
                    'created_at' => $user->created_at->format('M d, Y')
                ];
            });

        return response()->json([
            'data' => $surveyUsers
        ]);
    }
}