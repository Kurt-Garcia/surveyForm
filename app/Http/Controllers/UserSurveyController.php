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
        $surveys = Survey::all();
        return view('index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        $questions = $survey->questions;
        return view('surveys.show', compact('survey', 'questions'));
    }

    public function store(Request $request, Survey $survey)
    {
        $request->validate([
            'responses.*' => 'required'
        ]);

        foreach ($request->responses as $questionId => $response) {
            SurveyResponse::create([
                'user_id' => Auth::id(),
                'survey_id' => $survey->id,
                'question_id' => $questionId,
                'response' => $response
            ]);
        }

        return redirect()->route('index')->with('success', 'Survey completed successfully!');
    }
}