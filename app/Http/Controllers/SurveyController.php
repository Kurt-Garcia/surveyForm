<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::with('questions')->get();
        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $survey = Survey::create([
            'title' => $validated['title'],
            'admin_id' => auth()->guard('admin')->id()
        ]);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey created successfully.');
    }

    public function show(Survey $survey)
    {
        $survey->load('questions');
        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $survey->update($validated);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey updated successfully.');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully.');
    }
}