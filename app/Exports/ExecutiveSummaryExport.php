<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExecutiveSummaryExport implements FromView, WithStyles, WithColumnWidths
{
    protected $survey;
    protected $siteAnalytics;
    protected $npsData;
    protected $totalResponses;

    public function __construct($survey, $siteAnalytics, $npsData, $totalResponses)
    {
        $this->survey = $survey;
        $this->siteAnalytics = $siteAnalytics;
        $this->npsData = $npsData;
        $this->totalResponses = $totalResponses;
    }

    public function view(): View
    {
        $hitSites = collect($this->siteAnalytics)->where('qms_target_status', 'HIT')->count();
        $hitPercentage = count($this->siteAnalytics) > 0 ? round(($hitSites / count($this->siteAnalytics)) * 100, 1) : 0;
        $avgNPS = collect($this->npsData)->avg('nps_score');

        return view('admin.surveys.exports.executive_summary', [
            'survey' => $this->survey,
            'totalResponses' => $this->totalResponses,
            'sitesCount' => count($this->siteAnalytics),
            'hitPercentage' => $hitPercentage,
            'avgNPS' => $avgNPS,
            'siteAnalytics' => $this->siteAnalytics,
            'npsData' => $this->npsData
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
            'A:A' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 15,
            'D' => 20,
        ];
    }
}
