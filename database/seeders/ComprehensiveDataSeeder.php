<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationProfile;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\GeneralLedger;
use App\Models\User;
use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan User tersedia
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Dummy',
                'email' => 'admin.dummy@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 1. Seed Organization Profile untuk 2024, 2025 dan 2026
        $years = [2024, 2025, 2026];
        foreach ($years as $year) {
            OrganizationProfile::updateOrCreate(
                ['fiscal_year' => $year],
                [
                    'organization_name' => 'PMI Kabupaten Nganjuk (Data Komprehensif ' . $year . ')',
                    'address' => $faker->address,
                    'chairperson' => $faker->name,
                    'headquarters_treasurer' => $faker->name,
                    'blood_donation_unit_treasurer' => $faker->name,
                    'financial_period_start' => Carbon::create($year, 1, 1)->toDateString(),
                    'financial_period_end' => Carbon::create($year, 12, 31)->toDateString(),
                ]
            );
        }

        $this->command->info('Organisasi Profil 2025 & 2026 berhasil disetup.');

        // 2. Seed Programs
        $programs = [];
        for ($i = 1; $i <= 5; $i++) {
            $programs[] = Program::firstOrCreate(
                ['name' => 'Program ' . $faker->words(2, true)],
                [
                    'description' => $faker->sentence,
                    'user_id' => $user->id,
                ]
            );
        }
        $this->command->info('Program Kerja berhasil disetup.');

        // 3. Ambil COA Utama (Neraca)
        $coaKas = ChartOfAccount::where('id', '12051-00')->first(); // Bank Mandiri
        $coaKasKecil = ChartOfAccount::where('id', '11011-00')->first(); // Kas - Markas (Pengganti Kas Kecil)
        $coaPiutang = ChartOfAccount::where('id', '13231-00')->first(); // Piutang Karyawan
        $coaAsetTetap = ChartOfAccount::where('id', '22021-00')->first(); // Inventaris Kantor
        
        $coaHutang = ChartOfAccount::where('id', '31011-00')->first(); // Hutang Bank
        $coaHutangLain = ChartOfAccount::where('id', '32131-00')->first(); // Hutang Pihak Ketiga
        
        $coaAsetNetto = ChartOfAccount::where('id', '41001-00')->first(); // Aset Netto Tidak Terikat
        $coaAsetNettoTerikat = ChartOfAccount::where('id', '43001-00')->first(); // Aset Netto Terikat
        
        // COA Tambahan untuk variasi Neraca
        $coaPersediaan = ChartOfAccount::where('id', '14021-00')->first(); // Persediaan BHP
        $coaUangMuka = ChartOfAccount::where('id', '15011-00')->first(); // Persekot Kegiatan
        $coaBiayaDimuka = ChartOfAccount::where('id', '16001-00')->first(); // Biaya Dibayar Di Muka
        $coaHutangPajak = ChartOfAccount::where('id', '33001-00')->first(); // Hutang Pajak
        $coaBiayaYMH = ChartOfAccount::where('id', '34011-00')->first(); // Accrued Gaji

        // Ambil SEMUA COA Pendapatan (5xxxx) dan Beban (6xxxx, 7xxxx)
        $pendapatanCoas = ChartOfAccount::where('id', 'like', '5%')->get();
        $bebanCoas = ChartOfAccount::where('id', 'like', '6%')->orWhere('id', 'like', '7%')->get();

        if (!$coaKas || $pendapatanCoas->isEmpty() || $bebanCoas->isEmpty()) {
            $this->command->error('Chart of Account tidak lengkap. Pastikan seeder COA sudah dijalankan secara penuh.');
            return;
        }

        DB::beginTransaction();

        try {
            $transCount = 1;

            // FUNGSI HELPER UNTUK BUAT TRANSAKSI + GL
            $buatTransaksi = function ($type, $debitCoaId, $creditCoaId, $amount, $desc, $date, $jenisEntri = null) use (&$transCount, $user, $programs, $faker) {
                $prefix = $type === 'PEMASUKAN' ? 'BKM' : ($type === 'PENGELUARAN' ? 'BKK' : 'BKJ');
                $docNumber = $prefix . 'UDD' . str_pad($transCount++, 4, '0', STR_PAD_LEFT);
                
                $transaction = Transaction::create([
                    'transaction_date' => $date,
                    'document_number' => $docNumber,
                    'transaction_type' => $type,
                    'program_id' => $faker->randomElement($programs)->id,
                    'user_id' => $user->id,
                    'reference' => 'REF-' . $faker->randomNumber(5, true),
                    'description' => $desc,
                ]);

                // Debit
                GeneralLedger::create([
                    'transaction_id' => $transaction->id,
                    'chart_of_account_id' => $debitCoaId,
                    'debit' => $amount,
                    'credit' => 0,
                    'note' => $jenisEntri ?: 'Debit: ' . $desc,
                ]);

                // Kredit
                GeneralLedger::create([
                    'transaction_id' => $transaction->id,
                    'chart_of_account_id' => $creditCoaId,
                    'debit' => 0,
                    'credit' => $amount,
                    'note' => $jenisEntri ?: 'Kredit: ' . $desc,
                ]);
            };

            // 1. Saldo Awal Tahun (1 Januari 2024)
            $this->command->info('Membuat Saldo Awal per 1 Januari 2024...');
            $buatTransaksi('PENYESUAIAN', $coaKas->id, $coaAsetNetto->id, 1000000000, 'Saldo Awal Bank Mandiri', '2024-01-01', 'BEGINNING_BALANCES');
            $buatTransaksi('PENYESUAIAN', $coaKasKecil->id, $coaAsetNetto->id, 20000000, 'Saldo Awal Kas Kecil', '2024-01-01', 'BEGINNING_BALANCES');
            $buatTransaksi('PENYESUAIAN', $coaAsetTetap->id, $coaAsetNetto->id, 150000000, 'Saldo Awal Inventaris Kantor', '2024-01-01', 'BEGINNING_BALANCES');
            
            // Generate data untuk 2024, 2025 dan 2026
            foreach ($years as $year) {
                $this->command->info("Membuat Transaksi Bulanan untuk tahun {$year}...");
                
                // Tiap tahun ada hutang/piutang/aset baru biar balance sheet dinamis
                $buatTransaksi('PENGELUARAN', $coaAsetTetap->id, $coaKas->id, $faker->randomFloat(0, 10000000, 50000000), 'Pembelian Aset Tetap Tambahan', "{$year}-03-10");
                $buatTransaksi('PEMASUKAN', $coaKas->id, $coaHutang->id, $faker->randomFloat(0, 50000000, 100000000), 'Penerimaan Hutang Bank', "{$year}-05-15");
                $buatTransaksi('PENGELUARAN', $coaPiutang->id, $coaKas->id, $faker->randomFloat(0, 2000000, 5000000), 'Pemberian Pinjaman Karyawan', "{$year}-07-20");
                
                if ($coaPersediaan) $buatTransaksi('PENGELUARAN', $coaPersediaan->id, $coaKas->id, 5000000, 'Pembelian Persediaan BHP', "{$year}-02-10");
                if ($coaUangMuka) $buatTransaksi('PENGELUARAN', $coaUangMuka->id, $coaKas->id, 2000000, 'Uang Muka Kegiatan', "{$year}-04-12");
                if ($coaBiayaDimuka) $buatTransaksi('PENGELUARAN', $coaBiayaDimuka->id, $coaKas->id, 12000000, 'Sewa Dibayar Di Muka', "{$year}-01-05");
                if ($coaHutangPajak) $buatTransaksi('PENYESUAIAN', '72081-00', $coaHutangPajak->id, 1500000, 'Pencatatan Hutang Pajak', "{$year}-12-31", 'ADJUSTMENT');
                if ($coaBiayaYMH) $buatTransaksi('PENYESUAIAN', '72001-00', $coaBiayaYMH->id, 8000000, 'Accrued Gaji Karyawan', "{$year}-12-31", 'ADJUSTMENT');

                for ($month = 1; $month <= 12; $month++) {
                    $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                    
                    // --- ITERASI SEMUA COA PENDAPATAN ---
                    // Pastikan setiap akun pendapatan punya minimal 1 transaksi tiap bulan
                    foreach ($pendapatanCoas as $pendapatan) {
                        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                        $amount = $faker->randomFloat(0, 500000, 25000000); // 500rb - 25jt
                        // Pemasukan: Debit Kas, Kredit Pendapatan
                        $buatTransaksi('PEMASUKAN', $coaKas->id, $pendapatan->id, $amount, 'Penerimaan ' . $pendapatan->account_name, "{$year}-{$monthStr}-{$day}");
                    }

                    // --- ITERASI SEMUA COA BEBAN ---
                    // Pastikan setiap akun beban punya minimal 1 transaksi tiap bulan
                    foreach ($bebanCoas as $beban) {
                        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                        $amount = $faker->randomFloat(0, 100000, 15000000); // 100rb - 15jt
                        
                        // Beberapa beban dibayar pakai kas kecil biar kas kecil terpakai
                        $sumberDana = (rand(1, 10) <= 2) ? $coaKasKecil->id : $coaKas->id; 

                        // Pengeluaran: Debit Beban, Kredit Kas/Kas Kecil
                        $buatTransaksi('PENGELUARAN', $beban->id, $sumberDana, $amount, 'Pembayaran ' . $beban->account_name, "{$year}-{$monthStr}-{$day}");
                    }
                    
                    // Cicil Hutang tiap bulan
                    $buatTransaksi('PENGELUARAN', $coaHutang->id, $coaKas->id, 5000000, 'Cicilan Pokok Hutang Bank', "{$year}-{$monthStr}-05");
                    // Cicil Piutang Karyawan
                    $buatTransaksi('PEMASUKAN', $coaKas->id, $coaPiutang->id, 500000, 'Pembayaran Cicilan Piutang Karyawan', "{$year}-{$monthStr}-25");
                }

                // Jurnal Penyesuaian Akhir Tahun (Penyusutan, Pemindahan ke Aset Terikat dll)
                $coaPenyusutan = ChartOfAccount::where('id', '71001-00')->first();
                $coaAkumPenyusutan = ChartOfAccount::where('id', '23021-00')->first() ?? $coaAsetTetap;
                if ($coaPenyusutan) {
                    $buatTransaksi('PENYESUAIAN', $coaPenyusutan->id, $coaAkumPenyusutan->id, 30000000, "Jurnal Penyesuaian Penyusutan Tahun {$year}", "{$year}-12-31", 'ADJUSTMENT');
                }
                
                // Penyesuaian ke Aset Netto Terikat
                $buatTransaksi('PENYESUAIAN', $coaAsetNetto->id, $coaAsetNettoTerikat->id, 25000000, "Pencadangan Aset Netto Terikat Tahun {$year}", "{$year}-12-31", 'ADJUSTMENT');
            }

            DB::commit();
            $this->command->info('Berhasil! Sekitar ' . $transCount . ' transaksi telah dibuat untuk tahun 2024, 2025 dan 2026.');
            $this->command->info('Seluruh COA Laba Rugi dan Neraca kini terisi penuh. Laporan akan terlihat sangat padat dan komplit.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat transaksi: ' . $e->getMessage());
        }
    }
}
