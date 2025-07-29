<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\SurveyResponseHeader;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except(['login', 'authenticate']);
    }

    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get surveys created by current admin only
        $totalSurveys = Survey::where('admin_id', $admin->id)->count();
        $activeSurveys = Survey::where('admin_id', $admin->id)->where('is_active', true)->count();
        $inactiveSurveys = Survey::where('admin_id', $admin->id)->where('is_active', false)->count();
        
        // Get responses for admin's surveys only
        $totalResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->count();
        
        // Get responses from today
        $todayResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->whereDate('created_at', today())->count();
        
        // Get responses from this week
        $weekResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        // Get responses from this month
        $monthResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->whereMonth('created_at', now()->month)->count();
        
        // Calculate average responses per survey
        $avgResponsesPerSurvey = $totalSurveys > 0 ? round($totalResponses / $totalSurveys, 1) : 0;
        
        // Get completion rate (assuming surveys with responses are "completed")
        $surveysWithResponses = Survey::where('admin_id', $admin->id)
            ->whereHas('responses')
            ->count();
        $completionRate = $totalSurveys > 0 ? round(($surveysWithResponses / $totalSurveys) * 100, 1) : 0;
        
        // Get latest survey
        $latestSurvey = Survey::where('admin_id', $admin->id)->latest()->first();
        
        // Get most active survey (survey with most responses)
        $mostActiveSurvey = Survey::where('admin_id', $admin->id)
            ->withCount('responses')
            ->orderByDesc('responses_count')
            ->first();
        
        // Get recent activity (last 5 responses)
        $recentResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->with('survey')->latest()->take(5)->get();
        
        // Calculate response trends (compare this month vs last month)
        $lastMonthResponses = SurveyResponseHeader::whereHas('survey', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->whereMonth('created_at', now()->subMonth()->month)
          ->whereYear('created_at', now()->subMonth()->year)
          ->count();
        
        $responseTrend = $lastMonthResponses > 0 
            ? round((($monthResponses - $lastMonthResponses) / $lastMonthResponses) * 100, 1)
            : ($monthResponses > 0 ? 100 : 0);

        return view('admin.dashboard', compact(
            'totalSurveys', 
            'totalResponses', 
            'activeSurveys', 
            'inactiveSurveys',
            'todayResponses',
            'weekResponses',
            'monthResponses',
            'avgResponsesPerSurvey',
            'completionRate',
            'latestSurvey',
            'mostActiveSurvey',
            'recentResponses',
            'responseTrend'
        ));
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