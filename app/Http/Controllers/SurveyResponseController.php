<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use App\Models\SurveyImprovementArea;
use App\Models\SurveyImprovementCategory;
use App\Models\SurveyImprovementDetail;
use App\Services\SurveyImprovementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SurveyResponseController extends Controller
{
    public function toggleResubmission(Survey $survey, $account_name)
    {
        // Ensure the user has access to toggle resubmission
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $response = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $account_name)
            ->firstOrFail();

        $response->allow_resubmit = !$response->allow_resubmit;
        $response->save();

        return response()->json([
            'success' => true,
            'message' => 'Resubmission status updated successfully',
            'allow_resubmission' => $response->allow_resubmit
        ]);
    }

    public function index(Survey $survey, Request $request)
    {
        // Ensure the user has access to view responses
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->input('search');
        
        $query = SurveyResponseHeader::where('survey_id', $survey->id);
        
        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('account_name', 'like', '%' . $search . '%')
                  ->orWhere('account_type', 'like', '%' . $search . '%');
            });
        }
        
        $responses = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // This preserves the search parameter in pagination links

        // Get the active theme for the survey's admin
        $activeTheme = \App\Models\ThemeSetting::getActiveTheme($survey->admin_id);

        return view('surveys.responses.responses', [
            'survey' => $survey,
            'responses' => $responses,
            'activeTheme' => $activeTheme
        ]);
    }

    public function show(Survey $survey, $account_name)
    {
        // Ensure the user has access to view the response
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $response = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $account_name)
            ->with(['details.question', 'improvementCategories.details'])
            ->firstOrFail();

        // Get the responses for compatibility with admin view
        $header = $response;
        $responses = $response->details;

        // Get the active theme for the survey's admin
        $activeTheme = \App\Models\ThemeSetting::getActiveTheme($survey->admin_id);

        return view('admin.surveys.response-detail', compact('survey', 'header', 'responses', 'activeTheme'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'improvement_areas' => 'nullable|array',
            'improvement_details' => 'nullable|array',
            'other_comments' => 'nullable|string',
            'details_categories_map' => 'nullable|string'
        ]);

        // Check if user has already responded
        if (SurveyResponseHeader::hasResponded($validated['survey_id'], $validated['account_name'])) {
            return response()->json([
                'error' => 'You have already submitted this survey.'
            ], 422);
        }

        // Get the survey with its questions
        $survey = Survey::with('questions')->findOrFail($request->survey_id);
        
        // Get valid question IDs from the survey
        $validQuestionIds = $survey->questions->pluck('id')->toArray();
        
        // Validate all questions are answered
        foreach ($validQuestionIds as $questionId) {
            if (!isset($request->responses[$questionId])) {
                return response()->json([
                    'error' => 'Missing response for question ID: ' . $questionId
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Get user's site information if authenticated
            $userSiteId = null;
            if (Auth::check()) {
                // Get the user's primary site (first site they have access to)
                $userSites = Auth::user()->sites;
                if ($userSites->isNotEmpty()) {
                    $userSiteId = $userSites->first()->id;
                }
            } elseif ($siteId = session('site_id')) {
                // Use site from session if available (for public surveys)
                $userSiteId = $siteId;
            }

            // Create header record
            $header = SurveyResponseHeader::create([
                'survey_id' => $validated['survey_id'],
                'admin_id' => $survey->admin_id,
                'user_site_id' => $userSiteId, // Store the surveyor's site
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'recommendation' => $validated['recommendation'],
                'allow_resubmit' => false
            ]);
            
            // Save improvement areas
            if ($request->has('improvement_areas') && is_array($request->improvement_areas)) {
                // Create a map to associate improvement details with their categories
                $detailsByCategory = [];
                
                // Process improvement details if present
                if ($request->has('improvement_details') && is_array($request->improvement_details)) {
                    // Use direct mapping from details_categories_map if available
                    if ($request->has('details_categories_map')) {
                        try {
                            $detailsCategoriesMap = json_decode($request->details_categories_map, true);
                            if (is_array($detailsCategoriesMap)) {
                                foreach ($detailsCategoriesMap as $item) {
                                    $category = $item['category'] ?? null;
                                    $detail = $item['detail'] ?? null;
                                    
                                    if ($category && $detail) {
                                        if (!isset($detailsByCategory[$category])) {
                                            $detailsByCategory[$category] = [];
                                        }
                                        $detailsByCategory[$category][] = $detail;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Log the error but continue with fallback
                            Log::warning("Error parsing details_categories_map: " . $e->getMessage());
                        }
                    }
                    
                    // If direct mapping isn't available or didn't work, fall back to service method
                    if (empty($detailsByCategory)) {
                        $detailsByCategory = SurveyImprovementService::mapDetailsToCategories($request->improvement_details);
                    }
                }
                
                // Process each improvement area
                foreach ($request->improvement_areas as $areaCategory) {
                    $isOther = ($areaCategory === 'others');
                    $otherComments = $isOther ? ($request->other_comments ?? '') : null;
                    $details = isset($detailsByCategory[$areaCategory]) ? $detailsByCategory[$areaCategory] : null;
                    
                    // Use the service to create improvement areas with details
                    SurveyImprovementService::createImprovementAreaWithDetails(
                        $header->id,
                        $areaCategory,
                        $details,
                        $isOther,
                        $otherComments
                    );
                }
            }

            // Create detail records for each response
            foreach ($validQuestionIds as $questionId) {
                SurveyResponseDetail::create([
                    'header_id' => $header->id,
                    'question_id' => $questionId,
                    'response' => $request->responses[$questionId]
                ]);
            }

            DB::commit();

            // Store account name in session for future checks
            $request->session()->put('account_name', $validated['account_name']);

            return response()->json([
                'success' => true,
                'message' => 'Survey response submitted successfully',
                'redirect' => route('index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit survey response. Please try again.'
            ], 500);
        }
    }
}
