<?php

namespace App\Services;

use App\Repositories\AnalysisNotesRepository;
use App\Services\ProfitLossService;

class AnalysisNotesService
{
    // Subcategory names of accumulated net assets in the BS/GL
    private const SUBCATEGORY_AKUM_TIDAK_TERIKAT = 'Akumulasi Aset Netto Tidak Terikat';
    private const SUBCATEGORY_AKUM_TERIKAT        = 'Akumulasi Aset Netto Terikat';

    public function __construct(
        protected AnalysisNotesRepository $repository,
        protected ProfitLossService        $plService,
    ) {}

    /**
     * Generate the Laporan Perubahan Aset Netto (Analysis Notes) for a given year.
     *
     * The report follows ISAK 35 structure:
     *   Aset Netto Awal  = GL accumulated balances (prev year) + PL surplus (prev year)
     *   Perubahan        = PL surplus (current year)  ← reused from ProfitLossService
     *   Aset Netto Akhir = Awal + Perubahan
     *                    = must match BalanceSheet total_aset_netto['current']
     *
     * @param  int   $year  The fiscal year to report on (e.g., 2025).
     * @return array
     */
    public function generateReport(int $year): array
    {
        $prevYear = $year - 1;
        $prevPrevYear = $year - 2;

        // ── Accumulated Aset Netto GL balances ────────────────────────────────────
        $currentAkum  = $this->repository->getAsetNettoBalances($year);
        $previousAkum = $this->repository->getAsetNettoBalances($prevYear);
        $prevPrevAkum = $this->repository->getAsetNettoBalances($prevPrevYear);

        // ── PL surplus for current, previous, and prev-prev year ──────────────────────────────
        $plCurrent  = $this->plService->generateReport("{$year}-01-01", "{$year}-12-31");
        $plPrevious = $this->plService->generateReport("{$prevYear}-01-01", "{$prevYear}-12-31");
        $plPrevPrev = $this->plService->generateReport("{$prevPrevYear}-01-01", "{$prevPrevYear}-12-31");

        $surplusCurrent  = $plCurrent['surplus'];
        $surplusPrevious = $plPrevious['surplus'];
        $surplusPrevPrev = $plPrevPrev['surplus'];

        // ── Aset Netto Awal ────────────────────────────────────────────────
        // Current Year
        $akumTT_prev = $previousAkum[self::SUBCATEGORY_AKUM_TIDAK_TERIKAT] ?? 0.0;
        $akumT_prev  = $previousAkum[self::SUBCATEGORY_AKUM_TERIKAT]       ?? 0.0;

        $awalTT_curr = $akumTT_prev + $surplusPrevious['tidak_terikat'];
        $awalT_curr  = $akumT_prev  + $surplusPrevious['terikat'];

        // Previous Year
        $akumTT_prevPrev = $prevPrevAkum[self::SUBCATEGORY_AKUM_TIDAK_TERIKAT] ?? 0.0;
        $akumT_prevPrev  = $prevPrevAkum[self::SUBCATEGORY_AKUM_TERIKAT]       ?? 0.0;

        $awalTT_prev = $akumTT_prevPrev + $surplusPrevPrev['tidak_terikat'];
        $awalT_prev  = $akumT_prevPrev  + $surplusPrevPrev['terikat'];

        // ── Pendapatan Netto Periode Berjalan ────────────────────────────────
        $perubahanTT_curr = $surplusCurrent['tidak_terikat'];
        $perubahanT_curr  = $surplusCurrent['terikat'];

        $perubahanTT_prev = $surplusPrevious['tidak_terikat'];
        $perubahanT_prev  = $surplusPrevious['terikat'];

        // ── Aset Netto Akhir ───────────────────────────────────────────────
        $akhirTT_curr = $awalTT_curr + $perubahanTT_curr;
        $akhirT_curr  = $awalT_curr  + $perubahanT_curr;

        $akhirTT_prev = $awalTT_prev + $perubahanTT_prev;
        $akhirT_prev  = $awalT_prev  + $perubahanT_prev;

        // ── Cross-verification with Balance Sheet ────────────────────────────────
        $akumTT_current = $currentAkum[self::SUBCATEGORY_AKUM_TIDAK_TERIKAT] ?? 0.0;
        $akumT_current  = $currentAkum[self::SUBCATEGORY_AKUM_TERIKAT]       ?? 0.0;
        $bsAsetNettoTT  = $akumTT_current + $surplusCurrent['tidak_terikat'];
        $bsAsetNettoT   = $akumT_current  + $surplusCurrent['terikat'];
        $bsAsetNetto    = $bsAsetNettoTT  + $bsAsetNettoT;
        $akhir_curr_total = $akhirTT_curr + $akhirT_curr;

        return [
            'year'          => $year,
            'previous_year' => $prevYear,

            'tidak_terikat' => [
                'saldo_awal' => [
                    'current'  => $awalTT_curr,
                    'previous' => $awalTT_prev,
                ],
                'pendapatan_netto' => [
                    'current'  => $perubahanTT_curr,
                    'previous' => $perubahanTT_prev,
                ],
                'saldo_akhir' => [
                    'current'  => $akhirTT_curr,
                    'previous' => $akhirTT_prev,
                ],
            ],

            'terikat' => [
                'saldo_awal' => [
                    'current'  => $awalT_curr,
                    'previous' => $awalT_prev,
                ],
                'pendapatan_netto' => [
                    'current'  => $perubahanT_curr,
                    'previous' => $perubahanT_prev,
                ],
                'saldo_akhir' => [
                    'current'  => $akhirT_curr,
                    'previous' => $akhirT_prev,
                ],
            ],

            'total_aset_netto' => [
                'current'  => $akhir_curr_total,
                'previous' => $akhirTT_prev + $akhirT_prev,
            ],

            // Verification: should match BS total_aset_netto for current year
            'bs_verification' => [
                'total'   => $bsAsetNetto,
                'matches' => abs($akhir_curr_total - $bsAsetNetto) < 0.01,
            ],
        ];
    }

    /**
     * Get page data for the view (report + filter metadata).
     *
     * @param  array  $filters  Optional. May contain 'year' (int).
     * @return array
     */
    public function getPageData(array $filters = []): array
    {
        $currentYear = $this->repository->getCurrentYear();
        $year        = isset($filters['year']) ? (int) $filters['year'] : $currentYear;

        return [
            'report'       => $this->generateReport($year),
            'current_year' => $currentYear,
        ];
    }
}
