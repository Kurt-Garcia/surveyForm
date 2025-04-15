<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::where('is_active', true)->with('questions')->get();
        return view('index', compact('surveys'));
    }

    public function show(Survey $survey, Request $request)
    {
        // Check if survey is active
        if (!$survey->is_active) {
            return redirect()->route('index')
                ->with('warning', 'This survey is currently not active.');
        }

        $hasResponded = false;
        if ($accountName = session('account_name')) {
            $hasResponded = SurveyResponse::where('survey_id', $survey->id)
                ->where('account_name', $accountName)
                ->exists();
        }

        $questions = $survey->questions;
        return view('surveys.show', compact('survey', 'questions', 'hasResponded'));
    }

    public function store(Request $request, Survey $survey)
    {
        // Check if user has already responded
        if (SurveyResponse::where('survey_id', $survey->id)
            ->where('account_name', $request->account_name)
            ->exists()) {
            return response()->json([
                'error' => 'You have already submitted this survey.'
            ], 422);
        }

        $validated = $request->validate([
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'comments' => 'required|string'
        ]);

        foreach ($request->responses as $questionId => $response) {
            SurveyResponse::create([
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'question_id' => $questionId,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'response' => $response,
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments']
            ]);
        }

        // Store account name in session for future checks
        $request->session()->put('account_name', $validated['account_name']);

        return response()->json([
            'success' => true,
            'message' => 'Survey response submitted successfully'
        ]);
    }

    public function thankyou()
    {
        return view('surveys.thankyou');
    }
}