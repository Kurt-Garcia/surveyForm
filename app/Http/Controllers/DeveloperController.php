<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\Admin;
use App\Models\User;
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
    public function surveys()
    {
        $surveys = Survey::with(['questions', 'responses'])->paginate(15);
        return view('developer.surveys.index', compact('surveys'));
    }

    /**
     * Show all admins with management options
     */
    public function admins()
    {
        $admins = Admin::paginate(15);
        return view('developer.admins.index', compact('admins'));
    }

    /**
     * Show all users with management options
     */
    public function users()
    {
        $users = User::paginate(15);
        return view('developer.users.index', compact('users'));
    }

    /**
     * Toggle admin status
     */
    public function toggleAdminStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->status = !$admin->status;
        $admin->save();

        return back()->with('success', 'Admin status updated successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
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
