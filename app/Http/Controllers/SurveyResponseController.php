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

        // Get the survey with its questions
        $survey = Survey::with('questions')->findOrFail($request->survey_id);
        
        // Get valid question IDs from the survey
        $validQuestionIds = $survey->questions->pluck('id')->toArray();
        
        // Format and validate responses
        $formattedResponses = [];
        foreach ($validQuestionIds as $questionId) {
            if (!isset($request->responses[$questionId])) {
                return response()->json([
                    'error' => 'Missing response for question ID: ' . $questionId
                ], 422);
            }
            
            $question = $survey->questions->firstWhere('id', $questionId);
            $formattedResponses[] = [
                'question' => $question->text,
                'rating' => (int)$request->responses[$questionId]
            ];
        }

        // Create the response with formatted data
        $response = SurveyResponse::create([
            'survey_id' => $validated['survey_id'],
            'account_name' => $validated['account_name'],
            'account_type' => $validated['account_type'],
            'date' => $validated['date'],
            'responses' => $formattedResponses,
            'recommendation' => $validated['recommendation'],
            'comments' => $validated['comments']
        ]);

        return response()->json(['message' => 'Survey response submitted successfully']);
    }
}
