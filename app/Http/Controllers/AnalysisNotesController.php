<?php

namespace App\Http\Controllers;

use App\Services\AnalysisNotesService;
use App\Exports\AnalysisNotesExport;
use App\Http\Requests\AnalysisNotesRequest;
use Maatwebsite\Excel\Facades\Excel;

class AnalysisNotesController extends Controller
{
    public function __construct(
        protected AnalysisNotesService $service
    ) {}

    /**
     * Menampilkan halaman Laporan Perubahan Aset Netto (Analysis Notes).
     */
    public function index(AnalysisNotesRequest $request)
    {
        return view('pages.analysis-notes', $this->service->getPageData($request->validated()));
    }

    /**
     * Mengekspor Laporan Perubahan Aset Netto ke file Excel.
     */
    public function export(AnalysisNotesRequest $request)
    {
        $validated = $request->validated();
        $pageData  = $this->service->getPageData($validated);
        $report    = $pageData['report'];

        $filename = "Laporan_Perubahan_Aset_Netto_{$report['year']}.xlsx";

        return Excel::download(new AnalysisNotesExport($report), $filename);
    }
}
