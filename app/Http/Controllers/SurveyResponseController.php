<?php

namespace App\Http\Controllers;

use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyResponseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|min:1|max:10',
            'comments' => 'required|string',
            'survey_id' => 'required|exists:surveys,id'
        ]);

        SurveyResponse::create($validated);

        return redirect()->route('surveys.thankyou')
            ->with('success', 'Thank you for your feedback!');
    }
}
