<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SurveyReportExport;
use App\Exports\DetailedSurveyReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SurveyResponseController extends Controller
{
    public function index(Survey $survey, Request $request)
    {
        $query = SurveyResponseHeader::where('survey_id', $survey->id);
        
        // Get all responses for DataTables to handle filtering/pagination client-side
        $responses = $query->orderBy('date', 'desc')->get();
        $questions = $survey->questions;

        // Get response statistics for each question
        $statistics = [];
        foreach ($questions as $question) {
            $stats = DB::table('survey_response_details as d')
                ->join('survey_response_headers as h', 'h.id', '=', 'd.header_id')
                ->where('h.survey_id', $survey->id)
                ->where('d.question_id', $question->id)
                ->select('d.response', DB::raw('count(*) as count'))
                ->groupBy('d.response')
                ->get()
                ->pluck('count', 'response')
                ->toArray();
            
            $statistics[$question->id] = [
                'question' => $question->text,
                'type' => $question->type,
                'responses' => $stats
            ];
        }

        // Get average recommendation score
        $avgRecommendation = $query->avg('recommendation');

        return view('admin.surveys.responses', compact('survey', 'responses', 'questions', 'statistics', 'avgRecommendation'));
    }

    public function show(Survey $survey, $accountName)
    {
        $header = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $accountName)
            ->with(['details.question'])
            ->firstOrFail();

        $responses = $header->details;

        if ($responses->isEmpty()) {
            abort(404, 'Response not found');
        }

        // Calculate duration if both timestamps exist
        $duration = null;
        if ($header->start_time && $header->end_time) {
            $duration = $header->end_time->diffForHumans($header->start_time, ['parts' => 2]);
        }

        return view('admin.surveys.response-detail', compact('survey', 'header', 'responses'));
    }

    public function toggleResubmission(Survey $survey, $accountName)
    {
        $header = SurveyResponseHeader::where('survey_id', $survey->id)
            ->where('account_name', $accountName)
            ->firstOrFail();

        $header->update([
            'allow_resubmit' => !$header->allow_resubmit
        ]);

        return back()->with('success', 
            $header->allow_resubmit 
                ? 'Resubmission has been enabled for this response.' 
                : 'Resubmission has been disabled for this response.'
        );
    }

    public function uniqueRespondents(Survey $survey, Request $request)
    {
        $query = SurveyResponseHeader::where('survey_id', $survey->id);
        
        // Get all responses for DataTables to handle filtering/pagination client-side
        $responses = $query->orderBy('date', 'desc')->get();

        return view('admin.surveys.unique-respondents', compact('survey', 'responses'));
    }

    public function report(Survey $survey)
    {
        // Get survey with relationships
        $survey->load(['sbus', 'sites.sbu', 'questions']);
        
        $responses = SurveyResponseHeader::where('survey_id', $survey->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $questions = $survey->questions;

        // Get response statistics for each question
        $statistics = [];
        foreach ($questions as $question) {
            $stats = DB::table('survey_response_details as d')
                ->join('survey_response_headers as h', 'h.id', '=', 'd.header_id')
                ->where('h.survey_id', $survey->id)
                ->where('d.question_id', $question->id)
                ->select('d.response', DB::raw('count(*) as count'))
                ->groupBy('d.response')
                ->get()
                ->pluck('count', 'response')
                ->toArray();
            
            $statistics[$question->id] = [
                'question' => $question->text,
                'type' => $question->type,
                'responses' => $stats
            ];
        }

        // Calculate site-based analytics
        $siteAnalytics = $this->calculateSiteAnalytics($survey, $responses, $questions);
        
        // Calculate NPS for each site (assuming question about recommendation exists)
        $npsData = $this->calculateNPS($survey, $responses);

        // Get average recommendation score
        $avgRecommendation = $responses->avg('recommendation');

        // Additional report statistics
        $totalResponses = $responses->count();
        $uniqueRespondents = $responses->unique('account_name')->count();
        $completionRate = $totalResponses > 0 ? 100 : 0;
        
        // Calculate hit percentage and average NPS
        $hitSites = collect($siteAnalytics)->where('qms_target_status', 'HIT')->count();
        $hitPercentage = count($siteAnalytics) > 0 ? round(($hitSites / count($siteAnalytics)) * 100, 1) : 0;
        $avgNPS = collect($npsData)->avg('nps_score') ?? 0;
        
        // Response distribution by date
        $responsesByDate = $responses->groupBy(function($response) {
            return $response->date->format('Y-m-d');
        })->map->count();

        // Response distribution by account type
        $responsesByType = $responses->groupBy('account_type')->map->count();

        return view('admin.surveys.report', compact(
            'survey', 
            'responses', 
            'questions', 
            'statistics', 
            'avgRecommendation',
            'totalResponses',
            'uniqueRespondents',
            'completionRate',
            'responsesByDate',
            'responsesByType',
            'siteAnalytics',
            'npsData',
            'hitPercentage',
            'avgNPS'
        ));
    }

    private function calculateSiteAnalytics($survey, $responses, $questions)
    {
        $siteAnalytics = [];
        
        // Get unique admin IDs from responses to determine which sites actually have responses
        $adminIds = $responses->pluck('admin_id')->unique()->filter();
        
        if ($adminIds->isEmpty()) {
            return $siteAnalytics; // No responses to analyze
        }
        
        // Get all sites that have admins who submitted responses
        $sitesWithResponses = collect();
        foreach ($adminIds as $adminId) {
            $admin = Admin::find($adminId);
            if ($admin && $admin->sites) {
                $adminSites = $admin->sites;
                foreach ($adminSites as $site) {
                    // Only include sites that are part of this survey
                    if ($survey->sites->contains('id', $site->id)) {
                        $sitesWithResponses->push($site);
                    }
                }
            }
        }
        
        // Remove duplicates based on site ID
        $sitesWithResponses = $sitesWithResponses->unique('id');
        
        foreach ($sitesWithResponses as $site) {
            // Get responses from admins who have access to this site
            $siteAdminIds = DB::table('admin_site')
                ->where('site_id', $site->id)
                ->pluck('admin_id');
            $siteResponses = $responses->whereIn('admin_id', $siteAdminIds);
            
            if ($siteResponses->count() == 0) {
                continue; // Skip sites with no responses
            }
            
            $analytics = [
                'site_name' => $site->name,
                'sbu_name' => $site->sbu->name,
                'is_main' => $site->is_main,
                'respondent_count' => $siteResponses->count(),
                'question_ratings' => [],
                'overall_rating' => 0,
                'rating_label' => 'N/A',
                'qms_target_status' => 'MISS'
            ];
            
            // Calculate average rating for each question
            $totalRatings = [];
            foreach ($questions as $question) {
                if ($question->type === 'radio' || $question->type === 'star') {
                    // Get responses for this question from this site
                    $questionResponses = $siteResponses->map(function($response) use ($question) {
                        $detail = DB::table('survey_response_details')
                            ->where('header_id', $response->id)
                            ->where('question_id', $question->id)
                            ->value('response');
                        return is_numeric($detail) ? (float)$detail : null;
                    })->filter()->values();
                    
                    if ($questionResponses->count() > 0) {
                        $avgRating = $questionResponses->avg();
                        $analytics['question_ratings'][$question->id] = [
                            'question' => $question->text,
                            'average' => $avgRating,
                            'label' => $this->getRatingLabel($avgRating)
                        ];
                        $totalRatings[] = $avgRating;
                    }
                }
            }
            
            // Calculate overall rating
            if (!empty($totalRatings)) {
                $analytics['overall_rating'] = array_sum($totalRatings) / count($totalRatings);
                $analytics['rating_label'] = $this->getRatingLabel($analytics['overall_rating']);
                $analytics['qms_target_status'] = $analytics['overall_rating'] >= 4.0 ? 'HIT' : 'MISS';
            }
            
            $siteAnalytics[] = $analytics;
        }
        
        return $siteAnalytics;
    }
    
    private function calculateNPS($survey, $responses)
    {
        $npsData = [];
        
        // Get unique admin IDs from responses to determine which sites actually have responses
        $adminIds = $responses->pluck('admin_id')->unique()->filter();
        
        if ($adminIds->isEmpty()) {
            return $npsData; // No responses to analyze
        }
        
        // Get all sites that have admins who submitted responses
        $sitesWithResponses = collect();
        foreach ($adminIds as $adminId) {
            $admin = Admin::find($adminId);
            if ($admin && $admin->sites) {
                $adminSites = $admin->sites;
                foreach ($adminSites as $site) {
                    // Only include sites that are part of this survey
                    if ($survey->sites->contains('id', $site->id)) {
                        $sitesWithResponses->push($site);
                    }
                }
            }
        }
        
        // Remove duplicates based on site ID
        $sitesWithResponses = $sitesWithResponses->unique('id');
        
        foreach ($sitesWithResponses as $site) {
            // Get responses from admins who have access to this site
            $siteAdminIds = DB::table('admin_site')
                ->where('site_id', $site->id)
                ->pluck('admin_id');
            $siteResponses = $responses->whereIn('admin_id', $siteAdminIds);
            
            if ($siteResponses->count() == 0) {
                continue; // Skip sites with no responses
            }
            
            $npsScores = $siteResponses->pluck('recommendation')->map(function($score) {
                // Convert 1-10 scale recommendation to NPS categories
                if ($score >= 9) return 'promoter';
                if ($score >= 7) return 'passive';
                return 'detractor';
            });
            
            $total = $npsScores->count();
            $promoters = $npsScores->filter(fn($s) => $s === 'promoter')->count();
            $detractors = $npsScores->filter(fn($s) => $s === 'detractor')->count();
            
            $npsScore = $total > 0 ? (($promoters / $total) * 100) - (($detractors / $total) * 100) : 0;
            
            $npsStatus = 'MISS';
            if ($npsScore >= 70) $npsStatus = 'HIT';
            elseif ($npsScore >= 50) $npsStatus = 'Borderline';
            
            $npsData[] = [
                'site_name' => $site->name,
                'sbu_name' => $site->sbu->name,
                'total_respondents' => $total,
                'promoters' => $promoters,
                'detractors' => $detractors,
                'nps_score' => round($npsScore, 1),
                'status' => $npsStatus
            ];
        }
        
        return $npsData;
    }
    
    private function getRatingLabel($rating)
    {
        if ($rating >= 5.0) return 'E';
        if ($rating >= 4.0) return 'VS';
        if ($rating >= 3.0) return 'S';
        if ($rating >= 2.0) return 'NI';
        return 'P';
    }

    public function exportExcel(Survey $survey)
    {
        // Get the same data as the report method
        $survey->load(['sbus', 'sites.sbu', 'questions']);
        
        $responses = SurveyResponseHeader::where('survey_id', $survey->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $questions = $survey->questions;
        
        // Get response statistics for each question
        $statistics = [];
        foreach ($questions as $question) {
            $stats = DB::table('survey_response_details as d')
                ->join('survey_response_headers as h', 'h.id', '=', 'd.header_id')
                ->where('h.survey_id', $survey->id)
                ->where('d.question_id', $question->id)
                ->select('d.response', DB::raw('count(*) as count'))
                ->groupBy('d.response')
                ->get()
                ->pluck('count', 'response')
                ->toArray();
            
            $statistics[$question->id] = [
                'question' => $question->text,
                'type' => $question->type,
                'responses' => $stats
            ];
        }

        // Calculate site-based analytics
        $siteAnalytics = $this->calculateSiteAnalytics($survey, $responses, $questions);
        
        // Calculate NPS for each site
        $npsData = $this->calculateNPS($survey, $responses);
        
        $totalResponses = $responses->count();
        
        $export = new SurveyReportExport(
            $survey,
            $siteAnalytics,
            $npsData,
            $questions,
            $totalResponses,
            $statistics
        );
        
        $filename = 'Customer_Satisfaction_Survey_' . str_replace(' ', '_', $survey->title) . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download($export, $filename);
    }

    public function exportDetailedExcel(Survey $survey)
    {
        // Get the same data as the report method
        $survey->load(['sbus', 'sites.sbu', 'questions']);
        
        $responses = SurveyResponseHeader::where('survey_id', $survey->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $questions = $survey->questions;
        
        // Get response statistics for each question
        $statistics = [];
        foreach ($questions as $question) {
            $stats = DB::table('survey_response_details as d')
                ->join('survey_response_headers as h', 'h.id', '=', 'd.header_id')
                ->where('h.survey_id', $survey->id)
                ->where('d.question_id', $question->id)
                ->select('d.response', DB::raw('count(*) as count'))
                ->groupBy('d.response')
                ->get()
                ->pluck('count', 'response')
                ->toArray();
            
            $statistics[$question->id] = [
                'question' => $question->text,
                'type' => $question->type,
                'responses' => $stats
            ];
        }

        // Calculate site-based analytics
        $siteAnalytics = $this->calculateSiteAnalytics($survey, $responses, $questions);
        
        // Calculate NPS for each site
        $npsData = $this->calculateNPS($survey, $responses);
        
        $totalResponses = $responses->count();
        
        $export = new DetailedSurveyReportExport(
            $survey,
            $siteAnalytics,
            $npsData,
            $questions,
            $totalResponses,
            $statistics
        );
        
        $filename = 'Detailed_Customer_Satisfaction_Survey_' . str_replace(' ', '_', $survey->title) . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download($export, $filename);
    }
}
