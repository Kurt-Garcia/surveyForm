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

class SurveyReportExport implements FromView, WithStyles, WithColumnWidths, WithEvents
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

    public function view(): View
    {
        return view('admin.surveys.export_template', [
            'survey' => $this->survey,
            'siteAnalytics' => $this->siteAnalytics,
            'npsData' => $this->npsData,
            'questions' => $this->questions,
            'totalResponses' => $this->totalResponses,
            'statistics' => $this->statistics
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $siteCount = count($this->siteAnalytics);
        $lastColumn = chr(66 + $siteCount - 1); // B + site count - 1
        
        return [
            // Title row - Blue background (#0066CC) with white text - matches sample
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
            
            // Company header row - White background with borders
            4 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Site headers row - White background with borders
            6 => [
                'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Respondents row - White background with borders
            7 => [
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
        
        // Site columns - consistent width like sample
        $siteCount = count($this->siteAnalytics);
        for ($i = 0; $i < $siteCount; $i++) {
            $widths[chr(66 + $i)] = 15; // B, C, D, etc. - match sample column widths
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
                
                // Merge title and subtitle cells
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3"); // Empty row
                
                // Company header merge (assuming last two columns for company info)
                if ($siteCount >= 2) {
                    $companyStartCol = chr(66 + $siteCount - 2);
                    $sheet->mergeCells("{$companyStartCol}4:{$lastColumn}4");
                }
                
                // Empty row merge
                $sheet->mergeCells("A5:{$lastColumn}5");
                
                // Get question rows for styling
                $ratingQuestions = $this->questions->filter(function($q) {
                    return $q->type === 'radio' || $q->type === 'star';
                });
                $questionStartRow = 8;
                $questionEndRow = $questionStartRow + $ratingQuestions->count() - 1;
                
                // Style question rows with borders - white background like sample
                for ($row = $questionStartRow; $row <= $questionEndRow; $row++) {
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                        ],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10, 'name' => 'Arial']
                    ]);
                    
                    // Left align question text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Rating scale row styling - yellow background like sample
                $ratingRow = $questionEndRow + 2;
                $sheet->mergeCells("A{$ratingRow}:{$lastColumn}{$ratingRow}");
                $sheet->getStyle("A{$ratingRow}:{$lastColumn}{$ratingRow}")->applyFromArray([
                    'font' => ['size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']], // Yellow background
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating section header - Blue background like sample
                $overallHeaderRow = $ratingRow + 3;
                $sheet->mergeCells("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}");
                $sheet->getStyle("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']], // Same blue as title
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating values row - white background
                $overallValueRow = $overallHeaderRow + 1;
                $sheet->getStyle("A{$overallValueRow}:{$lastColumn}{$overallValueRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']]
                ]);
                
                // Left align overall rating label
                $sheet->getStyle("A{$overallValueRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // QMS Target status row with color coding - match sample colors exactly
                $qmsRow = $overallValueRow + 1;
                $sheet->getStyle("A{$qmsRow}:{$lastColumn}{$qmsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code QMS status - use exact colors from sample
                for ($i = 0; $i < $siteCount; $i++) {
                    $col = chr(66 + $i);
                    $status = $this->siteAnalytics[$i]['qms_target_status'] ?? 'MISS';
                    $color = $status === 'HIT' ? '00FF00' : 'FF0000'; // Pure green for HIT, Pure red for MISS like sample
                    $sheet->getStyle("{$col}{$qmsRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $color]],
                        'font' => ['color' => ['rgb' => '000000'], 'bold' => true, 'name' => 'Arial'] // Black text like sample
                    ]);
                }
                
                // NPS Section header - blue background like sample
                $npsHeaderRow = $qmsRow + 3;
                $sheet->mergeCells("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}");
                $sheet->getStyle("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0066CC']], // Same blue as other headers
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // NPS scores row - white background
                $npsScoreRow = $npsHeaderRow + 1;
                $sheet->getStyle("A{$npsScoreRow}:{$lastColumn}{$npsScoreRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']]
                ]);
                
                // Left align NPS label
                $sheet->getStyle("A{$npsScoreRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // NPS status row with color coding - match sample colors
                $npsStatusRow = $npsScoreRow + 1;
                $sheet->getStyle("A{$npsStatusRow}:{$lastColumn}{$npsStatusRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code NPS status - use exact colors from sample
                for ($i = 0; $i < count($this->npsData); $i++) {
                    $col = chr(66 + $i);
                    $status = $this->npsData[$i]['status'] ?? 'MISS';
                    $color = $status === 'HIT' ? '00FF00' : ($status === 'Borderline' ? 'FFFF00' : 'FF0000'); // Green, Yellow, Red
                    $sheet->getStyle("{$col}{$npsStatusRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $color]],
                        'font' => ['color' => ['rgb' => '000000'], 'bold' => true, 'name' => 'Arial'] // Black text like sample
                    ]);
                }
                
                // Feedback section header - red background like sample
                $feedbackHeaderRow = $npsStatusRow + 3;
                $sheet->mergeCells("A{$feedbackHeaderRow}:{$lastColumn}{$feedbackHeaderRow}");
                $sheet->getStyle("A{$feedbackHeaderRow}:{$lastColumn}{$feedbackHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000'], 'name' => 'Arial'], // Black text
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FF0000']], // Red background like sample
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Positive feedback header row - green background like sample
                $positiveFeedbackRow = $feedbackHeaderRow + 2;
                $sheet->getStyle("A{$positiveFeedbackRow}:{$lastColumn}{$positiveFeedbackRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000'], 'name' => 'Arial'], // Black text
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '00FF00']], // Pure green like sample
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Left align positive feedback label
                $sheet->getStyle("A{$positiveFeedbackRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Style positive feedback data rows (5 rows) - white background
                for ($i = 1; $i <= 5; $i++) {
                    $row = $positiveFeedbackRow + $i;
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']]
                    ]);
                    
                    // Left align feedback text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Areas for improvement header row - yellow background like sample
                $improvementHeaderRow = $positiveFeedbackRow + 8; // 5 feedback rows + 2 empty rows + 1
                $sheet->getStyle("A{$improvementHeaderRow}:{$lastColumn}{$improvementHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000'], 'name' => 'Arial'], // Black text
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']], // Pure yellow like sample
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Left align improvement label
                $sheet->getStyle("A{$improvementHeaderRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Style improvement data rows (5 rows) - white background
                for ($i = 1; $i <= 5; $i++) {
                    $row = $improvementHeaderRow + $i;
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']]
                    ]);
                    
                    // Left align improvement text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Signature section - match sample styling
                $signatureHeaderRow = $improvementHeaderRow + 9; // 5 improvement rows + 3 empty rows + 1
                $sheet->mergeCells("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}");
                $sheet->getStyle("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Signature names and titles - Arial font like sample
                $signatureNameRow = $signatureHeaderRow + 4;
                $signatureTitleRow = $signatureNameRow + 1;
                
                $sheet->getStyle("A{$signatureNameRow}:{$lastColumn}{$signatureNameRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                $sheet->getStyle("A{$signatureTitleRow}:{$lastColumn}{$signatureTitleRow}")->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Set row heights for better appearance - match sample
                $sheet->getRowDimension(1)->setRowHeight(30); // Title row height like sample (22.8pt)
                $sheet->getRowDimension(2)->setRowHeight(18); // Subtitle row height like sample (13.8pt)
                for ($row = $questionStartRow; $row <= $questionEndRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(16); // Standard question row height
                }
                
                // Set print settings
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                // Set print margins
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                
                // Set header and footer
                $sheet->getHeaderFooter()->setOddHeader('&C&"Arial,Bold"CUSTOMER SATISFACTION SURVEY');
                $sheet->getHeaderFooter()->setOddFooter('&L&D &T&R&P');
            }
        ];
    }
}
