<?php

namespace App\Http\Controllers;

use App\Services\CashFlowService;
use App\Exports\CashFlowExport;
use App\Http\Requests\CashFlowRequest;
use Maatwebsite\Excel\Facades\Excel;

class CashFlowController extends Controller
{
    public function __construct(
        protected CashFlowService $service
    ) {}

    /**
     * Menampilkan halaman Laporan Arus Kas (Cash Flow Statement).
     */
    public function index(CashFlowRequest $request)
    {
        return view('pages.cash-flow', $this->service->getPageData($request->validated()));
    }

    /**
     * Mengekspor Laporan Arus Kas ke file Excel.
     */
    public function export(CashFlowRequest $request)
    {
        $validated = $request->validated();
        $pageData  = $this->service->getPageData($validated);
        $report    = $pageData['report'];

        $filename = "Laporan_Arus_Kas_{$report['year']}.xlsx";

        return Excel::download(new CashFlowExport($report), $filename);
    }
}
