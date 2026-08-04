<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashFlowExport implements FromView, WithTitle, WithColumnWidths, WithStyles, WithEvents
{
    public function __construct(
        protected array $reportData
    ) {}

    public function view(): View
    {
        return view('exports.cash-flow', [
            'report' => $this->reportData,
        ]);
    }

    public function title(): string
    {
        return 'Laporan Arus Kas';
    }

    /**
     * Lebar kolom:
     * A = margin kiri kosong
     * B = kolom Uraian (lebar)
     * C = kolom Tahun Berjalan
     */
    public function columnWidths(): array
    {
        return [
            'A' => 3,
            'B' => 4,
            'C' => 58,  
            'D' => 22,
            'E' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A:E' => [
                'font' => ['name' => 'Calibri', 'size' => 10],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getPageSetup()->setPrintArea('A1:E' . $sheet->getHighestRow());
                $sheet->getPageSetup()->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
                );
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setRight(0.5);

                $sheet->setShowGridlines(false);
            },
        ];
    }
}
