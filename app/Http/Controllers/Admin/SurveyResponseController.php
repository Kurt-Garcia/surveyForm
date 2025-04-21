<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponseHeader;
use App\Models\SurveyResponseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyResponseController extends Controller
{
    public function index(Survey $survey, Request $request)
    {
        $query = SurveyResponseHeader::where('survey_id', $survey->id);

        // Handle search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('account_name', 'like', '%' . $search . '%')
                  ->orWhere('account_type', 'like', '%' . $search . '%')
                  ->orWhere('date', 'like', '%' . $search . '%');
            });
        }

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
}
