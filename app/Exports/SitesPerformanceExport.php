<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SitesPerformanceExport implements FromView, WithStyles, WithColumnWidths
{
    protected $siteAnalytics;
    protected $npsData;

    public function __construct($siteAnalytics, $npsData)
    {
        $this->siteAnalytics = $siteAnalytics;
        $this->npsData = $npsData;
    }

    public function view(): View
    {
        return view('admin.surveys.exports.sites_performance', [
            'siteAnalytics' => $this->siteAnalytics,
            'npsData' => $this->npsData
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
        return [
            'A' => 25, 'B' => 15, 'C' => 12, 'D' => 12,
            'E' => 15, 'F' => 12, 'G' => 12, 'H' => 12, 'I' => 12
        ];
    }
}
