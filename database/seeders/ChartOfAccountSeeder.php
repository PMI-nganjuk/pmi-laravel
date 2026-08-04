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
            // 1. Aset Lancar / Aktiva Lancar
            ['id' => '11001-00', 'subcategory' => 'Kas', 'account_name' => 'Kas - UDD', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '11011-00', 'subcategory' => 'Kas', 'account_name' => 'Kas - Markas', 'normal_balance' => 'D', 'report_type' => 1],
            
            ['id' => '12001-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Jatim No rek 0192650534 ( Markas )', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12011-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Jatim No Rek 0193899692(Markas)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12021-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Jatim No Rek 0192124366 ( Markas )', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12031-00', 'subcategory' => 'Bank', 'account_name' => 'BNI No Rek 0394160985 ( UDD )', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12041-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Jatim Giro 0191018869 (UDD)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '12051-00', 'subcategory' => 'Bank', 'account_name' => 'Bank Mandiri (Operasional)', 'normal_balance' => 'D', 'report_type' => 1], // Dari seeder 1
            
            ['id' => '13001-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSUD Darsono Pacitan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13011-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSU MEDICAL MANDIRI PACITAN', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13021-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSUD Nganjuk', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13031-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSUD Kertosono', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13041-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RS Bhayangkara', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13051-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSIA Nganjuk', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13061-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Adi Amerta Gondang', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13071-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Dharma Husada', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13081-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Galuh Husada', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13091-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Nafira', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13101-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Amalia Syifa', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13111-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Sakti Medika', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13121-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Ashofa', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13131-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Ismi Medika', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13141-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Asyafiu Santosa', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13151-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Alma Regina', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13161-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik Ngudi Waluyo', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13171-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang UDD Lain', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13181-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Klinik ', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13191-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RS Widodo', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13201-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang RSI At-Tin', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13221-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Lainnya', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '13231-00', 'subcategory' => 'Piutang Lain-lain', 'account_name' => 'Piutang Karyawan', 'normal_balance' => 'D', 'report_type' => 1], // Dari seeder 1
            
            ['id' => '14001-00', 'subcategory' => 'Persediaan', 'account_name' => 'Bantuan Bukan Makanan (Markas)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '14011-00', 'subcategory' => 'Persediaan', 'account_name' => 'Bantuan Peralatan (Markas)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '14021-00', 'subcategory' => 'Persediaan', 'account_name' => 'Persediaan Bahan Habis Pakai (Markas)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '14031-00', 'subcategory' => 'Persediaan', 'account_name' => 'Persediaan Bahan Habis Pakai (UDD)', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '15001-00', 'subcategory' => 'Uang Muka Kerja', 'account_name' => 'Dropping', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '15011-00', 'subcategory' => 'Uang Muka Kerja', 'account_name' => 'Persekot Kegiatan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '15021-00', 'subcategory' => 'Uang Muka Kerja', 'account_name' => 'Penyisihan Kegiatan Uang Muka Kerja', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '16001-00', 'subcategory' => 'Biaya Dibayar Di Muka', 'account_name' => 'Biaya Dibayar Di Muka', 'normal_balance' => 'D', 'report_type' => 1],

            // 2. Aset Tidak Lancar
            ['id' => '21001-00', 'subcategory' => 'Tanah dan Bangunan', 'account_name' => 'Tanah', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '21011-00', 'subcategory' => 'Tanah dan Bangunan', 'account_name' => 'Bangunan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '22001-00', 'subcategory' => 'Aset Tetap Lainnya', 'account_name' => 'Kendaraan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '22011-00', 'subcategory' => 'Aset Tetap Lainnya', 'account_name' => 'Inventaris & Peralatan', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '22021-00', 'subcategory' => 'Aset Tetap Lainnya', 'account_name' => 'Inventaris Kantor', 'normal_balance' => 'D', 'report_type' => 1], // Dari seeder 1
            
            ['id' => '23001-00', 'subcategory' => 'Akumulasi Penyusutan', 'account_name' => 'AK Penyusutan Bangunan', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '23011-00', 'subcategory' => 'Akumulasi Penyusutan', 'account_name' => 'AK Penyusutan Kendaraan', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '23021-00', 'subcategory' => 'Akumulasi Penyusutan', 'account_name' => 'AK Penyusutan Inventaris & Peralatan', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '24001-00', 'subcategory' => 'Aset Tidak Lancar Lainnya', 'account_name' => 'Aset Tidak Berwujud', 'normal_balance' => 'D', 'report_type' => 1],
            ['id' => '25001-00', 'subcategory' => 'Investasi pada entitas anak', 'account_name' => 'Investasi pada entitas anak', 'normal_balance' => 'D', 'report_type' => 1],

            // 3. Liabilitas Jangka Pendek & Jangka Panjang
            ['id' => '31001-00', 'subcategory' => 'Hutang Kepada Lembaga Lain', 'account_name' => 'Hutang Kepada Lembaga Lain', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '31011-00', 'subcategory' => 'Hutang Kepada Lembaga Lain', 'account_name' => 'Hutang Bank', 'normal_balance' => 'C', 'report_type' => 1], // Dari seeder 1
            
            ['id' => '32001-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. Dua Pilar', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32011-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. Java Borneo', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32021-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. TBI', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32031-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. Barik', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32041-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang CV. Atma', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32051-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. Kimia Farma', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32061-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. Global Trans', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32071-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. IHS', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32081-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. JBJ', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32091-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT. PMI SU', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32101-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang CV Sentosa', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32111-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang CV ASA', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32121-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang PT Abhimata', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '32131-00', 'subcategory' => 'Hutang Lain-lain', 'account_name' => 'Hutang Pihak Ketiga', 'normal_balance' => 'C', 'report_type' => 1], // Dari seeder 1
            
            ['id' => '33001-00', 'subcategory' => 'Hutang Pajak', 'account_name' => 'Hutang Pajak', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '34001-00', 'subcategory' => 'Biaya Yang Masih Harus Dibayar', 'account_name' => 'Accruals', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '34011-00', 'subcategory' => 'Biaya Yang Masih Harus Dibayar', 'account_name' => 'Accrued Gaji', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '34021-00', 'subcategory' => 'Biaya Yang Masih Harus Dibayar', 'account_name' => 'Accrued Listrik', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '35001-00', 'subcategory' => 'Hutang Usaha Jangka Panjang Inter Co', 'account_name' => 'Share holder loan', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '35011-00', 'subcategory' => 'Liabilitas Tidak Lancar Lainnya', 'account_name' => 'Liabilitas Tidak Lancar Lainnya', 'normal_balance' => 'C', 'report_type' => 1],

            // 4. Aset Netto
            ['id' => '41001-00', 'subcategory' => 'Akumulasi Aset Netto Tidak Terikat', 'account_name' => 'Akumulasi Aset Netto Tidak Terikat', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '42001-00', 'subcategory' => 'Pendapatan Netto Tidak Terikat Periode Berjalan', 'account_name' => 'Pendapatan Netto Tidak Terikat Periode Berjalan', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '43001-00', 'subcategory' => 'Akumulasi Aset Netto Terikat', 'account_name' => 'Akumulasi Aset Netto Terikat', 'normal_balance' => 'C', 'report_type' => 1],
            ['id' => '44001-00', 'subcategory' => 'Pendapatan Netto Terikat Periode Berjalan', 'account_name' => 'Pendapatan Netto Terikat Periode Berjalan', 'normal_balance' => 'C', 'report_type' => 1],

            // ================== LABA RUGI (REPORT TYPE 2) ==================
            //          // 5. Pendapatan
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

        $this->command->info('Seeded ' . count($accounts) . ' Chart of Account entries.');
    }
}
