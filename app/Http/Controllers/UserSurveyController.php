<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use App\Models\SurveyImprovementArea;
use App\Models\SurveyImprovementCategory;
use App\Models\SurveyImprovementDetail;
use App\Services\SurveyImprovementService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserSurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = Survey::with(['questions', 'sites', 'sbus', 'admin'])->where('is_active', true);
        
        if (Auth::check()) {
            // Get all site IDs the user has access to
            $userSiteIds = Auth::user()->sites->pluck('id')->toArray();
            
            if (!empty($userSiteIds)) {
                // Filter surveys that are deployed to any of the user's sites
                $query->whereHas('sites', function($q) use ($userSiteIds) {
                    $q->whereIn('site_id', $userSiteIds);
                });
                
                // Store user's site IDs in session for fallback
                session(['user_site_ids' => $userSiteIds]);
            }
        } elseif ($userSiteIds = session('user_site_ids')) {
            // Fallback to session if available (for session persistence)
            $query->whereHas('sites', function($q) use ($userSiteIds) {
                $q->whereIn('site_id', $userSiteIds);
            });
        }
        
        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('title', 'LIKE', "%{$searchTerm}%");
        }
        
        $surveys = $query->with('questions')->paginate(6);
        
        // Preserve search parameter in pagination links
        if ($request->has('search')) {
            $surveys->appends(['search' => $request->search]);
        }
        
        // Get active themes for each survey's admin
        $surveyThemes = [];
        foreach ($surveys as $survey) {
            if ($survey->admin_id) {
                $surveyThemes[$survey->id] = \App\Models\ThemeSetting::getActiveTheme($survey->admin_id);
            }
        }
        
        return view('index', compact('surveys', 'surveyThemes'));
    }

    public function show(Survey $survey, Request $request)
    {
        // Check if survey is active
        if (!$survey->is_active) {
            return redirect()->route('index')
                ->with('warning', 'This survey is currently not active.');
        }
        
        // Verify the user has access to one of the survey's sites
        $hasAccess = false;
        
        if (Auth::check()) {
            // Get all site IDs the user has access to
            $userSiteIds = Auth::user()->sites->pluck('id')->toArray();
            
            if (!empty($userSiteIds)) {
                // Check if survey is deployed to any of the user's sites
                $hasAccess = $survey->isAvailableForAnySite($userSiteIds);
            }
        } elseif ($userSiteIds = session('user_site_ids')) {
            // Fallback to session if available
            $hasAccess = $survey->isAvailableForAnySite($userSiteIds);
        }
        
        if (!$hasAccess) {
            return redirect()->route('index')
                ->with('error', 'You do not have access to this survey.');
        }

        $hasResponded = false;
        $allowResubmit = false;
        
        if ($accountName = session('account_name')) {
            $response = SurveyResponseHeader::where('survey_id', $survey->id)
                ->where('account_name', $accountName)
                ->first();
            
            if ($response) {
                $hasResponded = true;
                $allowResubmit = $response->allow_resubmit;
            }
        }

        // Get prefilled data from URL parameters
        $prefillAccountName = $request->query('account_name');
        $prefillAccountType = $request->query('account_type');

        $questions = $survey->questions;
        
        // Get the active theme for the survey's admin
        $activeTheme = \App\Models\ThemeSetting::getActiveTheme($survey->admin_id);
        
        // Get user's site IDs for filtering customers
        $userSiteIds = [];
        if (Auth::check()) {
            $userSiteIds = Auth::user()->sites->pluck('id')->toArray();
        } elseif ($sessionSiteIds = session('user_site_ids')) {
            $userSiteIds = $sessionSiteIds;
        }
        
        return view('surveys.show', compact(
            'survey', 
            'questions', 
            'hasResponded', 
            'allowResubmit',
            'prefillAccountName',
            'prefillAccountType',
            'activeTheme',
            'userSiteIds'
        ));
    }

    public function store(Request $request, Survey $survey)
    {
        // Verify the user has access to one of the survey's sites
        $hasAccess = false;
        
        if (Auth::check()) {
            // Get all site IDs the user has access to
            $userSiteIds = Auth::user()->sites->pluck('id')->toArray();
            
            if (!empty($userSiteIds)) {
                // Check if survey is deployed to any of the user's sites
                $hasAccess = $survey->isAvailableForAnySite($userSiteIds);
            }
        } elseif ($userSiteIds = session('user_site_ids')) {
            // Fallback to session if available
            $hasAccess = $survey->isAvailableForAnySite($userSiteIds);
        }
        
        if (!$hasAccess) {
            return response()->json([
                'error' => 'You do not have access to submit this survey.'
            ], 403);
        }
        
        // Check if user has already responded
        $existingResponse = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $request->account_name)
            ->first();

        if ($existingResponse) {
            if (!$existingResponse->allow_resubmit) {
                return response()->json([
                    'error' => 'You have already submitted this survey.'
                ], 422);
            }
        }

        $validated = $request->validate([
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'improvement_areas' => 'nullable|array',
            'improvement_details' => 'nullable|array',
            'other_comments' => 'nullable|string',
            'start_time' => 'nullable',
            'end_time' => 'nullable'
        ]);

        // Get required questions and validate them
        $requiredQuestions = $survey->questions()->where('required', true)->get();
        foreach ($requiredQuestions as $question) {
            if (!isset($request->responses[$question->id]) || empty($request->responses[$question->id])) {
                return response()->json([
                    'error' => "Question '{$question->text}' is required."
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // If resubmission is allowed, delete previous response
            if ($existingResponse) {
                $existingResponse->details()->delete();
                $existingResponse->delete();
            }

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
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'user_site_id' => $userSiteId, // Store the surveyor's site
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'start_time' => $request->start_time ? \Carbon\Carbon::parse($request->start_time) : null,
                'end_time' => $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null,
                'recommendation' => $validated['recommendation'],
                'allow_resubmit' => false
            ]);

            // Create detail records
            foreach ($request->responses as $questionId => $response) {
                SurveyResponseDetail::create([
                    'header_id' => $header->id,
                    'question_id' => $questionId,
                    'response' => $response
                ]);
            }
            
            // Save improvement areas
            if ($request->has('improvement_areas') && is_array($request->improvement_areas)) {
                // Create a map to associate improvement details with their categories
                $detailsByCategory = [];
                
                // Process improvement details if present
                if ($request->has('improvement_details') && is_array($request->improvement_details)) {
                    foreach ($request->improvement_details as $detail) {
                        // For each checkbox, determine its category
                        $category = null;
                        
                        // Map each detail to its parent category
                        if (strpos($detail, 'product') !== false) {
                            $category = 'product_quality';
                        } elseif (strpos($detail, 'delivery') !== false) {
                            $category = 'delivery_logistics';
                        } elseif (strpos($detail, 'service') !== false || strpos($detail, 'sales') !== false) {
                            $category = 'customer_service';
                        } elseif (strpos($detail, 'time') !== false) {
                            $category = 'timeliness';
                        } elseif (strpos($detail, 'return') !== false || strpos($detail, 'BO') !== false) {
                            $category = 'returns_handling';
                        }
                        
                        if ($category) {
                            if (!isset($detailsByCategory[$category])) {
                                $detailsByCategory[$category] = [];
                            }
                            $detailsByCategory[$category][] = $detail;
                        }
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

            DB::commit();

            // Store account name in session for future checks
            $request->session()->put('account_name', $validated['account_name']);

            return response()->json([
                'success' => true,
                'message' => 'Survey response submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit survey response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thankyou()
    {
        return view('surveys.thankyou');
    }

    /**
     * Display the survey directly to customers without requiring login
     */
    public function customerSurvey(Survey $survey, Request $request)
    {
        // Check if survey is active
        if (!$survey->is_active) {
            return redirect()->route('welcome')
                ->with('warning', 'This survey is currently not active.');
        }

        // For customer surveys, we should check if a site_id is provided in the query string
        // This allows public surveys to be filtered by site as well
        $siteId = $request->query('site_id');
        
        if ($siteId) {
            // If a specific site_id is provided, check if the survey is deployed to that site
            if (!$survey->isAvailableForSite($siteId)) {
                return redirect()->route('welcome')
                    ->with('error', 'This survey is not available for your site.');
            }
            // Store site_id in session for future requests
            session(['site_id' => $siteId]);
        }

        // Get prefilled data from URL parameters
        $prefillAccountName = $request->query('account_name');
        $prefillAccountType = $request->query('account_type');

        $hasResponded = false;
        $allowResubmit = false;
        
        if ($accountName = $prefillAccountName) {
            $response = SurveyResponseHeader::where('survey_id', $survey->id)
                ->where('account_name', $accountName)
                ->first();
            
            if ($response) {
                $hasResponded = true;
                $allowResubmit = $response->allow_resubmit;
            }
        }

        $questions = $survey->questions;
        
        // Get the active theme for the survey's admin
        $activeTheme = \App\Models\ThemeSetting::getActiveTheme($survey->admin_id);
        
        return view('surveys.customer-survey', compact(
            'survey', 
            'questions', 
            'hasResponded', 
            'allowResubmit',
            'prefillAccountName',
            'prefillAccountType',
            'activeTheme'
        ));
    }

    /**
     * Store a response from a customer without requiring login
     */
    public function checkAccountExists(Request $request)
    {
        $existingResponse = SurveyResponseHeader::where('survey_id', $request->survey_id)
            ->where('account_name', $request->account_name)
            ->first();

        return response()->json([
            'exists' => $existingResponse ? true : false,
            'allow_resubmit' => $existingResponse ? $existingResponse->allow_resubmit : false
        ]);
    }

    public function customerStore(Request $request, Survey $survey)
    {
        // Check if survey is active
        if (!$survey->is_active) {
            return response()->json([
                'error' => 'This survey is currently not active.'
            ], 422);
        }
        
        // Check if a site_id is available in the session (from the URL parameter)
        $siteId = session('site_id');
        if ($siteId) {
            // If a site_id is stored, check if the survey is deployed to that site
            if (!$survey->isAvailableForSite($siteId)) {
                return response()->json([
                    'error' => 'This survey is not available for your site.'
                ], 403);
            }
        }
        
        // Check if user has already responded
        $existingResponse = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $request->account_name)
            ->first();

        if ($existingResponse) {
            if (!$existingResponse->allow_resubmit) {
                return response()->json([
                    'error' => 'You have already submitted this survey.'
                ], 422);
            }
        }

        $validated = $request->validate([
            'account_name' => 'required|string',
            'account_type' => 'required|string',
            'date' => 'required|date',
            'responses' => 'required|array',
            'recommendation' => 'required|integer|between:1,10',
            'improvement_areas' => 'nullable|array',
            'improvement_details' => 'nullable|array',
            'other_comments' => 'nullable|string',
            'start_time' => 'nullable',
            'end_time' => 'nullable'
        ]);

        // Get required questions and validate them
        $requiredQuestions = $survey->questions()->where('required', true)->get();
        foreach ($requiredQuestions as $question) {
            if (!isset($request->responses[$question->id]) || empty($request->responses[$question->id])) {
                return response()->json([
                    'error' => "Question '{$question->text}' is required."
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // If resubmission is allowed, delete previous response
            if ($existingResponse) {
                $existingResponse->details()->delete();
                $existingResponse->delete();
            }

            // Get user's site information
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
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'user_site_id' => $userSiteId, // Store the surveyor's site
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'start_time' => $request->start_time ? \Carbon\Carbon::parse($request->start_time) : null,
                'end_time' => $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null,
                'recommendation' => $validated['recommendation'],
                'allow_resubmit' => false
            ]);

            // Create detail records
            foreach ($request->responses as $questionId => $response) {
                SurveyResponseDetail::create([
                    'header_id' => $header->id,
                    'question_id' => $questionId,
                    'response' => $response
                ]);
            }
            
            // Save improvement areas
            if ($request->has('improvement_areas') && is_array($request->improvement_areas)) {
                // Create a map to associate improvement details with their categories
                $detailsByCategory = [];
                
                // Process improvement details if present
                if ($request->has('improvement_details') && is_array($request->improvement_details)) {
                    foreach ($request->improvement_details as $detail) {
                        // For each checkbox, determine its category
                        $category = null;
                        
                        // Map each detail to its parent category
                        if (strpos($detail, 'product') !== false) {
                            $category = 'product_quality';
                        } elseif (strpos($detail, 'delivery') !== false) {
                            $category = 'delivery_logistics';
                        } elseif (strpos($detail, 'service') !== false || strpos($detail, 'sales') !== false) {
                            $category = 'customer_service';
                        } elseif (strpos($detail, 'time') !== false) {
                            $category = 'timeliness';
                        } elseif (strpos($detail, 'return') !== false || strpos($detail, 'BO') !== false) {
                            $category = 'returns_handling';
                        }
                        
                        if ($category) {
                            if (!isset($detailsByCategory[$category])) {
                                $detailsByCategory[$category] = [];
                            }
                            $detailsByCategory[$category][] = $detail;
                        }
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

            DB::commit();

            // Store account name in session for future checks
            if ($request->session()->isStarted()) {
                $request->session()->put('account_name', $validated['account_name']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Survey response submitted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit survey response: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customers for the broadcast modal
     */
    public function getCustomers(Survey $survey)
    {
        $customers = DB::table('TBLCUSTOMER')
            ->select('id', 'CUSTCODE', 'CUSTNAME', 'EMAIL', 'CUSTTYPE')
            ->whereNotNull('EMAIL')
            ->where('EMAIL', '!=', '')
            ->orderBy('CUSTNAME')
            ->get();
        
        return response()->json([
            'customers' => $customers
        ]);
    }

    /**
     * Send survey broadcast to selected customers using async queue processing
     */
    public function broadcastSurvey(Request $request, Survey $survey)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:TBLCUSTOMER,id',
        ]);

        $customerIds = $request->input('customer_ids');
        
        // Validate customer emails exist
        $validCustomersCount = DB::table('TBLCUSTOMER')
            ->whereIn('id', $customerIds)
            ->whereNotNull('EMAIL')
            ->where('EMAIL', '!=', '')
            ->count();
            
        if ($validCustomersCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No valid customer emails found for broadcast'
            ], 400);
        }
        
        // Generate unique batch ID for progress tracking
        $batchId = 'survey_' . $survey->id . '_' . uniqid();
        
        // Dispatch the broadcast job to queue
        \App\Jobs\ProcessSurveyBroadcastJob::dispatch($survey, $customerIds, $batchId);
        
        Log::info("Survey broadcast initiated for survey {$survey->title} with batch ID {$batchId}");
        
        return response()->json([
            'success' => true,
            'message' => "Survey broadcast has been queued for {$validCustomersCount} customer(s)",
            'batch_id' => $batchId,
            'total_customers' => $validCustomersCount
        ]);
    }

    /**
     * Get broadcast progress for real-time updates
     */
    public function getBroadcastProgress($batchId)
    {
        $cacheKey = "broadcast_progress_{$batchId}";
        $progress = \Illuminate\Support\Facades\Cache::get($cacheKey, [
            'sent' => 0,
            'failed' => 0,
            'total' => 0,
            'status' => 'not_found'
        ]);
        
        // Calculate completion percentage
        $total = $progress['total'] ?? 0;
        $processed = ($progress['sent'] ?? 0) + ($progress['failed'] ?? 0);
        $percentage = $total > 0 ? round(($processed / $total) * 100, 2) : 0;
        
        // Determine status
        if ($progress['status'] === 'not_found') {
            $status = 'not_found';
        } elseif ($processed >= $total && $total > 0) {
            $status = 'completed';
        } else {
            $status = 'processing';
        }
        
        return response()->json([
            'batch_id' => $batchId,
            'sent' => $progress['sent'] ?? 0,
            'failed' => $progress['failed'] ?? 0,
            'total' => $total,
            'processed' => $processed,
            'percentage' => $percentage,
            'status' => $status,
            'started_at' => $progress['started_at'] ?? null
        ]);
    }

    /**
     * Health check endpoint for monitoring broadcast system
     */
    public function healthCheck()
    {
        try {
            // Check database connection
            $dbStatus = DB::select('SELECT 1 as test');
            
            // Check if jobs table exists and is accessible
            $jobsTableExists = DB::getSchemaBuilder()->hasTable('jobs');
            
            // Check recent queue activity (last 10 minutes)
            $recentJobs = DB::table('jobs')
                ->where('created_at', '>', now()->subMinutes(10))
                ->count();
            
            // Check failed jobs count
            $failedJobs = DB::table('failed_jobs')
                ->where('failed_at', '>', now()->subHour())
                ->count();
            
            // Check email configuration
            $emailConfigured = config('mail.mailers.smtp.host') !== null;
            
            $status = [
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'checks' => [
                    'database' => !empty($dbStatus),
                    'jobs_table' => $jobsTableExists,
                    'email_config' => $emailConfigured,
                    'recent_job_activity' => $recentJobs,
                    'failed_jobs_last_hour' => $failedJobs
                ],
                'queue_stats' => [
                    'pending_jobs' => DB::table('jobs')->count(),
                    'failed_jobs' => DB::table('failed_jobs')->count(),
                    'recent_activity' => $recentJobs > 0
                ]
            ];
            
            // Determine overall health
            $isHealthy = $status['checks']['database'] && 
                        $status['checks']['jobs_table'] && 
                        $status['checks']['email_config'] &&
                        $status['checks']['failed_jobs_last_hour'] < 10; // Less than 10 failures per hour
            
            if (!$isHealthy) {
                $status['status'] = 'degraded';
            }
            
            return response()->json($status, $isHealthy ? 200 : 503);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ], 503);
        }
    }
}