<?php

namespace App\Services;

use App\Repositories\BalanceSheetRepository;
use App\Repositories\ProfitLossRepository;
use Illuminate\Support\Collection;

class BalanceSheetService
{
    // ── Aset Lancar ──────────────────────────────────────────────
    private const CAT_KAS                       = 'Kas';
    private const CAT_BANK                      = 'Bank';
    private const CAT_PIUTANG                   = 'Piutang Lain-lain';
    private const CAT_PERSEDIAAN                = 'Persediaan';
    private const CAT_UANG_MUKA                 = 'Uang Muka Kerja';
    private const CAT_BIAYA_DIBAYAR_MUKA        = 'Biaya Dibayar Di Muka';

    // ── Aset Tidak Lancar ─────────────────────────────────────────
    private const CAT_TANAH_BANGUNAN            = 'Tanah dan Bangunan';
    private const CAT_ASET_TETAP_LAINNYA        = 'Aset Tetap Lainnya';
    private const CAT_AKUMULASI_PENYUSUTAN      = 'Akumulasi Penyusutan';
    private const CAT_ASET_TIDAK_LANCAR_LAINNYA = 'Aset Tidak Lancar Lainnya';
    private const CAT_INVESTASI_ENTITAS_ANAK    = 'Investasi pada entitas anak';

    // ── Liabilitas Lancar ─────────────────────────────────────────
    private const CAT_HUTANG_LEMBAGA_LAIN       = 'Hutang Kepada Lembaga Lain';
    private const CAT_HUTANG_LAIN               = 'Hutang Lain-lain';
    private const CAT_HUTANG_PAJAK              = 'Hutang Pajak';
    private const CAT_BIAYA_MASIH_HARUS_DIBAYAR = 'Biaya Yang Masih Harus Dibayar';

    // ── Liabilitas Tidak Lancar ───────────────────────────────────
    private const CAT_HUTANG_JANGKA_PANJANG     = 'Hutang Usaha Jangka Panjang Inter Co';
    private const CAT_LIABILITAS_TIDAK_LANCAR   = 'Liabilitas Tidak Lancar Lainnya';

    // ── Aset Netto ────────────────────────────────────────────────
    private const CAT_AKUM_ASET_NETTO_TIDAK_TERIKAT = 'Akumulasi Aset Netto Tidak Terikat';
    private const CAT_AKUM_ASET_NETTO_TERIKAT        = 'Akumulasi Aset Netto Terikat';

    // Grouping map: category_name → section key
    private const ASET_LANCAR_CATEGORIES = [
        self::CAT_KAS,
        self::CAT_BANK,
        self::CAT_PIUTANG,
        self::CAT_PERSEDIAAN,
        self::CAT_UANG_MUKA,
        self::CAT_BIAYA_DIBAYAR_MUKA,
    ];

    private const ASET_TIDAK_LANCAR_CATEGORIES = [
        self::CAT_TANAH_BANGUNAN,
        self::CAT_ASET_TETAP_LAINNYA,
        self::CAT_AKUMULASI_PENYUSUTAN,
        self::CAT_ASET_TIDAK_LANCAR_LAINNYA,
        self::CAT_INVESTASI_ENTITAS_ANAK,
    ];

    private const LIABILITAS_LANCAR_CATEGORIES = [
        self::CAT_HUTANG_LEMBAGA_LAIN,
        self::CAT_HUTANG_LAIN,
        self::CAT_HUTANG_PAJAK,
        self::CAT_BIAYA_MASIH_HARUS_DIBAYAR,
    ];

    private const LIABILITAS_TIDAK_LANCAR_CATEGORIES = [
        self::CAT_HUTANG_JANGKA_PANJANG,
        self::CAT_LIABILITAS_TIDAK_LANCAR,
    ];

    private const ASET_NETTO_CATEGORIES = [
        self::CAT_AKUM_ASET_NETTO_TIDAK_TERIKAT,
        self::CAT_AKUM_ASET_NETTO_TERIKAT,
    ];

    public function __construct(
        protected BalanceSheetRepository $repository,
        protected ProfitLossRepository   $plRepository,
        protected ProfitLossService      $plService,
    ) {}

