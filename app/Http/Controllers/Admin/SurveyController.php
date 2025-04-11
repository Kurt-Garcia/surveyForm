<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}