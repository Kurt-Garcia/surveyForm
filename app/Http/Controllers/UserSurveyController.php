<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::where('is_active', true)->with('questions')->paginate(9);
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
        $allowResubmit = false;
        
        if ($accountName = session('account_name')) {
            $response = SurveyResponseHeader::where('survey_id', $survey->id)
                ->where('account_name', $accountName)
                ->first();
            
            if ($response) {
                $hasResponded = true;
                $allowResubmit = $response->allow_resubmit;
            }
        }

        $questions = $survey->questions;
        return view('surveys.show', compact('survey', 'questions', 'hasResponded', 'allowResubmit'));
    }

    public function store(Request $request, Survey $survey)
    {
        // Check if user has already responded
        $existingResponse = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $request->account_name)
            ->first();

        if ($existingResponse && !$existingResponse->allow_resubmit) {
            return response()->json([
                'error' => 'You have already submitted this survey.'
            ], 422);
        }

        $validated = $request->validate([
            'account_name' => 'nullable|string',
            'account_type' => 'nullable|string',
            'date' => 'nullable|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'comments' => 'nullable|string', // Changed from required to nullable
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        // Get required questions and validate them
        $requiredQuestions = $survey->questions()->where('required', true)->get();
        foreach ($requiredQuestions as $question) {
            if (!isset($request->responses[$question->id]) || empty($request->responses[$question->id])) {
                return response()->json([
                    'error' => "Question '{$question->text}' is required."
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // If resubmission is allowed, delete previous response
            if ($existingResponse) {
                $existingResponse->details()->delete();
                $existingResponse->delete();
            }

            // Create header record
            $header = SurveyResponseHeader::create([
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments'],
                'allow_resubmit' => false
            ]);

            // Create detail records
            foreach ($request->responses as $questionId => $response) {
                SurveyResponseDetail::create([
                    'header_id' => $header->id,
                    'question_id' => $questionId,
                    'response' => $response
                ]);
            }

            DB::commit();

            // Store account name in session for future checks
            $request->session()->put('account_name', $validated['account_name']);

            return response()->json([
                'success' => true,
                'message' => 'Survey response submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit survey response. Please try again.'
            ], 500);
        }
    }

    public function thankyou()
    {
        return view('surveys.thankyou');
    }
}