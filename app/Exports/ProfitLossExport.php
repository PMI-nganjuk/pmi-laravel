<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProfitLossExport implements FromView, ShouldAutoSize, WithTitle
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
        return 'Laporan Laba Rugi';
    }
}
