<?php

namespace Database\Seeders;

use App\Models\AccountSubcategory;
use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Seed Chart of Accounts untuk Neraca dan Laba Rugi.
     */
    public function run(): void
    {
        // Ambil mapping ID subkategori akun berdasarkan nama.
        $subcategoryMap = AccountSubcategory::pluck('id', 'name')->toArray();

        $accounts = [
            // ================== NERACA (REPORT TYPE 1) ==================
            // 1. Aset Lancar
            ['id' => '11001-00', 'subcategory' => 'Kas', 'account_name' => 'Kas Kecil', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '11002-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Mandiri (Operasional)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12001-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Karyawan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13001-00', 'subcategory' => 'Aset Tetap Lainnya', 'account_name' => 'Inventaris Kantor', 'normal_balance' => 'D', 'report_type' => 1],

            // 2. Liabilitas
            ['id' => '21001-00', 'subcategory' => 'Hutang Kepada Lembaga Lain', 'account_name' => 'Hutang Bank', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '22001-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang Pihak Ketiga', 'normal_balance' => 'C', 'report_type' => 1],

            // 3. Aset Netto
            ['id' => '31001-00', 'subcategory' => 'Akumulasi Aset Netto Tidak Terikat', 'account_name' => 'Aset Netto Tidak Terikat', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32001-00', 'subcategory' => 'Akumulasi Aset Netto Terikat', 'account_name' => 'Aset Netto Terikat', 'normal_balance' => 'C', 'report_type' => 1],

            // ================== LABA RUGI (REPORT TYPE 2) ==================
            // 5. Pendapatan
            ['id' => '51001-00', 'subcategory' => 'Sumbangan', 'account_name' => 'Sumbangan Institusi',        'normal_balance' => 'C', 'report_type' => 2],
            ['id' => '51011-00', 'subcategory' => 'Sumbangan', 'account_name' => 'Sumbangan Individual',        'normal_balance' => 'C', 'report_type' => 2],
            ['id' => '51021-00', 'subcategory' => 'Sumbangan', 'account_name' => 'Pengumpulan Dana Internal',   'normal_balance' => 'C', 'report_type' => 2],
            ['id' => '52001-00', 'subcategory' => 'Non Sumbangan', 'account_name' => 'Penghasilan penggantian Pengolahan darah', 'normal_balance' => 'C', 'report_type' => 2],
            ['id' => '52011-00', 'subcategory' => 'Non Sumbangan', 'account_name' => 'Penghasilan Lainnya',     'normal_balance' => 'C', 'report_type' => 2],

            // 6. Beban Program
            ['id' => '61001-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Dana',                 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61011-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Non-Food',            'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61021-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Distribusi Bantuan',           'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61031-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Peralatan',            'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61041-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Beban Kebencanaan Lainnya',    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61051-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Obat dan Alkes',        'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61061-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Food',                  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61071-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Kendaraan',             'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61081-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Sarana Kesehatan',     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '61091-00', 'subcategory' => 'Beban Barang Bantuan dan Penyalurannya', 'account_name' => 'Bantuan Sarana Pendidikan',    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '62001-00', 'subcategory' => 'Beban Program dan Operasional', 'account_name' => 'Beban Operasional Program',  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '62011-00', 'subcategory' => 'Beban Program dan Operasional', 'account_name' => 'Overhead',                   'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '62021-00', 'subcategory' => 'Beban Program dan Operasional', 'account_name' => 'Subsidi',                    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '62031-00', 'subcategory' => 'Beban Program dan Operasional', 'account_name' => 'Beban Operasional Lainnya',  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '63001-00', 'subcategory' => 'Beban Pendidikan dan Pelatihan', 'account_name' => 'Beban Pendidikan dan Pelatihan', 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '64001-00', 'subcategory' => 'Beban Pengembangan dan Komunikasi', 'account_name' => 'Beban Pengembangan dan Komunikasi', 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '65001-00', 'subcategory' => 'Beban Sukarelawan dan Staf Lapangan', 'account_name' => 'Beban Sukarelawan dan Staf Lapangan', 'normal_balance' => 'D', 'report_type' => 2],

            // 7. Beban Manajemen Umum
            ['id' => '71001-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Beban Penyusutan',             'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71011-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Biaya Rapat',                  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71021-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Perawatan dan Pemeliharaan',   'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71031-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Peralatan dan Mesin',           'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71041-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Sewa',                         'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71051-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Alat Tulis Kantor',           'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71061-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Bensin, Parkir dan Tol',       'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71071-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Internet, Listrik dan Air',    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71081-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Internet',                     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71091-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Beban Rumah Tangga Kantor',    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71101-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Fotocopy',                     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71111-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Asuransi',                     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '71121-00', 'subcategory' => 'Beban Administrasi Kantor', 'account_name' => 'Beban Administrasi Lain-lain', 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72001-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Gaji',                  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72011-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Tunjangan Asuransi',     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72021-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'BPJS',                   'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72031-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Pesangon',               'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72041-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Tunjangan Pengurus',     'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72051-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Tunjangan Hari Raya',    'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72061-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Dana Pensiun',           'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72071-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Tunjangan Komunikasi',   'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72081-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Pajak',                  'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72091-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Honor Posko',            'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72101-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Insentif',               'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '72111-00', 'subcategory' => 'Beban Pegawai', 'account_name' => 'Lain-lain',              'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '73001-00', 'subcategory' => 'Beban Jasa Profesional', 'account_name' => 'Beban Jasa Profesional', 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '74001-00', 'subcategory' => 'Beban Perjalanan Dinas', 'account_name' => 'Beban Perjalanan Dinas', 'normal_balance' => 'D', 'report_type' => 2],
            ['id' => '75001-00', 'subcategory' => 'Beban Lain-lain', 'account_name' => 'Beban Lain-lain', 'normal_balance' => 'D', 'report_type' => 2],
        ];

        foreach ($accounts as $account) {
            $subcategoryId = $subcategoryMap[$account['subcategory']] ?? null;

            if ($subcategoryId === null) {
                $this->command->warn("Subcategory \"{$account['subcategory']}\" not found — skipping {$account['id']}");
                continue;
            }

            ChartOfAccount::updateOrCreate(
                ['id' => $account['id']],
                [
                    'account_subcategory_id'  => $subcategoryId,
                    'account_name'            => $account['account_name'],
                    'normal_balance'          => $account['normal_balance'],
                    'financial_report_type_id' => $account['report_type'],
                ]
            );
        }

        $this->command->info('Seeded ' . count($accounts) . ' Chart of Account entries (Neraca & Laba Rugi).');
    }
}
