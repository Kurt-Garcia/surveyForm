<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\SurveyResponse;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['login', 'authenticate']);
    }

    public function dashboard()
    {
        $totalSurveys = Survey::count();
        $totalResponses = SurveyResponse::distinct('survey_id')->count('survey_id');
        $activeSurveys = Survey::where('created_at', '>=', now()->subDays(30))->count();

        return view('admin.dashboard', compact('totalSurveys', 'totalResponses', 'activeSurveys'));
    }

    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function createSurvey(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array',
                'questions.*.text' => 'required|string|max:255',
                'questions.*.type' => 'required|in:text,radio,checkbox,select'
            ]);

            $survey = Survey::create([
                'accountName' => $validated['title'],
                'accountType' => $validated['description'] ?? '',
                'date' => now(),
                'admin_id' => Auth::guard('admin')->id()
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Survey created successfully!');
        }

        return view('admin.create_survey');
    }
}