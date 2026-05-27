<?php

namespace Database\Seeders;

use App\Models\AccountSubcategory;
use Illuminate\Database\Seeder;

class AccountSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            ['name' => 'Kas', 'account_category_id' => 1],
            ['name' => 'Bank', 'account_category_id' => 1],
            ['name' => 'Piutang Lain-lain', 'account_category_id' => 1],
            ['name' => 'Persediaan', 'account_category_id' => 1],
            ['name' => 'Uang Muka Kerja', 'account_category_id' => 1],
            ['name' => 'Biaya Dibayar Di Muka', 'account_category_id' => 1],
            ['name' => 'Tanah dan Bangunan', 'account_category_id' => 2],
            ['name' => 'Aset Tetap Lainnya', 'account_category_id' => 2],
            ['name' => 'Akumulasi Penyusutan', 'account_category_id' => 2],
            ['name' => 'Aset Tidak Lancar Lainnya', 'account_category_id' => 2],
            ['name' => 'Investasi pada entitas anak', 'account_category_id' => 2],
            ['name' => 'Hutang Kepada Lembaga Lain', 'account_category_id' => 3],
            ['name' => 'Hutang Lain-lain', 'account_category_id' => 3],
            ['name' => 'Hutang Pajak', 'account_category_id' => 3],
            ['name' => 'Biaya Yang Masih Harus Dibayar', 'account_category_id' => 3],
            ['name' => 'Hutang Usaha Jangka Panjang Inter Co', 'account_category_id' => 3],
            ['name' => 'Liabilitas Tidak Lancar Lainnya', 'account_category_id' => 3],
            ['name' => 'Akumulasi Aset Netto Tidak Terikat', 'account_category_id' => 4],
            ['name' => 'Pendapatan Netto Tidak Terikat Periode Berjalan', 'account_category_id' => 4],
            ['name' => 'Akumulasi Aset Netto Terikat', 'account_category_id' => 4],
            ['name' => 'Pendapatan Netto Terikat Periode Berjalan', 'account_category_id' => 4],
            ['name' => 'Sumbangan', 'account_category_id' => 5],
            ['name' => 'Non Sumbangan', 'account_category_id' => 5],
            ['name' => 'Beban Barang Bantuan dan Penyalurannya', 'account_category_id' => 6],
            ['name' => 'Beban Program dan Operasional', 'account_category_id' => 6],
            ['name' => 'Beban Pendidikan dan Pelatihan', 'account_category_id' => 6],
            ['name' => 'Beban Pengembangan dan Komunikasi', 'account_category_id' => 6],
            ['name' => 'Beban Sukarelawan dan Staf Lapangan', 'account_category_id' => 6],
            ['name' => 'Beban Administrasi Kantor', 'account_category_id' => 7],
            ['name' => 'Beban Pegawai', 'account_category_id' => 7],
            ['name' => 'Beban Jasa Profesional', 'account_category_id' => 7],
            ['name' => 'Beban Perjalanan Dinas', 'account_category_id' => 7],
            ['name' => 'Beban Lain-lain', 'account_category_id' => 7],  
        ];


        foreach ($subcategories as $subcategory) {
            AccountSubcategory::updateOrCreate(
                ['name' => $subcategory['name']],
                ['account_category_id' => $subcategory['account_category_id']]
            );
        }
    }
}