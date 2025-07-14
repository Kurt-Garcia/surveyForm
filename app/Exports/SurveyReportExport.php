<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

class SurveyReportExport implements FromView, WithStyles, WithColumnWidths, WithEvents
{
    protected $survey;
    protected $siteAnalytics;
    protected $npsData;
    protected $questions;
    protected $totalResponses;
    protected $statistics;
    protected $recommendationStats;
    protected $improvementAreasStats;

    public function __construct($survey, $siteAnalytics, $npsData, $questions, $totalResponses, $statistics, $recommendationStats = null, $improvementAreasStats = null)
    {
        $this->survey = $survey;
        $this->siteAnalytics = $siteAnalytics;
        $this->npsData = $npsData;
        $this->questions = $questions;
        $this->totalResponses = $totalResponses;
        $this->statistics = $statistics;
        $this->recommendationStats = $recommendationStats;
        $this->improvementAreasStats = $improvementAreasStats;
    }

    public function view(): View
    {
        return view('admin.surveys.export_template', [
            'survey' => $this->survey,
            'siteAnalytics' => $this->siteAnalytics,
            'npsData' => $this->npsData,
            'questions' => $this->questions,
            'totalResponses' => $this->totalResponses,
            'statistics' => $this->statistics,
            'recommendationStats' => $this->recommendationStats,
            'improvementAreasStats' => $this->improvementAreasStats
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $siteCount = count($this->siteAnalytics);
        $lastColumn = chr(66 + $siteCount - 1); // B + site count - 1
        
        return [
            // Title row - Blue background (#0066CC) with white text - matches sample exactly
            1 => [
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Subtitle row - Same blue background (#0066CC) with white text
            2 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Full company names row - White background with borders
            4 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Company acronyms row - White background with borders
            5 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Site headers row - White background with borders
            7 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Respondents row - White background with borders
            8 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 60]; // Question/label column wider like sample
        
        // Site columns - match the sample exact width
        $siteCount = count($this->siteAnalytics);
        for ($i = 0; $i < $siteCount; $i++) {
            $widths[chr(66 + $i)] = 25; // Fixed width to match sample
        }
        
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $siteCount = count($this->siteAnalytics);
                $lastColumn = chr(66 + $siteCount - 1);
                
                // Set row heights to match sample exactly
                $sheet->getRowDimension(1)->setRowHeight(40); // Title row
                $sheet->getRowDimension(2)->setRowHeight(24); // Subtitle row
                
                // Merge title and subtitle cells
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3"); // Empty row
                
                // No need to merge company names and acronyms - each site has its own
                // Company full names are in row 4, acronyms in row 5
                
                // Empty row merge
                $sheet->mergeCells("A6:{$lastColumn}6");
                
                // Get question rows for styling
                $ratingQuestions = $this->questions->filter(function($q) {
                    return $q->type === 'radio' || $q->type === 'star';
                });
                $questionStartRow = 9;
                $questionEndRow = $questionStartRow + $ratingQuestions->count() - 1;
                
                // Style question rows with borders - white background like sample
                for ($row = $questionStartRow; $row <= $questionEndRow; $row++) {
                    // Set row height to match sample exactly
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    
                    // Question text styling - left aligned with borders
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => false, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                    
                    // Answer cells styling - centered with borders
                    $sheet->getStyle("B{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                
                // Empty row after questions
                $emptyRow = $questionEndRow + 1;
                
                // Rating scale row styling - match sample exactly
                $ratingRow = $questionEndRow + 2;
                $sheet->mergeCells("A{$ratingRow}:{$lastColumn}{$ratingRow}");
                $sheet->getStyle("A{$ratingRow}:{$lastColumn}{$ratingRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF99']], // Light yellow background like sample
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);

                // Rating range legend row styling - same yellow background, centered
                $ratingRangeRow = $ratingRow + 1;
                $sheet->mergeCells("A{$ratingRangeRow}:{$lastColumn}{$ratingRangeRow}");
                $sheet->getStyle("A{$ratingRangeRow}:{$lastColumn}{$ratingRangeRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF99']], // Light yellow background like sample
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Empty row before overall rating
                $emptyRowBeforeOverall = $ratingRangeRow + 1;
                
                // Overall rating section header - Blue background like sample
                $overallHeaderRow = $ratingRangeRow + 2;
                $sheet->mergeCells("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}");
                $sheet->getStyle("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']], // Blue background like sample
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                $sheet->getRowDimension($overallHeaderRow)->setRowHeight(21);
                
                // Overall rating values row - white background
                $overallValueRow = $overallHeaderRow + 1;
                // First cell empty but with border
                $sheet->getStyle("A{$overallValueRow}")->applyFromArray([
                    'font' => ['bold' => false, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Rating values cells
                $sheet->getStyle("B{$overallValueRow}:{$lastColumn}{$overallValueRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating labels row (P, NI, S, VS, E) - white background with color coding
                $overallLabelsRow = $overallValueRow + 1;
                // First cell empty but with border
                $sheet->getStyle("A{$overallLabelsRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating labels styling - white background without color coding
                for ($i = 0; $i < $siteCount; $i++) {
                    $col = chr(66 + $i);
                    
                    $sheet->getStyle("{$col}{$overallLabelsRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                
                // QMS Target status row with color coding - match sample colors exactly
                $qmsRow = $overallLabelsRow + 1;
                $sheet->getStyle("A{$qmsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code QMS status - white background without color coding
                for ($i = 0; $i < $siteCount; $i++) {
                    $col = chr(66 + $i);
                    
                    $sheet->getStyle("{$col}{$qmsRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                
                // Empty row before NPS
                $emptyRowBeforeNPS = $qmsRow + 1;
                
                // NPS Section header - blue background like sample
                $npsHeaderRow = $qmsRow + 2;
                $sheet->mergeCells("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}");
                $sheet->getStyle("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']], // Blue background like sample
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                $sheet->getRowDimension($npsHeaderRow)->setRowHeight(21);
                
                // NPS legend row - showing detractors, passives, promoters
                $npsLegendRow = $npsHeaderRow + 1;
                $sheet->getStyle("A{$npsLegendRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Add the NPS legend text with colored formatting
                // Display all three NPS ranges in one cell using emoji squares
                $sheet->setCellValue("A{$npsLegendRow}", "0-6 (RED)  7-8 (YELLOW)  9-10 (GREEN)");
                
                // Empty cells in the NPS legend row
                $sheet->getStyle("B{$npsLegendRow}:{$lastColumn}{$npsLegendRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                $sheet->getRowDimension($npsLegendRow)->setRowHeight(21);
                
                // NPS scores row - white background
                $npsScoreRow = $npsLegendRow + 1;
                $sheet->getStyle("A{$npsScoreRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // NPS status row with color coding - match sample colors
                $npsStatusRow = $npsScoreRow + 1;
                $sheet->getStyle("A{$npsStatusRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code NPS status - white background without color coding
                for ($i = 0; $i < count($this->npsData); $i++) {
                    $col = chr(66 + $i);
                    
                    $sheet->getStyle("{$col}{$npsStatusRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                
                // Calculate the current position after NPS section
                $currentRow = $npsStatusRow + 4; // 3 empty rows + 1
                
                // Style NPS scores row - white background without color coding
                for ($i = 0; $i < count($this->npsData); $i++) {
                    $col = chr(66 + $i);
                    
                    $sheet->getStyle("{$col}{$npsScoreRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '000000'], 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                
                // Add styling for Areas for Improvement Section if it exists
                if ($this->improvementAreasStats && isset($this->improvementAreasStats['categories']) && count($this->improvementAreasStats['categories']) > 0) {
                    // Areas for Improvement header
                    $improvementHeaderRow = $currentRow;
                    $sheet->mergeCells("A{$improvementHeaderRow}:{$lastColumn}{$improvementHeaderRow}");
                    $sheet->getStyle("A{$improvementHeaderRow}:{$lastColumn}{$improvementHeaderRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']], // Blue background
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                    ]);
                    $sheet->getRowDimension($improvementHeaderRow)->setRowHeight(21);
                    
                    // Count top improvement categories (max 5)
                    $topCategoriesCount = min(5, count($this->improvementAreasStats['top_categories']));
                    
                    // Improvement categories rows + 1 empty row
                    for ($i = 1; $i <= $topCategoriesCount + 1; $i++) {
                        $row = $improvementHeaderRow + $i;
                        
                        // First column styling
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                        ]);
                        
                        // Data cells styling
                        $sheet->getStyle("B{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                        ]);
                    }
                    
                    $currentRow += $topCategoriesCount + 2; // categories rows + 1 empty row + 1 header row
                }
                
                // Signature section header - match sample styling
                $signatureHeaderRow = $currentRow;
                $sheet->mergeCells("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}");
                $sheet->getStyle("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Empty rows for signature space
                $signatureSpaceRow1 = $signatureHeaderRow + 1;
                $signatureSpaceRow2 = $signatureSpaceRow1 + 1;
                $signatureSpaceRow3 = $signatureSpaceRow2 + 1;
                
                // Names row
                $namesRow = $signatureSpaceRow3 + 1;
                
                // Titles row
                $titlesRow = $namesRow + 1;
            }
        ];
    }
}
