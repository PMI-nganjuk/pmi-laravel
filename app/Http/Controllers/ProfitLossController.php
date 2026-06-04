<?php

namespace App\Http\Controllers;

use App\Services\ProfitLossService;
use App\Exports\ProfitLossExport;
use App\Http\Requests\ProfitLossReportRequest;
use Maatwebsite\Excel\Facades\Excel;

class ProfitLossController extends Controller
{
    public function __construct(
        protected ProfitLossService $service
    ) {}

    /**
     * Menampilkan halaman laporan laba rugi dengan data terfilter.
     */
    public function index(ProfitLossReportRequest $request)
    {
        return view('pages.profit-loss', $this->service->getPageData($request->validated()));
    }

    /**
     * Mengekspor data laporan laba rugi ke file Excel.
     */
    public function export(ProfitLossReportRequest $request)
    {
        $validated = $request->validated();
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        $reportData = $this->service->generateReport($startDate, $endDate);

        $filename = 'Laporan_Laba_Rugi_' . ($startDate ?? $reportData['period']['start']) . '_sd_' . ($endDate ?? $reportData['period']['end']) . '.xlsx';

        return Excel::download(new ProfitLossExport($reportData), $filename);
    }
}
