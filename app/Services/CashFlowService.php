<?php

namespace App\Services;

use App\Repositories\CashFlowRepository;
use App\Services\ProfitLossService;

class CashFlowService
{
    // ── COA IDs for specific revenue accounts ─────────────────────────────────
    private const COA_JASA_DARAH          = '52001-00'; // Penghasilan penggantian Pengolahan darah
    private const COA_PENDAPATAN_BUNGA    = '52003-00'; // Placeholder for Pendapatan Bunga
    private const COA_PENGHASILAN_LAINNYA = '52011-00'; // Penghasilan Lainnya
    private const COA_PENYUSUTAN          = '71001-00'; // Beban Penyusutan (non-cash, excluded from payments)

    // ── BS subcategory names ───────────────────────────────────────────────────
    private const SUB_KAS           = 'Kas';
    private const SUB_BANK          = 'Bank';
    private const SUB_PIUTANG       = 'Piutang Lain-lain';
    private const SUB_PERSEDIAAN    = 'Persediaan';
    private const SUB_ASET_TETAP    = 'Aset Tetap Lainnya';
    private const SUB_ASET_TAK_BERWUJUD = 'Aset Tak Berwujud';
    private const SUB_INVESTASI_ANAK    = 'Investasi pada Entitas Anak';
    private const SUB_HUTANG_LEMBAGA = 'Hutang Kepada Lembaga Lain';
    private const SUB_HUTANG_LAIN   = 'Hutang Lain-lain';
    private const SUB_HUTANG_PAJAK  = 'Hutang Pajak';
    private const SUB_BIAYA_MASIH   = 'Biaya Yang Masih Harus Dibayar';

    // ── PL subcategory name for employee expenses ─────────────────────────────
    private const PL_SUBCATEGORY_BEBAN_PEGAWAI = 'Beban Pegawai';
    private const PL_SUBCATEGORY_SUMBANGAN     = 'Sumbangan';

    public function __construct(
        protected CashFlowRepository $repository,
        protected ProfitLossService  $plService,
    ) {}

    public function generateReport(int $year): array
    {
        return [
            'year'          => $year,
            'previous_year' => $year - 1,
            'current'       => $this->calculateCashFlow($year),
            'previous'      => $this->calculateCashFlow($year - 1),
        ];
    }

