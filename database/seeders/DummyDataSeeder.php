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

class DummyDataSeeder extends Seeder
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

        // 1. Seed Organization Profile
        OrganizationProfile::create([
            'organization_name' => 'PMI Kabupaten Nganjuk (Dummy Lengkap)',
            'address' => $faker->address,
            'chairperson' => $faker->name,
            'headquarters_treasurer' => $faker->name,
            'blood_donation_unit_treasurer' => $faker->name,
            'financial_period_start' => Carbon::create(2026, 1, 1)->toDateString(),
            'financial_period_end' => Carbon::create(2026, 12, 31)->toDateString(),
            'fiscal_year' => 2026,
        ]);

        $this->command->info('Organisasi Profil berhasil dibuat.');

        // 2. Seed Programs
        $programs = [];
        for ($i = 1; $i <= 5; $i++) {
            $programs[] = Program::create([
                'name' => 'Program ' . $faker->words(2, true),
                'description' => $faker->sentence,
                'user_id' => $user->id,
            ]);
        }
        $this->command->info('5 Program berhasil dibuat.');

        // Pastikan COA terisi dengan benar (Pastikan db:seed --class=ChartOfAccountSeeder dijalankan)
        $coaKas = ChartOfAccount::where('id', '12051-00')->first(); // Bank Mandiri
        $coaPendapatan = ChartOfAccount::where('id', '51001-00')->first(); // Sumbangan Institusi
        $coaBebanProgram = ChartOfAccount::where('id', '61001-00')->first(); // Bantuan Dana
        $coaBebanManajemen = ChartOfAccount::where('id', '72001-00')->first(); // Gaji
        $coaAsetTetap = ChartOfAccount::where('id', '22021-00')->first(); // Inventaris Kantor
        $coaAsetNetto = ChartOfAccount::where('id', '41001-00')->first(); // Aset Netto Tidak Terikat
        $coaHutang = ChartOfAccount::where('id', '31011-00')->first(); // Hutang Bank

        if (!$coaKas || !$coaPendapatan || !$coaAsetNetto) {
            $this->command->error('Chart of Account tidak lengkap. Jalankan dulu seeder COA: php artisan db:seed --class=ChartOfAccountSeeder');
            return;
        }

        DB::beginTransaction();

        try {
            $transCount = 1;

            // FUNGSI HELPER UNTUK BUAT TRANSAKSI + GL
            $buatTransaksi = function ($type, $debitCoaId, $creditCoaId, $amount, $desc, $date) use (&$transCount, $user, $programs, $faker) {
                $prefix = $type === 'PEMASUKAN' ? 'BKM' : ($type === 'PENGELUARAN' ? 'BKK' : 'BKJ');
                $docNumber = $prefix . 'UDD' . str_pad($transCount++, 3, '0', STR_PAD_LEFT);
                
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
                    'note' => 'Debit: ' . $desc,
                ]);

                // Kredit
                GeneralLedger::create([
                    'transaction_id' => $transaction->id,
                    'chart_of_account_id' => $creditCoaId,
                    'debit' => 0,
                    'credit' => $amount,
                    'note' => 'Kredit: ' . $desc,
                ]);
            };

            // 1. Saldo Awal Aset Netto (Penyesuaian: Debit Kas, Kredit Aset Netto)
            // Ini akan mengisi Posisi Keuangan (Kas bertambah, Aset Netto bertambah)
            $buatTransaksi('PENYESUAIAN', $coaKas->id, $coaAsetNetto->id, 500000000, 'Saldo Awal Aset Netto', Carbon::create(2026, 1, 1)->toDateString());

            // 2. Penerimaan Sumbangan (Pemasukan: Debit Kas, Kredit Pendapatan)
            // Ini mengisi Laba Rugi (Pendapatan bertambah) & Arus Kas (Arus Masuk Operasi)
            for ($i = 0; $i < 10; $i++) {
                $buatTransaksi('PEMASUKAN', $coaKas->id, $coaPendapatan->id, $faker->randomFloat(0, 5000000, 50000000), 'Penerimaan Sumbangan ' . $faker->company, $faker->dateTimeBetween('2026-01-02', 'now')->format('Y-m-d'));
            }

            // 3. Pembayaran Beban Program (Pengeluaran: Debit Beban Program, Kredit Kas)
            // Ini mengisi Laba Rugi (Beban bertambah) & Arus Kas (Arus Keluar Operasi)
            for ($i = 0; $i < 10; $i++) {
                $buatTransaksi('PENGELUARAN', $coaBebanProgram->id, $coaKas->id, $faker->randomFloat(0, 1000000, 10000000), 'Penyaluran Bantuan Program', $faker->dateTimeBetween('2026-01-02', 'now')->format('Y-m-d'));
            }

            // 4. Pembayaran Beban Manajemen (Pengeluaran: Debit Beban Manajemen, Kredit Kas)
            for ($i = 0; $i < 5; $i++) {
                $buatTransaksi('PENGELUARAN', $coaBebanManajemen->id, $coaKas->id, $faker->randomFloat(0, 3000000, 15000000), 'Pembayaran Gaji Pegawai', $faker->dateTimeBetween('2026-01-02', 'now')->format('Y-m-d'));
            }

            // 5. Pembelian Aset Tetap (Pengeluaran: Debit Aset Tetap, Kredit Kas)
            // Ini mengisi Posisi Keuangan (Aset Tetap bertambah) & Arus Kas (Arus Keluar Investasi)
            $buatTransaksi('PENGELUARAN', $coaAsetTetap->id, $coaKas->id, 25000000, 'Pembelian Inventaris Kantor Baru', $faker->dateTimeBetween('2026-01-02', 'now')->format('Y-m-d'));

            // 6. Pencairan Hutang Bank (Pemasukan: Debit Kas, Kredit Hutang)
            // Ini mengisi Posisi Keuangan (Hutang bertambah) & Arus Kas (Arus Masuk Pendanaan/Lainnya)
            $buatTransaksi('PEMASUKAN', $coaKas->id, $coaHutang->id, 100000000, 'Pencairan Pinjaman Bank', $faker->dateTimeBetween('2026-01-02', 'now')->format('Y-m-d'));

            DB::commit();
            $this->command->info('Data transaksi spesifik berhasil digenerate! Laporan Keuangan (Neraca, Laba Rugi, Arus Kas, Perubahan Aset Netto) kini terisi penuh.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal membuat transaksi: ' . $e->getMessage());
        }
    }
}