    public function generateReport(int $year): array
    {
        $currentBalances  = $this->repository->getAccountBalances($year);
        $previousBalances = $this->repository->getAccountBalances($year - 1);

        $current  = $this->indexBySubcategory($currentBalances);
        $previous = $this->indexBySubcategory($previousBalances);

        // ── Profit/Loss surplus untuk Pendapatan Netto Periode Berjalan ──
        $plCurrent  = $this->getSurplusFromPL($year);
        $plPrevious = $this->getSurplusFromPL($year - 1);

        // ── Build sections ──
        $asetLancar         = $this->buildRows(self::ASET_LANCAR_CATEGORIES, $current, $previous);
        $asetTidakLancar    = $this->buildRows(self::ASET_TIDAK_LANCAR_CATEGORIES, $current, $previous);
        $liabilitasLancar   = $this->buildRows(self::LIABILITAS_LANCAR_CATEGORIES, $current, $previous);
        $liabilitasTidakLancar = $this->buildRows(self::LIABILITAS_TIDAK_LANCAR_CATEGORIES, $current, $previous);
        $asetNettoBase      = $this->buildRows(self::ASET_NETTO_CATEGORIES, $current, $previous);

        // Append Pendapatan Netto rows (dari ProfitLoss)
        $asetNetto = array_merge($asetNettoBase, [
            [
                'name'     => 'Pendapatan Netto Tidak Terikat Periode Berjalan',
                'current'  => $plCurrent['tidak_terikat'],
                'previous' => $plPrevious['tidak_terikat'],
                'is_pl'    => true,
            ],
            [
                'name'     => 'Pendapatan Netto Terikat Periode Berjalan',
                'current'  => $plCurrent['terikat'],
                'previous' => $plPrevious['terikat'],
                'is_pl'    => true,
            ],
        ]);

        // ── Totals ──
        $totalAsetLancar      = $this->sumRows($asetLancar);
        $totalAsetTidakLancar = $this->sumRows($asetTidakLancar);
        $totalAset            = [
            'current'  => $totalAsetLancar['current'] + $totalAsetTidakLancar['current'],
            'previous' => $totalAsetLancar['previous'] + $totalAsetTidakLancar['previous'],
        ];

        $totalLiabilitasLancar      = $this->sumRows($liabilitasLancar);
        $totalLiabilitasTidakLancar = $this->sumRows($liabilitasTidakLancar);
        $totalLiabilitas            = [
            'current'  => $totalLiabilitasLancar['current'] + $totalLiabilitasTidakLancar['current'],
            'previous' => $totalLiabilitasLancar['previous'] + $totalLiabilitasTidakLancar['previous'],
        ];

        $totalAsetNetto = $this->sumRows($asetNetto);

        $totalLiabilitasDanAsetNetto = [
            'current'  => $totalLiabilitas['current'] + $totalAsetNetto['current'],
            'previous' => $totalLiabilitas['previous'] + $totalAsetNetto['previous'],
        ];

        return [
            'year'                          => $year,
            'previous_year'                 => $year - 1,

            'aset_lancar'                   => $asetLancar,
            'aset_tidak_lancar'             => $asetTidakLancar,
            'liabilitas_lancar'             => $liabilitasLancar,
            'liabilitas_tidak_lancar'       => $liabilitasTidakLancar,
            'aset_netto'                    => $asetNetto,

            'total_aset_lancar'             => $totalAsetLancar,
            'total_aset_tidak_lancar'       => $totalAsetTidakLancar,
            'total_aset'                    => $totalAset,
            'total_liabilitas_lancar'       => $totalLiabilitasLancar,
            'total_liabilitas_tidak_lancar' => $totalLiabilitasTidakLancar,
            'total_liabilitas'              => $totalLiabilitas,
            'total_aset_netto'              => $totalAsetNetto,
            'total_liabilitas_dan_aset_netto' => $totalLiabilitasDanAsetNetto,
        ];
    }

    public function getPageData(array $filters = []): array
    {
        $currentYear = $this->repository->getCurrentYear();
        $year        = isset($filters['year']) ? (int) $filters['year'] : $currentYear;

        return [
            'report'       => $this->generateReport($year),
            'current_year' => $currentYear,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────

    /**
     * Index GL balances keyed by subcategory_name → balance
     */
    private function indexBySubcategory(Collection $balances): array
    {
        $result = [];
        foreach ($balances as $row) {
            $key = $row->subcategory_name;
            if (!isset($result[$key])) {
                $result[$key] = 0;
            }
            $result[$key] += (float) $row->balance;
        }
        return $result;
    }

    /**
     * Build row array for a list of category names.
     */
    private function buildRows(array $categoryNames, array $current, array $previous): array
    {
        $rows = [];
        foreach ($categoryNames as $name) {
            $rows[] = [
                'name'     => $name,
                'current'  => $current[$name]  ?? 0,
                'previous' => $previous[$name] ?? 0,
                'is_pl'    => false,
            ];
        }
        return $rows;
    }

    /**
     * Sum current and previous from a rows array.
     */
    private function sumRows(array $rows): array
    {
        $current  = 0;
        $previous = 0;
        foreach ($rows as $row) {
            $current  += $row['current'];
            $previous += $row['previous'];
        }
        return ['current' => $current, 'previous' => $previous];
    }

    /**
     * Get surplus (tidak_terikat & terikat) from ProfitLoss for a given year.
     */
    private function getSurplusFromPL(int $year): array
    {
        try {
            $report = $this->plService->generateReport(
                "{$year}-01-01",
                "{$year}-12-31"
            );
            return [
                'tidak_terikat' => $report['surplus']['tidak_terikat'] ?? 0,
                'terikat'       => $report['surplus']['terikat']       ?? 0,
            ];
        } catch (\Throwable $e) {
            return ['tidak_terikat' => 0, 'terikat' => 0];
        }
    }
}