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
            // Title row - Blue background with white text
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Subtitle row - Light blue background
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '5B9BD5']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Company header row - Light gray background
            4 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F2F2F2']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Site headers row - Gray background
            6 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D9D9D9']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
            
            // Respondents row - Light blue background
            7 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B4C6E7']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = ['A' => 50]; // Question/label column wider
        
        // Site columns - consistent width
        $siteCount = count($this->siteAnalytics);
        for ($i = 0; $i < $siteCount; $i++) {
            $widths[chr(66 + $i)] = 20; // B, C, D, etc. - wider for better readability
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
                
                // Style question rows with borders and alternating colors
                for ($row = $questionStartRow; $row <= $questionEndRow; $row++) {
                    $fillColor = ($row % 2 == 0) ? 'F8F9FA' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                        ],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10]
                    ]);
                    
                    // Left align question text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Rating scale row styling
                $ratingRow = $questionEndRow + 2;
                $sheet->mergeCells("A{$ratingRow}:{$lastColumn}{$ratingRow}");
                $sheet->getStyle("A{$ratingRow}:{$lastColumn}{$ratingRow}")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF2CC']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating section header
                $overallHeaderRow = $ratingRow + 3;
                $sheet->mergeCells("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}");
                $sheet->getStyle("A{$overallHeaderRow}:{$lastColumn}{$overallHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Overall rating values row
                $overallValueRow = $overallHeaderRow + 1;
                $sheet->getStyle("A{$overallValueRow}:{$lastColumn}{$overallValueRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E7E6E6']]
                ]);
                
                // Left align overall rating label
                $sheet->getStyle("A{$overallValueRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // QMS Target status row with color coding
                $qmsRow = $overallValueRow + 1;
                $sheet->getStyle("A{$qmsRow}:{$lastColumn}{$qmsRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code QMS status
                for ($i = 0; $i < $siteCount; $i++) {
                    $col = chr(66 + $i);
                    $status = $this->siteAnalytics[$i]['qms_target_status'] ?? 'MISS';
                    $color = $status === 'HIT' ? '00B050' : 'FF0000'; // Green for HIT, Red for MISS
                    $sheet->getStyle("{$col}{$qmsRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $color]],
                        'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
                    ]);
                }
                
                // NPS Section header
                $npsHeaderRow = $qmsRow + 3;
                $sheet->mergeCells("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}");
                $sheet->getStyle("A{$npsHeaderRow}:{$lastColumn}{$npsHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '70AD47']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // NPS scores row
                $npsScoreRow = $npsHeaderRow + 1;
                $sheet->getStyle("A{$npsScoreRow}:{$lastColumn}{$npsScoreRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E7E6E6']]
                ]);
                
                // Left align NPS label
                $sheet->getStyle("A{$npsScoreRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // NPS status row with color coding
                $npsStatusRow = $npsScoreRow + 1;
                $sheet->getStyle("A{$npsStatusRow}:{$lastColumn}{$npsStatusRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Color code NPS status
                for ($i = 0; $i < count($this->npsData); $i++) {
                    $col = chr(66 + $i);
                    $status = $this->npsData[$i]['status'] ?? 'MISS';
                    $color = $status === 'HIT' ? '00B050' : ($status === 'Borderline' ? 'FFA500' : 'FF0000');
                    $sheet->getStyle("{$col}{$npsStatusRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $color]],
                        'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
                    ]);
                }
                
                // Feedback section header
                $feedbackHeaderRow = $npsStatusRow + 3;
                $sheet->mergeCells("A{$feedbackHeaderRow}:{$lastColumn}{$feedbackHeaderRow}");
                $sheet->getStyle("A{$feedbackHeaderRow}:{$lastColumn}{$feedbackHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'C5504B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Positive feedback header row
                $positiveFeedbackRow = $feedbackHeaderRow + 2;
                $sheet->getStyle("A{$positiveFeedbackRow}:{$lastColumn}{$positiveFeedbackRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '00B050']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Left align positive feedback label
                $sheet->getStyle("A{$positiveFeedbackRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Style positive feedback data rows (5 rows)
                for ($i = 1; $i <= 5; $i++) {
                    $row = $positiveFeedbackRow + $i;
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10]
                    ]);
                    
                    // Left align feedback text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Areas for improvement header row
                $improvementHeaderRow = $positiveFeedbackRow + 8; // 5 feedback rows + 2 empty rows + 1
                $sheet->getStyle("A{$improvementHeaderRow}:{$lastColumn}{$improvementHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FF9900']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                // Left align improvement label
                $sheet->getStyle("A{$improvementHeaderRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Style improvement data rows (5 rows)
                for ($i = 1; $i <= 5; $i++) {
                    $row = $improvementHeaderRow + $i;
                    $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font' => ['size' => 10]
                    ]);
                    
                    // Left align improvement text in column A
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                }
                
                // Signature section
                $signatureHeaderRow = $improvementHeaderRow + 9; // 5 improvement rows + 3 empty rows + 1
                $sheet->mergeCells("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}");
                $sheet->getStyle("A{$signatureHeaderRow}:{$lastColumn}{$signatureHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Signature names and titles
                $signatureNameRow = $signatureHeaderRow + 4;
                $signatureTitleRow = $signatureNameRow + 1;
                
                $sheet->getStyle("A{$signatureNameRow}:{$lastColumn}{$signatureNameRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
                ]);
                
                $sheet->getStyle("A{$signatureTitleRow}:{$lastColumn}{$signatureTitleRow}")->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Set row heights for better appearance
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                for ($row = $questionStartRow; $row <= $questionEndRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(18);
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
