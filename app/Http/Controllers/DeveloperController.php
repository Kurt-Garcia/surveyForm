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
        $sbuName = '';
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $surveys = Survey::with(['questions', 'responses', 'sbus'])
                    ->whereHas('sbus', function($query) use ($sbuModel) {
                        $query->where('sbu_id', $sbuModel->id);
                    })
                    ->paginate(15);
                $sbuName = $sbu;
            } else {
                $surveys = Survey::with(['questions', 'responses', 'sbus'])->paginate(15);
            }
        } else {
            // Show all surveys
            $surveys = Survey::with(['questions', 'responses', 'sbus'])->paginate(15);
        }
        
        return view('developer.surveys.index', compact('surveys', 'sbuName'));
    }

    /**
     * Show all admins with management options
     */
    public function admins(Request $request)
    {
        $sbu = $request->get('sbu');
        $sbuName = '';
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $admins = Admin::with(['sbus'])->whereHas('sbus', function($query) use ($sbuModel) {
                    $query->where('sbu_id', $sbuModel->id);
                })->paginate(15);
                $sbuName = $sbu;
            } else {
                $admins = Admin::with(['sbus'])->paginate(15);
            }
        } else {
            // Show all admins
            $admins = Admin::with(['sbus'])->paginate(15);
        }
        
        return view('developer.admins.index', compact('admins', 'sbuName'));
    }

    /**
     * Show all users with management options
     */
    public function users(Request $request)
    {
        $sbu = $request->get('sbu');
        $sbuName = '';
        
        if ($sbu && in_array($sbu, ['FDC', 'FUI'])) {
            // Filter by SBU
            $sbuModel = Sbu::where('name', $sbu)->first();
            if ($sbuModel) {
                $users = User::with(['sbus'])->whereHas('sbus', function($query) use ($sbuModel) {
                    $query->where('sbu_id', $sbuModel->id);
                })->paginate(15);
                $sbuName = $sbu;
            } else {
                $users = User::with(['sbus'])->paginate(15);
            }
        } else {
            // Show all users
            $users = User::with(['sbus'])->paginate(15);
        }
        
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
        } else {
            // If enabling the admin, clear the disabled reason
            $admin->status = 1;
            $admin->disabled_reason = null;
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
        } else {
            // If enabling the user, clear the disabled reason
            $user->status = 1;
            $user->disabled_reason = null;
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
     * Delete survey
     */
    public function deleteSurvey($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return back()->with('success', 'Survey deleted successfully.');
    }
}
