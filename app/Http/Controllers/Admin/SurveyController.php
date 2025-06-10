<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Sbu;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    public function create()
    {
        $sbus = Sbu::with('sites')->get();
        return view('admin.create_survey', compact('sbus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:surveys,title',
            'sbu_ids' => 'required|array|min:1',
            'sbu_ids.*' => 'exists:sbus,id',
            'site_ids' => 'required|array|min:1',
            'site_ids.*' => 'exists:sites,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|string|in:text,radio,star,select',
            'questions.*.required' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('survey-logos', 'public');
            }

            $survey = Survey::create([
                'title' => ucfirst($request->title),
                'admin_id' => Auth::guard('admin')->id(),
                'is_active' => true,
                'logo' => $logoPath
            ]);
            
            // Attach SBUs to the survey
            $survey->sbus()->attach($request->sbu_ids);
            
            // Attach sites to the survey
            $survey->sites()->attach($request->site_ids);

            foreach ($request->questions as $index => $questionData) {
                $survey->questions()->create([
                    'text' => ucfirst($questionData['text']),
                    'type' => $questionData['type'],
                    'required' => isset($questionData['required']) ? (bool)$questionData['required'] : false,
                    'order' => $index + 1
                ]);
            }

            DB::commit();
            return redirect()->route('admin.surveys.show', $survey)
                ->with('success', 'Survey created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create survey. Please try again.');
        }
    }

    public function index()
    {
        $query = Survey::with('questions')
            ->where('admin_id', Auth::guard('admin')->id());

        $search = request('search');
        $date = request('date');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereDate('created_at', $search);
            });
        }
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $surveys = $query->latest()->paginate(6);
        $totalSurveys = $query->count();
        return view('admin.surveys.index', compact('surveys', 'totalSurveys'));
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

    public function updateLogo(Request $request, Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('remove_logo')) {
            // Remove existing logo if it exists
            if ($survey->logo) {
                Storage::disk('public')->delete($survey->logo);
                $survey->update(['logo' => null]);
            }
            return redirect()->back()->with('success', 'Logo removed successfully!');
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Remove old logo if exists
            if ($survey->logo) {
                Storage::disk('public')->delete($survey->logo);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('survey-logos', 'public');
            $survey->update(['logo' => $logoPath]);

            return redirect()->back()->with('success', 'Logo updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update logo. Please try again.');
        }
    }

    public function update(Request $request, Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => "required|string|max:255|unique:surveys,title,{$survey->id}",
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:255',
            'questions.*.type' => 'required|string|in:text,radio,star,select',
            'questions.*.required' => 'boolean'
        ]);

        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Update survey title
            $survey->update([
                'title' => ucfirst($request->title)
            ]);

            // Delete existing questions
            $survey->questions()->delete();

            // Create new questions
            foreach ($request->questions as $index => $questionData) {
                $survey->questions()->create([
                    'text' => ucfirst($questionData['text']),
                    'type' => $questionData['type'],
                    'required' => $questionData['required'] ?? false,
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

    public function toggleStatus(Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $survey->update([
            'is_active' => !$survey->is_active
        ]);

        $status = $survey->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', "Survey has been {$status} successfully!");
    }
    
    /**
     * Get all SBUs with their associated sites for the dropdown
     */
    public function getSbusWithSites()
    {
        $sbus = Sbu::with('sites')->get();
        return response()->json($sbus);
    }
    
    /**
     * Update the survey's SBU and deployment sites
     */
    public function updateDeployment(Request $request, Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'sbu_ids' => 'required|array|min:1',
            'sbu_ids.*' => 'exists:sbus,id',
            'site_ids' => 'required|array|min:1',
            'site_ids.*' => 'exists:sites,id',
        ]);
        
        DB::beginTransaction();
        try {
            // Sync SBUs (detach old ones and attach new ones)
            $survey->sbus()->sync($request->sbu_ids);
            
            // Sync sites (detach old ones and attach new ones)
            $survey->sites()->sync($request->site_ids);
            
            DB::commit();
            return redirect()->route('admin.surveys.show', $survey)
                ->with('success', 'Deployment settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update deployment settings. Please try again.');
        }
    }

    /**
     * Check if a survey title is unique (for real-time validation)
     */
    public function checkTitleUniqueness(Request $request)
    {
        $title = $request->input('title');
        $surveyId = $request->input('survey_id'); // For edit mode (exclude current survey)
        
        if (empty($title)) {
            return response()->json([
                'available' => false,
                'message' => 'Title is required.'
            ]);
        }
        
        $query = Survey::where('title', $title);
        
        // If editing, exclude the current survey
        if ($surveyId) {
            $query->where('id', '!=', $surveyId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This title is already in use. Please choose a different title.' : 'Title is available.'
        ]);
    }
}