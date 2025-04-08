<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyForm;

class FormController extends Controller
{
    public function store(Request $request)
{
    // Validate the form data
    $validatedData = $request->validate([
        'accountName' => 'required|string',
        'accountType' => 'required|string',
        'date' => 'required|date',
        'Q1' => 'required|integer',
        'Q2' => 'required|integer',
        'Q3' => 'required|integer',
        'Q4' => 'required|integer',
        'Q5' => 'required|integer',
        'Q6' => 'required|integer',
        'surveyRating' => 'required|integer',
        'comments' => 'required|string',
    ]);

    // Store the data in the database
    SurveyForm::create($validatedData);

    // Redirect back with a success message
    return redirect()->route('home')->with('success', 'Survey submitted successfully!');
}

}