    private function calculateCashFlow(int $year): array
    {
        $startDate = "{$year}-01-01";
        $endDate   = "{$year}-12-31";

        // ── PL report for the period ──────────────────────────────────────────
        $plReport = $this->plService->generateReport($startDate, $endDate);

        // ── BS subcategory balances for current & previous year ───────────────
        $bsCurrent  = $this->repository->getBSSubcategoryBalances($year);
        $bsPrevious = $this->repository->getBSSubcategoryBalances($year - 1);

        // ═════════════════════════════════════════════════════════════════════
        // AKTIVITAS OPERASI
        // ═════════════════════════════════════════════════════════════════════

        // 1. Penerimaan dari penyumbang
        $pendapatanSumbangan    = $this->getSumbanganTotal($plReport);
        $piutangAwal            = $bsPrevious[self::SUB_PIUTANG] ?? 0.0;
        $piutangAkhir           = $bsCurrent[self::SUB_PIUTANG]  ?? 0.0;
        $deltaPiutang           = $piutangAwal - $piutangAkhir;
        $penerimaan_penyumbang  = $pendapatanSumbangan + $deltaPiutang;

        // 2. Penerimaan Bunga
        $penerimaan_bunga = $this->repository->getGLNetBalanceByCoaIds(
            [self::COA_PENDAPATAN_BUNGA],
            $startDate,
            $endDate
        );

        // 3. Penerimaan Lain-lain (including Jasa Darah)
        $penerimaan_jasa_darah = $this->repository->getGLNetBalanceByCoaIds(
            [self::COA_JASA_DARAH],
            $startDate,
            $endDate
        );
        $penerimaan_lain_lain_coas = $this->repository->getGLNetBalanceByCoaIds(
            [self::COA_PENGHASILAN_LAINNYA],
            $startDate,
            $endDate
        );
        $penerimaan_lain_lain = $penerimaan_jasa_darah + $penerimaan_lain_lain_coas;

        // 4. Pembayaran kepada pemasok dan penerima sumbangan
        $totalBebanProgram    = (float) $plReport['total_beban_program']['total'];
        $bebanMuNonPegawai    = $this->getBebanMuNonPegawai($plReport);
        $bebanPenyusutan      = $this->repository->getGLNetBalanceByCoaIds(
            [self::COA_PENYUSUTAN],
            $startDate,
            $endDate
        );
        // Total non-cash-adjusted beban for supplier-related expenses
        $totalBebanNonPegawai = $totalBebanProgram + $bebanMuNonPegawai - $bebanPenyusutan;

        $persediaanAwal  = $bsPrevious[self::SUB_PERSEDIAAN] ?? 0.0;
        $persediaanAkhir = $bsCurrent[self::SUB_PERSEDIAAN]  ?? 0.0;
        $deltaPersediaan = $persediaanAkhir - $persediaanAwal;

        $hutangAwal  = $this->getHutangUsahaTotal($bsPrevious);
        $hutangAkhir = $this->getHutangUsahaTotal($bsCurrent);
        $deltaHutang = $hutangAkhir - $hutangAwal;

        $pembayaran_pemasok = -$totalBebanNonPegawai - $deltaPersediaan + $deltaHutang;

        // 5. Pembayaran kepada pegawai dan sukarelawan
        $bebanPegawai        = $this->getBebanPegawaiTotal($plReport);
        $pembayaran_karyawan = -$bebanPegawai;

        // Kas Neto dari Aktivitas Operasi
        $kas_neto_operasi = $penerimaan_penyumbang
                          + $penerimaan_bunga
                          + $penerimaan_lain_lain
                          + $pembayaran_pemasok
                          + $pembayaran_karyawan;

        // ═════════════════════════════════════════════════════════════════════
        // AKTIVITAS INVESTASI
        // ═════════════════════════════════════════════════════════════════════

        // 1. Pembelian aset Tetap
        $asetTetapAwal    = $bsPrevious[self::SUB_ASET_TETAP] ?? 0.0;
        $asetTetapAkhir   = $bsCurrent[self::SUB_ASET_TETAP]  ?? 0.0;
        $pembelian_aset_tetap = -($asetTetapAkhir - $asetTetapAwal);

        // 2. Pembelian aset tak berwujud
        $asetTakBerwujudAwal  = $bsPrevious[self::SUB_ASET_TAK_BERWUJUD] ?? 0.0;
        $asetTakBerwujudAkhir = $bsCurrent[self::SUB_ASET_TAK_BERWUJUD]  ?? 0.0;
        $pembelian_aset_tak_berwujud = -($asetTakBerwujudAkhir - $asetTakBerwujudAwal);

        // 3. Investasi pada entitas anak
        $investasiAwal  = $bsPrevious[self::SUB_INVESTASI_ANAK] ?? 0.0;
        $investasiAkhir = $bsCurrent[self::SUB_INVESTASI_ANAK]  ?? 0.0;
        $investasi_entitas_anak = -($investasiAkhir - $investasiAwal);

        $kas_neto_investasi = $pembelian_aset_tetap + $pembelian_aset_tak_berwujud + $investasi_entitas_anak;

        // ═════════════════════════════════════════════════════════════════════
        // REKONSILIASI SALDO KAS
        // ═════════════════════════════════════════════════════════════════════
        $kenaikan_neto_kas = $kas_neto_operasi + $kas_neto_investasi;

        $saldo_kas_awal  = $this->getKasDanBank($bsPrevious);
        $saldo_kas_akhir = $saldo_kas_awal + $kenaikan_neto_kas;

        // Cross-verification: should match BS (Kas + Bank) for current year
        $bs_kas_akhir = $this->getKasDanBank($bsCurrent);
        $selisih_kas  = $bs_kas_akhir - $saldo_kas_akhir;

        return [
            // ── Aktivitas Operasi ────────────────────────────────────────────
            'penerimaan_penyumbang' => $penerimaan_penyumbang,
            'penerimaan_bunga'      => $penerimaan_bunga,
            'penerimaan_lain_lain'  => $penerimaan_lain_lain,
            'pembayaran_pemasok'    => $pembayaran_pemasok,
            'pembayaran_karyawan'   => $pembayaran_karyawan,
            'kas_neto_operasi'      => $kas_neto_operasi,

            // ── Aktivitas Investasi ──────────────────────────────────────────
            'pembelian_aset_tetap'         => $pembelian_aset_tetap,
            'pembelian_aset_tak_berwujud'  => $pembelian_aset_tak_berwujud,
            'investasi_entitas_anak'       => $investasi_entitas_anak,
            'kas_neto_investasi'           => $kas_neto_investasi,

            // ── Rekonsiliasi ─────────────────────────────────────────────────
            'kenaikan_neto_kas'     => $kenaikan_neto_kas,
            'saldo_kas_awal'        => $saldo_kas_awal,
            'saldo_kas_akhir'       => $saldo_kas_akhir,

            // ── Cross-verification ───────────────────────────────────────────
            'bs_kas_akhir'          => $bs_kas_akhir,
            'selisih_kas'           => $selisih_kas,
            'rekonsiliasi_matches'  => abs($selisih_kas) < 0.01,
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

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Extract total Sumbangan revenue from the PL report array.
     */
    private function getSumbanganTotal(array $plReport): float
    {
        foreach ($plReport['pendapatan'] as $section) {
            if ($section['name'] === self::PL_SUBCATEGORY_SUMBANGAN) {
                return (float) $section['subtotal']['total'];
            }
        }
        return 0.0;
    }

    /**
     * Extract total Beban Manajemen Umum EXCLUDING the "Beban Pegawai" subcategory.
     * Used for computing supplier payments (cash paid to vendors/suppliers).
     */
    private function getBebanMuNonPegawai(array $plReport): float
    {
        $total = 0.0;
        foreach ($plReport['beban_manajemen_umum'] as $section) {
            if ($section['name'] !== self::PL_SUBCATEGORY_BEBAN_PEGAWAI) {
                $total += (float) $section['subtotal']['total'];
            }
        }
        return $total;
    }

    /**
     * Extract total Beban Pegawai from the PL report array.
     * Maps to all COA accounts with subcategory "Beban Pegawai" (72xxx series).
     */
    private function getBebanPegawaiTotal(array $plReport): float
    {
        foreach ($plReport['beban_manajemen_umum'] as $section) {
            if ($section['name'] === self::PL_SUBCATEGORY_BEBAN_PEGAWAI) {
                return (float) $section['subtotal']['total'];
            }
        }
        return 0.0;
    }

    /**
     * Compute total Hutang Usaha (current liabilities) from indexed BS balances.
     */
    private function getHutangUsahaTotal(array $bsBalances): float
    {
        return ($bsBalances[self::SUB_HUTANG_LEMBAGA] ?? 0.0)
             + ($bsBalances[self::SUB_HUTANG_LAIN]    ?? 0.0)
             + ($bsBalances[self::SUB_HUTANG_PAJAK]   ?? 0.0)
             + ($bsBalances[self::SUB_BIAYA_MASIH]    ?? 0.0);
    }

    /**
     * Compute total Kas dan Bank (cash equivalents) from indexed BS balances.
     */
    private function getKasDanBank(array $bsBalances): float
    {
        return ($bsBalances[self::SUB_KAS]  ?? 0.0)
             + ($bsBalances[self::SUB_BANK] ?? 0.0);
    }
}
