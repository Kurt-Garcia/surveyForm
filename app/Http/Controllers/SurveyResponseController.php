<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyResponse;
use App\Models\Survey;

class SurveyResponseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'comments' => 'required|string'
        ]);

        // Check if user has already responded
        if (SurveyResponse::hasResponded($validated['survey_id'], $validated['account_name'])) {
            return response()->json([
                'error' => 'You have already submitted this survey.'
            ], 422);
        }

        // Get the survey with its questions
        $survey = Survey::with('questions')->findOrFail($request->survey_id);
        
        // Get valid question IDs from the survey
        $validQuestionIds = $survey->questions->pluck('id')->toArray();
        
        // Store individual responses for each question
        foreach ($validQuestionIds as $questionId) {
            if (!isset($request->responses[$questionId])) {
                return response()->json([
                    'error' => 'Missing response for question ID: ' . $questionId
                ], 422);
            }
            
            SurveyResponse::create([
                'survey_id' => $validated['survey_id'],
                'admin_id' => $survey->admin_id,
                'question_id' => $questionId,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'response' => $request->responses[$questionId],
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments']
            ]);
        }

        // Store account name in session for future checks
        $request->session()->put('account_name', $validated['account_name']);

        return response()->json([
            'success' => true,
            'message' => 'Survey response submitted successfully',
            'redirect' => route('index')
        ]);
    }
}
