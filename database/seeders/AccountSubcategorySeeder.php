<?php

namespace Database\Seeders;

use App\Models\AccountCategory;
use App\Models\AccountSubcategory;
use Illuminate\Database\Seeder;

class AccountSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil mapping ID kategori akun berdasarkan nama.
        $categoryMap = AccountCategory::pluck('id', 'name')->toArray();

        $subcategories = [
            // Aset Lancar
            ['name' => 'Kas', 'category_name' => 'Aset Lancar'],
            ['name' => 'Bank', 'category_name' => 'Aset Lancar'],
            ['name' => 'Piutang Lain-lain', 'category_name' => 'Aset Lancar'],
            ['name' => 'Persediaan', 'category_name' => 'Aset Lancar'],
            ['name' => 'Uang Muka Kerja', 'category_name' => 'Aset Lancar'],
            ['name' => 'Biaya Dibayar Di Muka', 'category_name' => 'Aset Lancar'],

            // Aset Tidak Lancar
            ['name' => 'Tanah dan Bangunan', 'category_name' => 'Aset Tidak Lancar'],
            ['name' => 'Aset Tetap Lainnya', 'category_name' => 'Aset Tidak Lancar'],
            ['name' => 'Akumulasi Penyusutan', 'category_name' => 'Aset Tidak Lancar'],
            ['name' => 'Aset Tidak Lancar Lainnya', 'category_name' => 'Aset Tidak Lancar'],
            ['name' => 'Investasi pada entitas anak', 'category_name' => 'Aset Tidak Lancar'],

            // Liabilitas Jangka Pendek
            ['name' => 'Hutang Kepada Lembaga Lain', 'category_name' => 'Liabilitas Jangka Pendek'],
            ['name' => 'Hutang Lain-lain', 'category_name' => 'Liabilitas Jangka Pendek'],
            ['name' => 'Hutang Pajak', 'category_name' => 'Liabilitas Jangka Pendek'],
            ['name' => 'Biaya Yang Masih Harus Dibayar', 'category_name' => 'Liabilitas Jangka Pendek'],

            // Liabilitas Jangka Panjang
            ['name' => 'Hutang Usaha Jangka Panjang Inter Co', 'category_name' => 'Liabilitas Jangka Panjang'],
            ['name' => 'Liabilitas Tidak Lancar Lainnya', 'category_name' => 'Liabilitas Jangka Panjang'],

            // Aset Netto
            ['name' => 'Akumulasi Aset Netto Tidak Terikat', 'category_name' => 'Aset Netto'],
            ['name' => 'Pendapatan Netto Tidak Terikat Periode Berjalan', 'category_name' => 'Aset Netto'],
            ['name' => 'Akumulasi Aset Netto Terikat', 'category_name' => 'Aset Netto'],
            ['name' => 'Pendapatan Netto Terikat Periode Berjalan', 'category_name' => 'Aset Netto'],

            // Pendapatan
            ['name' => 'Sumbangan', 'category_name' => 'Pendapatan'],
            ['name' => 'Non Sumbangan', 'category_name' => 'Pendapatan'],

            // Beban Program
            ['name' => 'Beban Barang Bantuan dan Penyalurannya', 'category_name' => 'Beban Program'],
            ['name' => 'Beban Program dan Operasional', 'category_name' => 'Beban Program'],
            ['name' => 'Beban Pendidikan dan Pelatihan', 'category_name' => 'Beban Program'],
            ['name' => 'Beban Pengembangan dan Komunikasi', 'category_name' => 'Beban Program'],
            ['name' => 'Beban Sukarelawan dan Staf Lapangan', 'category_name' => 'Beban Program'],

            // Beban Manajemen Umum
            ['name' => 'Beban Administrasi Kantor', 'category_name' => 'Beban Manajemen Umum'],
            ['name' => 'Beban Pegawai', 'category_name' => 'Beban Manajemen Umum'],
            ['name' => 'Beban Jasa Profesional', 'category_name' => 'Beban Manajemen Umum'],
            ['name' => 'Beban Perjalanan Dinas', 'category_name' => 'Beban Manajemen Umum'],
            ['name' => 'Beban Lain-lain', 'category_name' => 'Beban Manajemen Umum'],  
        ];

        foreach ($subcategories as $subcategory) {
            $categoryId = $categoryMap[$subcategory['category_name']] ?? null;

            if ($categoryId === null) {
                $this->command->warn("Category \"{$subcategory['category_name']}\" not found — skipping subcategory \"{$subcategory['name']}\"");
                continue;
            }

            AccountSubcategory::updateOrCreate(
                ['name' => $subcategory['name']],
                ['account_category_id' => $categoryId]
            );
        }
    }
}