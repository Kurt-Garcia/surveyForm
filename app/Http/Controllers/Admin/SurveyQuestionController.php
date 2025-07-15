<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\TranslationHeader;
use Illuminate\Http\Request;

class SurveyQuestionController extends Controller
{
    public function create(Survey $survey)
    {
        $activeLanguages = TranslationHeader::active()->get();
        // Create an empty collection for translations to match the edit view's structure
        // Initialize with the same structure as in edit method
        $questionTranslations = collect();
        // Convert to the same format as getTranslationsWithLocales() returns
        $questionTranslations = $activeLanguages->mapWithKeys(function ($language) {
            return [$language->locale => ''];
        });
        return view('admin.surveys.questions.create', compact('survey', 'activeLanguages', 'questionTranslations'));
    }

    public function store(Request $request, Survey $survey)
    {
        // Build validation rules dynamically
        $rules = [
            'text' => 'required|string|max:255',
            'type' => 'required|string|in:radio,star',
            'required' => 'boolean'
        ];

        // Add validation rules for active languages
        $activeLanguages = TranslationHeader::active()->get();
        foreach ($activeLanguages as $language) {
            if ($language->locale !== 'en') {
                $rules["text_{$language->locale}"] = 'nullable|string|max:255';
            }
        }

        $validated = $request->validate($rules);

        $question = $survey->questions()->create([
            'text' => ucfirst($validated['text']),
            'type' => $validated['type'],
            'required' => $validated['required'] ?? false,
            'order' => $survey->questions->count() + 1
        ]);

        // Add translations for active languages
        foreach ($activeLanguages as $language) {
            if ($language->locale !== 'en') {
                $textKey = "text_{$language->locale}";
                if (!empty($validated[$textKey]) && trim($validated[$textKey]) !== '') {
                    $question->setTranslation($language->locale, ucfirst(trim($validated[$textKey])));
                }
            }
        }

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Question added successfully.');
    }

    public function edit(Survey $survey, SurveyQuestion $question)
    {
        $activeLanguages = TranslationHeader::active()->get();
        $questionTranslations = $question->getTranslationsWithLocales();
        return view('admin.surveys.questions.edit', compact('survey', 'question', 'activeLanguages', 'questionTranslations'));
    }

    public function update(Request $request, Survey $survey, SurveyQuestion $question)
    {
        // Build validation rules dynamically
        $rules = [
            'text' => 'required|string|max:255',
            'type' => 'required|string|in:radio,star',
            'required' => 'boolean'
        ];

        // Add validation rules for active languages
        $activeLanguages = TranslationHeader::active()->get();
        foreach ($activeLanguages as $language) {
            if ($language->locale !== 'en') {
                $rules["text_{$language->locale}"] = 'nullable|string|max:255';
            }
        }

        $validated = $request->validate($rules);

        $question->update([
            'text' => ucfirst($validated['text']),
            'type' => $validated['type'],
            'required' => $validated['required'] ?? false,
        ]);

        // Update translations for active languages
        foreach ($activeLanguages as $language) {
            if ($language->locale !== 'en') {
                $textKey = "text_{$language->locale}";
                if (!empty($validated[$textKey]) && trim($validated[$textKey]) !== '') {
                    $question->setTranslation($language->locale, ucfirst(trim($validated[$textKey])));
                } else {
                    // Remove translation if empty
                    $question->translations()
                        ->whereHas('translationHeader', function ($query) use ($language) {
                            $query->where('locale', $language->locale);
                        })
                        ->delete();
                }
            }
        }

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