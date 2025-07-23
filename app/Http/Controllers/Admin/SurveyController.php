<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Survey;
use App\Models\Sbu;
use App\Models\Site;
use App\Models\TranslationHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\LogActivity;

class SurveyController extends Controller
{
    public function create()
    {
        // Get only the SBUs that the current admin has access to
        $admin = Auth::guard('admin')->user();
        
        // Get SBUs that this admin has access to via pivot table
        $sbus = Sbu::with('sites')
            ->whereHas('admins', function($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->get();
        
        // If no SBUs are assigned to the admin, return empty collection for security
        if ($sbus->isEmpty()) {
            $sbus = collect();
        }
        
        // Get active translation headers for language tabs
        $activeLanguages = TranslationHeader::active()->get();
        
        return view('admin.create_survey', compact('sbus', 'activeLanguages'));
    }

    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Get SBUs that this admin has access to
        $adminSbuIds = Sbu::whereHas('admins', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->pluck('id')->toArray();
        
        $request->validate([
            'title' => 'required|string|max:255|unique:surveys,title',
            'sbu_ids' => 'required|array|min:1',
            'sbu_ids.*' => 'exists:sbus,id',
            'site_ids' => 'required|array|min:1',
            'site_ids.*' => 'exists:sites,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|max:255',
            // Dynamic validation rules for active languages
            ...collect(TranslationHeader::active()->get())->mapWithKeys(function ($language) {
                if ($language->locale !== 'en') {
                    return ["questions.*.text_{$language->locale}" => 'nullable|string|max:255'];
                }
                return [];
            })->toArray(),
            'questions.*.type' => 'required|string|in:text,radio,star,select',
            'questions.*.required' => 'boolean'
        ]);
        
        // Additional validation: Check if admin has access to selected SBUs
        $invalidSbuIds = array_diff($request->sbu_ids, $adminSbuIds);
        if (!empty($invalidSbuIds)) {
            return back()->withErrors(['sbu_ids' => 'You do not have access to some of the selected SBUs.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            // Debug: Log the incoming question data
            Log::info('Survey creation - Questions data received:', $request->questions);
            
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('survey-logos', 'public');
            }
            
            $departmentLogoPath = null;
            if ($request->hasFile('department_logo')) {
                $departmentLogoPath = $request->file('department_logo')->store('survey-logos', 'public');
            }

            $survey = Survey::create([
                'title' => ucfirst($request->title),
                'admin_id' => Auth::guard('admin')->id(),
                'is_active' => true,
                'logo' => $logoPath,
                'department_logo' => $departmentLogoPath
            ]);
            
            // Activity logging is handled automatically by the Survey model's LogsActivity trait
            
            // Attach SBUs to the survey
            $survey->sbus()->attach($request->sbu_ids);
            
            // Attach sites to the survey
            $survey->sites()->attach($request->site_ids);

            foreach ($request->questions as $index => $questionData) {
                $questionCreateData = [
                    'text' => ucfirst(trim($questionData['text'])),
                    'type' => $questionData['type'],
                    'required' => isset($questionData['required']) ? (bool)$questionData['required'] : false,
                    'order' => $index + 1
                ];

                $question = $survey->questions()->create($questionCreateData);

                // Add translations for active languages dynamically
                $activeLanguages = TranslationHeader::active()->get();
                foreach ($activeLanguages as $language) {
                    if ($language->locale !== 'en') {
                        $textKey = "text_{$language->locale}";
                        if (!empty($questionData[$textKey]) && trim($questionData[$textKey]) !== '') {
                            $question->setTranslation($language->locale, ucfirst(trim($questionData[$textKey])));
                        }
                    }
                }
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
        $admin = Auth::guard('admin')->user();
        
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get SBUs that this admin has access to
        $sbus = Sbu::with('sites')
            ->whereHas('admins', function($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->get();

        return view('admin.surveys.show', compact('survey', 'sbus'));
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
                $oldLogoPath = $survey->logo;
                Storage::disk('public')->delete($survey->logo);
                $survey->update(['logo' => null]);
                
                // Log the logo removal activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_logo' => $oldLogoPath,
                        'new_logo' => null,
                        'action_type' => 'logo_removal'
                    ])
                    ->event('removed')
                    ->log("{$survey->title} Survey logo has been removed");
            }
            return redirect()->back()->with('success', 'Logo removed successfully!');
        }
        
        if ($request->has('remove_department_logo')) {
            // Remove existing department logo if it exists
            if ($survey->department_logo) {
                $oldDepartmentLogoPath = $survey->department_logo;
                Storage::disk('public')->delete($survey->department_logo);
                $survey->update(['department_logo' => null]);
                
                // Log the department logo removal activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_department_logo' => $oldDepartmentLogoPath,
                        'new_department_logo' => null,
                        'action_type' => 'department_logo_removal'
                    ])
                    ->event('removed')
                    ->log("{$survey->title} Department logo has been removed");
            }
            return redirect()->back()->with('success', 'Department logo removed successfully!');
        }

        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Update main logo if provided
            if ($request->hasFile('logo')) {
                $oldLogoPath = $survey->logo;
                
                // Remove old logo if exists
                if ($survey->logo) {
                    Storage::disk('public')->delete($survey->logo);
                }

                // Store new logo
                $logoPath = $request->file('logo')->store('survey-logos', 'public');
                $survey->update(['logo' => $logoPath]);
                
                // Log the logo update activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_logo' => $oldLogoPath,
                        'new_logo' => $logoPath,
                        'action_type' => 'logo_update'
                    ])
                    ->event('updated')
                    ->log("{$survey->title} Survey logo has been updated");
            }
            
