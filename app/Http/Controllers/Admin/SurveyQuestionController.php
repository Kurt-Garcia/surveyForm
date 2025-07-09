<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class SurveyQuestionController extends Controller
{
    public function create(Survey $survey)
    {
        return view('admin.surveys.questions.create', compact('survey'));
    }

    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'text_tagalog' => 'nullable|string|max:255',
            'text_cebuano' => 'nullable|string|max:255',
            'type' => 'required|string|in:radio,star',
            'required' => 'boolean'
        ]);

        $survey->questions()->create([
            'text' => ucfirst($validated['text']),
            'text_tagalog' => $validated['text_tagalog'] ? ucfirst($validated['text_tagalog']) : null,
            'text_cebuano' => $validated['text_cebuano'] ? ucfirst($validated['text_cebuano']) : null,
            'type' => $validated['type'],
            'required' => $validated['required'] ?? false,
            'order' => $survey->questions->count() + 1
        ]);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Survey $survey, SurveyQuestion $question)
    {
        return view('admin.surveys.questions.edit', compact('survey', 'question'));
    }

    public function update(Request $request, Survey $survey, SurveyQuestion $question)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'text_tagalog' => 'nullable|string|max:255',
            'text_cebuano' => 'nullable|string|max:255',
            'type' => 'required|string|in:radio,star',
            'required' => 'boolean'
        ]);

        $question->update([
            'text' => ucfirst($validated['text']),
            'text_tagalog' => $validated['text_tagalog'] ? ucfirst($validated['text_tagalog']) : null,
            'text_cebuano' => $validated['text_cebuano'] ? ucfirst($validated['text_cebuano']) : null,
            'type' => $validated['type'],
            'required' => $validated['required'] ?? false,
        ]);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Survey $survey, SurveyQuestion $question)
    {
        $question->delete();

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Question deleted successfully.');
    }
}