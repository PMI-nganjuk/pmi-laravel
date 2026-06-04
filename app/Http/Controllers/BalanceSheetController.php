<?php

namespace App\Http\Controllers;

use App\Services\BalanceSheetService;
use App\Exports\BalanceSheetExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheetController extends Controller
{
    public function __construct(
        protected BalanceSheetService $service
    ) {}

    /**
     * Menampilkan halaman laporan posisi keuangan (balance sheet).
     */
    public function index(Request $request)
    {
        $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        return view('pages.balance-sheet', $this->service->getPageData($request->only('year')));
    }

    /**
     * Mengekspor data balance sheet ke file Excel.
     */
    public function export(Request $request)
    {
        $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $filters    = $request->only('year');
        $pageData   = $this->service->getPageData($filters);
        $report     = $pageData['report'];
        $year       = $report['year'];

        $filename = "Laporan_Posisi_Keuangan_{$year}.xlsx";

        return Excel::download(new BalanceSheetExport($report), $filename);
    }
}