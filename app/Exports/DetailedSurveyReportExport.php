<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DetailedSurveyReportExport implements WithMultipleSheets
{
    protected $survey;
    protected $siteAnalytics;
    protected $npsData;
    protected $questions;
    protected $totalResponses;
    protected $statistics;

    public function __construct($survey, $siteAnalytics, $npsData, $questions, $totalResponses, $statistics)
    {
        $this->survey = $survey;
        $this->siteAnalytics = $siteAnalytics;
        $this->npsData = $npsData;
        $this->questions = $questions;
        $this->totalResponses = $totalResponses;
        $this->statistics = $statistics;
    }

    public function sheets(): array
    {
        return [
            'Corporate Format' => new SurveyReportExport(
                $this->survey,
                $this->siteAnalytics,
                $this->npsData,
                $this->questions,
                $this->totalResponses,
                $this->statistics
            ),
            'Executive Summary' => new ExecutiveSummaryExport(
                $this->survey,
                $this->siteAnalytics,
                $this->npsData,
                $this->totalResponses
            ),
            'Sites Performance' => new SitesPerformanceExport($this->siteAnalytics, $this->npsData),
            'Question Analysis' => new QuestionAnalysisExport($this->questions, $this->siteAnalytics),
            'NPS Analysis' => new NPSAnalysisExport($this->npsData),
            'Rating Scale Guide' => new RatingScaleExport()
        ];
    }
}
