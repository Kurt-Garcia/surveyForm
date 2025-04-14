<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function create()
    {
        return view('admin.create_survey');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|string|in:text,radio,star,select'
        ]);

        $survey = Survey::create([
            'title' => $request->title,
            'admin_id' => Auth::guard('admin')->id()
        ]);

        foreach ($request->questions as $questionData) {
            $survey->questions()->create([
                'text' => $questionData['text'],
                'type' => $questionData['type']
            ]);
        }

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey created successfully!');
    }

    public function index()
    {
        $surveys = Survey::with('questions')
            ->where('admin_id', Auth::guard('admin')->id())
            ->latest()
            ->paginate(10);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|string|in:text,radio,star,select'
        ]);

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Update survey title
            $survey->update([
                'title' => $request->title
            ]);

            // Delete existing questions
            $survey->questions()->delete();

            // Create new questions
            foreach ($request->questions as $index => $questionData) {
                $survey->questions()->create([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'order' => $index + 1
                ]);
            }

            DB::commit();
            return redirect()->route('admin.surveys.show', $survey)
                ->with('success', 'Survey updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update survey. Please try again.');
        }
    }

    public function destroy(Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated questions first
        $survey->questions()->delete();
        
        // Then delete the survey
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully!');
    }
}