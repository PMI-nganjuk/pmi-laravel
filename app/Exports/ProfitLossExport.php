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

class ProfitLossExport implements FromView, WithTitle, WithColumnWidths, WithStyles, WithEvents
{
    public function __construct(
        protected array $reportData
    ) {}

    public function view(): View
    {
        return view('exports.profit-loss', [
            'report' => $this->reportData,
        ]);
    }

    public function title(): string
    {
        return 'Laporan Aktivitas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 3,   // Spacer
            'B' => 4,   // Nomor
            'C' => 45,  // Uraian
            'D' => 22,  // Tidak Terikat
            'E' => 22,  // Terikat
            'F' => 22,  // Total
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A:F' => [
                'font' => ['name' => 'Calibri', 'size' => 10],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getPageSetup()->setPrintArea('A1:F' . $sheet->getHighestRow());
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
