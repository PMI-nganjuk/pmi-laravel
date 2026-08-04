<?php

namespace App\Http\Controllers;

use App\Services\BalanceSheetService;
use App\Exports\BalanceSheetExport;
use App\Http\Requests\BalanceSheetRequest;
use Maatwebsite\Excel\Facades\Excel;

class BalanceSheetController extends Controller
{
    public function __construct(
        protected BalanceSheetService $service
    ) {}

    /**
     * Menampilkan halaman laporan posisi keuangan (balance sheet).
     */
    public function index(BalanceSheetRequest $request)
    {
        return view('pages.balance-sheet', $this->service->getPageData($request->validated()));
    }

    /**
     * Mengekspor data balance sheet ke file Excel.
     */
    public function export(BalanceSheetRequest $request)
    {
        $validated  = $request->validated();
        $pageData   = $this->service->getPageData($validated);
        $report     = $pageData['report'];
        $year       = $report['year'];

        $filename = "Laporan_Posisi_Keuangan_{$year}.xlsx";

        return Excel::download(new BalanceSheetExport($report), $filename);
    }
}