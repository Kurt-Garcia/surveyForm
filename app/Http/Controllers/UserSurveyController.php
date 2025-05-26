<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
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
        $query = Survey::with(['questions', 'sites', 'sbu'])->where('is_active', true);
        
        if (Auth::check()) {
            $userSiteId = Auth::user()->site_id;
            session(['site_id' => $userSiteId]); // Ensure site_id is always in session
            
            $query->whereHas('sites', function($q) use ($userSiteId) {
                $q->where('sites.id', $userSiteId);
            });
        } elseif ($userSiteId = session('site_id')) {
            // Fallback to session if available (for session persistence)
            $query->whereHas('sites', function($q) use ($userSiteId) {
                $q->where('sites.id', $userSiteId);
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
        
        return view('index', compact('surveys'));
    }

    public function show(Survey $survey, Request $request)
    {
        // Check if survey is active
        if (!$survey->is_active) {
            return redirect()->route('index')
                ->with('warning', 'This survey is currently not active.');
        }
        
        // Verify the user's site_id matches one of the survey's sites
        $userSiteId = Auth::check() ? Auth::user()->site_id : session('site_id');
        
        if (!$survey->isAvailableForSite($userSiteId)) {
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
        return view('surveys.show', compact(
            'survey', 
            'questions', 
            'hasResponded', 
            'allowResubmit',
            'prefillAccountName',
            'prefillAccountType'
        ));
    }

    public function store(Request $request, Survey $survey)
    {
        // Verify the user's site_id matches one of the survey's sites
        $userSiteId = Auth::check() ? Auth::user()->site_id : session('site_id');
        
        if (!$survey->isAvailableForSite($userSiteId)) {
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
            'comments' => 'nullable|string',
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

            // Create header record
            $header = SurveyResponseHeader::create([
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'start_time' => $request->start_time ? \Carbon\Carbon::parse($request->start_time) : null,
                'end_time' => $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null,
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments'] ?? '',
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
        return view('surveys.customer-survey', compact(
            'survey', 
            'questions', 
            'hasResponded', 
            'allowResubmit',
            'prefillAccountName',
            'prefillAccountType'
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
        
        // Check if a site_id is available in the session
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
            'comments' => 'nullable|string',
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

            // Create header record
            $header = SurveyResponseHeader::create([
                'survey_id' => $survey->id,
                'admin_id' => $survey->admin_id,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'date' => $validated['date'],
                'start_time' => $request->start_time ? \Carbon\Carbon::parse($request->start_time) : null,
                'end_time' => $request->end_time ? \Carbon\Carbon::parse($request->end_time) : null,
                'recommendation' => $validated['recommendation'],
                'comments' => $validated['comments'] ?? '',
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
     * Send survey broadcast to selected customers
     */
    public function broadcastSurvey(Request $request, Survey $survey)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:TBLCUSTOMER,id',
        ]);

        $successCount = 0;
        $failCount = 0;
        $customerIds = $request->input('customer_ids');
        
        $customers = DB::table('TBLCUSTOMER')
            ->select('id', 'CUSTCODE', 'CUSTNAME', 'EMAIL', 'CUSTTYPE')
            ->whereIn('id', $customerIds)
            ->whereNotNull('EMAIL')
            ->where('EMAIL', '!=', '')
            ->get();
            
        foreach ($customers as $customer) {
            try {
                // Create personalized survey URL with customer name and account type pre-filled
                $surveyUrl = route('customer.survey', $survey->id) . '?account_name=' . urlencode($customer->CUSTNAME) . '&account_type=' . urlencode($customer->CUSTTYPE ?? 'Customer');
                
                // Send email to customer
                $emailData = [
                    'customer_name' => $customer->CUSTNAME,
                    'survey_title' => $survey->title,
                    'survey_url' => $surveyUrl
                ];
                
                Mail::send('emails.survey_invitation', $emailData, function($message) use ($customer, $survey) {
                    $message->to($customer->EMAIL, $customer->CUSTNAME)
                            ->subject('You\'re invited to complete a survey: ' . $survey->title)
                            ->from('testsurvey_1@w-itsolutions.com', 'Fast Distribution Corporation');
                });
                
                $successCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send survey email to ' . $customer->EMAIL . ': ' . $e->getMessage());
                $failCount++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Survey invitation sent to {$successCount} customer(s)",
            'failed' => $failCount
        ]);
    }
}