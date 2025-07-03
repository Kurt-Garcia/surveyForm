<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NPSAnalysisExport implements FromView, WithStyles, WithColumnWidths
{
    protected $npsData;

    public function __construct($npsData)
    {
        $this->npsData = $npsData;
    }

    public function view(): View
    {
        return view('admin.surveys.exports.nps_analysis', [
            'npsData' => $this->npsData
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            10 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, 'B' => 15, 'C' => 15, 'D' => 12, 'E' => 12,
            'F' => 12, 'G' => 12, 'H' => 12, 'I' => 12, 'J' => 15
        ];
    }
}