            // Update department logo if provided
            if ($request->hasFile('department_logo')) {
                $oldDepartmentLogoPath = $survey->department_logo;
                
                // Remove old department logo if exists
                if ($survey->department_logo) {
                    Storage::disk('public')->delete($survey->department_logo);
                }

                // Store new department logo
                $departmentLogoPath = $request->file('department_logo')->store('survey-logos', 'public');
                $survey->update(['department_logo' => $departmentLogoPath]);
                
                // Log the department logo update activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_department_logo' => $oldDepartmentLogoPath,
                        'new_department_logo' => $departmentLogoPath,
                        'action_type' => 'department_logo_update'
                    ])
                    ->event('updated')
                    ->log("{$survey->title} Department logo has been updated");
            }

            return redirect()->back()->with('success', 'Logos updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update logos. Please try again.');
        }
    }

    public function updateDepartmentLogo(Request $request, Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('remove_department_logo')) {
            // Remove existing department logo if it exists
            if ($survey->department_logo) {
                $oldDepartmentLogoPath = $survey->department_logo;
                Storage::disk('public')->delete($survey->department_logo);
                $survey->update(['department_logo' => null]);
                
                // Log the department logo removal activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_department_logo' => $oldDepartmentLogoPath,
                        'new_department_logo' => null,
                        'action_type' => 'department_logo_removal'
                    ])
                    ->event('removed')
                    ->log("{$survey->title} Department logo has been removed");
            }
            return redirect()->back()->with('success', 'Department logo removed successfully!');
        }

        $request->validate([
            'department_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Update department logo if provided
            if ($request->hasFile('department_logo')) {
                $oldDepartmentLogoPath = $survey->department_logo;
                
                // Remove old department logo if exists
                if ($survey->department_logo) {
                    Storage::disk('public')->delete($survey->department_logo);
                }

                // Store new department logo
                $departmentLogoPath = $request->file('department_logo')->store('survey-logos', 'public');
                $survey->update(['department_logo' => $departmentLogoPath]);
                
                // Log the department logo update activity
                activity()
                    ->performedOn($survey)
                    ->causedBy(Auth::guard('admin')->user())
                    ->withProperties([
                        'old_department_logo' => $oldDepartmentLogoPath,
                        'new_department_logo' => $departmentLogoPath,
                        'action_type' => 'department_logo_update'
                    ])
                    ->event('updated')
                    ->log("{$survey->title} Department logo has been updated");
                
                return redirect()->back()->with('success', 'Department logo updated successfully!');
            }

            return redirect()->back()->with('error', 'Please select a department logo file to upload.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update department logo. Please try again.');
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
            // Store original data for logging
            $originalTitle = $survey->title;
            $originalQuestionCount = $survey->questions()->count();
            
            // Update survey title
            $survey->update([
                'title' => ucfirst($request->title)
            ]);

            // Delete existing questions
            $survey->questions()->delete();

            // Create new questions
            foreach ($request->questions as $index => $questionData) {
                $question = $survey->questions()->create([
                    'text' => ucfirst($questionData['text']),
                    'type' => $questionData['type'],
                    'required' => $questionData['required'] ?? false,
                    'order' => $index + 1
                ]);

                // Add translations for active languages dynamically
                $activeLanguages = TranslationHeader::active()->get();
                foreach ($activeLanguages as $language) {
                    if ($language->locale !== 'en') {
                        $textKey = "text_{$language->locale}";
                        if (!empty($questionData[$textKey]) && trim($questionData[$textKey]) !== '') {
                            $question->setTranslation($language->locale, ucfirst(trim($questionData[$textKey])));
                        }
                    }
                }
            }

            // Activity logging is handled automatically by the Survey model's LogsActivity trait

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

        // Store survey data for logging before deletion
        $surveyTitle = $survey->title;
        $questionCount = $survey->questions()->count();
        
        // Manual activity logging with custom description including survey title
        activity()
            ->performedOn($survey)
            ->causedBy(Auth::guard('admin')->user())
            ->withProperties([
                'title' => $surveyTitle,
                'question_count' => $questionCount,
                'action_type' => 'survey_delete'
            ])
            ->event('deleted')
            ->log("{$surveyTitle} has been deleted");

        // Delete associated questions first
        $survey->questions()->delete();
        
        // Then delete the survey
        $survey->deleteQuietly(); // Use deleteQuietly to prevent automatic logging

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully!');
    }

    public function toggleStatus(Survey $survey)
    {
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== Auth::guard('admin')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $originalStatus = $survey->is_active;
        $newStatus = !$originalStatus;
        
        // Use saveQuietly to prevent automatic activity logging by the LogsActivity trait
        $survey->is_active = $newStatus;
        $survey->saveQuietly();

        // Manual activity logging to use custom event names
        $eventName = $newStatus ? 'activated' : 'deactivated';
        $logDescription = $newStatus ? "{$survey->title} has been activated" : "{$survey->title} is deactivated";
        
        activity()
            ->performedOn($survey)
            ->causedBy(Auth::guard('admin')->user())
            ->withProperties([
                'old' => ['is_active' => $originalStatus],
                'attributes' => ['is_active' => $newStatus],
                'action_type' => 'survey_status_change'
            ])
            ->event($eventName)
            ->log($logDescription);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', "Survey has been {$eventName} successfully!");
    }
    
    /**
     * Get all SBUs with their associated sites for the dropdown
     */
    public function getSbusWithSites()
    {
        $admin = Auth::guard('admin')->user();
        
        // Get SBUs that this admin has access to
        $sbus = Sbu::with('sites')
            ->whereHas('admins', function($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->get();
            
        return response()->json($sbus);
    }
    
    /**
     * Update the survey's SBU and deployment sites
     */
    public function updateDeployment(Request $request, Survey $survey)
    {
        $admin = Auth::guard('admin')->user();
        
        // Ensure the authenticated admin owns this survey
        if ($survey->admin_id !== $admin->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get SBUs that this admin has access to
        $adminSbuIds = Sbu::whereHas('admins', function($query) use ($admin) {
            $query->where('admin_id', $admin->id);
        })->pluck('id')->toArray();
        
        $request->validate([
            'sbu_ids' => 'required|array|min:1',
            'sbu_ids.*' => 'exists:sbus,id',
            'site_ids' => 'required|array|min:1',
            'site_ids.*' => 'exists:sites,id',
        ]);
        
        // Additional validation: Check if admin has access to selected SBUs
        $invalidSbuIds = array_diff($request->sbu_ids, $adminSbuIds);
        if (!empty($invalidSbuIds)) {
            return back()->withErrors(['sbu_ids' => 'You do not have access to some of the selected SBUs.'])
                        ->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Get current SBUs and Sites for logging
            $oldSbuIds = $survey->sbus()->pluck('sbus.id')->toArray();
            $oldSiteIds = $survey->sites()->pluck('sites.id')->toArray();
            $newSbuIds = $request->sbu_ids;
            $newSiteIds = $request->site_ids;
            
            // Sync SBUs (detach old ones and attach new ones)
            $survey->sbus()->sync($request->sbu_ids);
            
            // Sync sites (detach old ones and attach new ones)
            $survey->sites()->sync($request->site_ids);
            
            // Log the deployment settings update activity
            activity()
                ->performedOn($survey)
                ->causedBy(Auth::guard('admin')->user())
                ->withProperties([
                    'old_sbu_ids' => $oldSbuIds,
                    'new_sbu_ids' => $newSbuIds,
                    'old_site_ids' => $oldSiteIds,
                    'new_site_ids' => $newSiteIds,
                    'action_type' => 'deployment_settings_update'
                ])
                ->event('updated')
                ->log("{$survey->title} deployment settings has been updated");
            
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