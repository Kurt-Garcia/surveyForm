<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyForm;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SurveyFormController extends Controller
{
    public function show(Survey $survey)
    {
        $questions = $survey->questions;
        return view('surveys.show', compact('survey', 'questions'));
    }

    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'accountName' => 'required|string|max:255',
            'accountType' => 'required|string|max:255',
            'date' => 'required|date',
            'responses.*' => 'required|integer|min:1|max:5',
            'recommendation' => 'required|integer|min:1|max:10',
            'comments' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Create survey form entry
            $surveyForm = SurveyForm::create([
                'survey_id' => $survey->id,
                'user_id' => Auth::id(),
                'accountName' => $validated['accountName'],
                'accountType' => $validated['accountType'],
                'submission_date' => $validated['date'],
                'status' => 'submitted',
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments']
            ]);

            // Store individual question responses
            foreach ($validated['responses'] as $questionId => $rating) {
                SurveyResponse::create([
                    'user_id' => Auth::id(),
                    'survey_id' => $survey->id,
                    'question_id' => $questionId,
                    'response' => $rating
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Thank you for your feedback!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while submitting your survey. Please try again.')
                ->withInput();
        }
    }
}