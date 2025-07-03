<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class QuestionAnalysisExport implements FromView, WithStyles, WithColumnWidths
{
    protected $questions;
    protected $siteAnalytics;

    public function __construct($questions, $siteAnalytics)
    {
        $this->questions = $questions;
        $this->siteAnalytics = $siteAnalytics;
    }

    public function view(): View
    {
        return view('admin.surveys.exports.question_analysis', [
            'questions' => $this->questions,
            'siteAnalytics' => $this->siteAnalytics
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 55];
        $siteCount = count($this->siteAnalytics);
        for ($i = 0; $i < $siteCount; $i++) {
            $widths[chr(66 + $i)] = 18;
        }
        $widths[chr(66 + $siteCount)] = 15; // Overall average column
        return $widths;
    }
}
