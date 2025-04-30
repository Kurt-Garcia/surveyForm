<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SurveyResponseController extends Controller
{
    public function toggleResubmission(Survey $survey, $account_name)
    {
        // Ensure the user has access to toggle resubmission
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $response = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $account_name)
            ->firstOrFail();

        $response->allow_resubmit = !$response->allow_resubmit;
        $response->save();

        return response()->json([
            'success' => true,
            'message' => 'Resubmission status updated successfully',
            'allow_resubmission' => $response->allow_resubmit
        ]);
    }

    public function index(Survey $survey)
    {
        // Ensure the user has access to view responses
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $responses = SurveyResponseHeader::where('survey_id', $survey->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('surveys.responses.responses', [
            'survey' => $survey,
            'responses' => $responses
        ]);
    }

    public function show(Survey $survey, $account_name)
    {
        // Ensure the user has access to view the response
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $response = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $account_name)
            ->with('details.question')
            ->firstOrFail();

        return view('surveys.responses.responses-detail', [
            'survey' => $survey,
            'response' => $response
        ]);
    }

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
        if (SurveyResponseHeader::hasResponded($validated['survey_id'], $validated['account_name'])) {
            return response()->json([
                'error' => 'You have already submitted this survey.'
            ], 422);
        }

        // Get the survey with its questions
        $survey = Survey::with('questions')->findOrFail($request->survey_id);
        
        // Get valid question IDs from the survey
        $validQuestionIds = $survey->questions->pluck('id')->toArray();
        
        // Validate all questions are answered
        foreach ($validQuestionIds as $questionId) {
            if (!isset($request->responses[$questionId])) {
                return response()->json([
                    'error' => 'Missing response for question ID: ' . $questionId
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Create header record
            $header = SurveyResponseHeader::create([
                'survey_id' => $validated['survey_id'],
                'admin_id' => $survey->admin_id,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments']
            ]);

            // Create detail records for each response
            foreach ($validQuestionIds as $questionId) {
                SurveyResponseDetail::create([
                    'header_id' => $header->id,
                    'question_id' => $questionId,
                    'response' => $request->responses[$questionId]
                ]);
            }

            DB::commit();

            // Store account name in session for future checks
            $request->session()->put('account_name', $validated['account_name']);

            return response()->json([
                'success' => true,
                'message' => 'Survey response submitted successfully',
                'redirect' => route('index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit survey response. Please try again.'
            ], 500);
        }
    }
}
