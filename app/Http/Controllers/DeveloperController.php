<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\Admin;
use App\Models\User;
use App\Models\Sbu;
use App\Models\SurveyResponseHeader;

class DeveloperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:developer')->except(['showLoginForm', 'login']);
        $this->middleware('developer.access')->except(['showLoginForm', 'login']);
    }

    /**
     * Show the developer login form
     */
    public function showLoginForm()
    {
        return view('developer.login');
    }

    /**
     * Handle developer login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'], // Can be username or email
            'password' => ['required'],
        ]);

        // Try to login with username first, then email
        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $loginCredentials = [
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
            'status' => true, // Only active developers can login
        ];

        if (Auth::guard('developer')->attempt($loginCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('developer.dashboard'));
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Handle developer logout
     */
    public function logout(Request $request)
    {
        Auth::guard('developer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('developer.login');
    }

    /**
     * Show the developer dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_surveys' => Survey::count(),
            'total_admins' => Admin::count(),
            'total_users' => User::count(),
            'total_responses' => SurveyResponseHeader::count(),
            'active_surveys' => Survey::where('is_active', true)->count(),
            'active_admins' => Admin::where('status', 1)->count(),
            'active_users' => User::where('status', 1)->count(),
        ];

        return view('developer.dashboard', compact('stats'));
    }

    /**
     * Show all surveys with management options
     */
    public function surveys(Request $request)
    {
        $sbu = $request->get('sbu');
        $search = $request->get('search');
        $sbuName = '';
        
        $query = Survey::with(['questions', 'responses', 'sbus', 'admin']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('admin', function($adminQuery) use ($search) {
                      $adminQuery->where('name', 'LIKE', "%{$search}%")
                                 ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $query->whereHas('sbus', function($q) use ($sbuModel) {
                    $q->where('sbu_id', $sbuModel->id);
                });
                $sbuName = $sbu;
            }
        }
        
        $surveys = $query->paginate(6)->appends($request->query());
        
        return view('developer.surveys.index', compact('surveys', 'sbuName'));
    }

    /**
     * Show all admins with management options
     */
    public function admins(Request $request)
    {
        $sbu = $request->get('sbu');
        $search = $request->get('search');
        $sbuName = '';
        
        $query = Admin::with(['sbus']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%");
            });
        }
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $query->whereHas('sbus', function($q) use ($sbuModel) {
                    $q->where('sbu_id', $sbuModel->id);
                });
                $sbuName = $sbu;
            }
        }
        
        $admins = $query->paginate(6)->appends($request->query());
        
        return view('developer.admins.index', compact('admins', 'sbuName'));
    }

    /**
     * Show all users with management options
     */
    public function users(Request $request)
    {
        $sbu = $request->get('sbu');
        $search = $request->get('search');
        $sbuName = '';
        
        $query = User::with(['sbus']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%");
            });
        }
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $query->whereHas('sbus', function($q) use ($sbuModel) {
                    $q->where('sbu_id', $sbuModel->id);
                });
                $sbuName = $sbu;
            }
        }
        
        $users = $query->paginate(6)->appends($request->query());
        
        return view('developer.users.index', compact('users', 'sbuName'));
    }

    /**
     * Toggle admin status
     */
    public function toggleAdminStatus(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        // If disabling the admin, require a reason
        if ($admin->status == 1) {
            $request->validate([
                'disabled_reason' => 'required|string|min:10|max:500'
            ], [
                'disabled_reason.required' => 'Please provide a reason for disabling this account.',
                'disabled_reason.min' => 'The reason must be at least 10 characters long.',
                'disabled_reason.max' => 'The reason cannot exceed 500 characters.'
            ]);
            
            $admin->status = 0;
            $admin->disabled_reason = $request->disabled_reason;
            $admin->disabled_at = now();
        } else {
            // If enabling the admin, clear the disabled reason and timestamp
            $admin->status = 1;
            $admin->disabled_reason = null;
            $admin->disabled_at = null;
        }
        
        $admin->save();

        return back()->with('success', 'Admin status updated successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // If disabling the user, require a reason
        if ($user->status == 1) {
            $request->validate([
                'disabled_reason' => 'required|string|min:10|max:500'
            ], [
                'disabled_reason.required' => 'Please provide a reason for disabling this account.',
                'disabled_reason.min' => 'The reason must be at least 10 characters long.',
                'disabled_reason.max' => 'The reason cannot exceed 500 characters.'
            ]);
            
            $user->status = 0;
            $user->disabled_reason = $request->disabled_reason;
            $user->disabled_at = now();
        } else {
            // If enabling the user, clear the disabled reason and timestamp
            $user->status = 1;
            $user->disabled_reason = null;
            $user->disabled_at = null;
        }
        
        $user->save();

        return back()->with('success', 'User status updated successfully.');
    }

    /**
     * Delete admin
     */
    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return back()->with('success', 'Admin deleted successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * Enable survey
     */
    public function enableSurvey($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->update(['is_active' => true]);

        return back()->with('success', 'Survey has been enabled successfully.');
    }

    /**
     * Disable survey
     */
    public function disableSurvey($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->update(['is_active' => false]);

        return back()->with('success', 'Survey has been disabled successfully.');
    }

    /**
     * Delete survey
     */
    public function deleteSurvey($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return back()->with('success', 'Survey deleted successfully.');
    }
}
