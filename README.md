# 🏢 Sistem Informasi Keuangan & Akuntansi PMI Kabupaten Nganjuk

Aplikasi berbasis web untuk pengelolaan keuangan, transaksi kas, jurnal penyesuaian, dan penyusunan laporan keuangan otomatis di lingkungan **Palang Merah Indonesia (PMI) Kabupaten Nganjuk**.

---

## 📌 Daftar Isi
1. [Tentang Sistem](#-tentang-sistem)
2. [Fitur Utama](#-fitur-utama)
3. [Teknologi & Spesifikasi](#-teknologi--spesifikasi)
4. [Panduan Instalasi & Pengaturan Awal](#-panduan-instalasi--pengaturan-awal)
5. [Akun Bawaan (Kredensial Default)](#-akun-bawaan-kredensial-default)
6. [Panduan Pemeliharaan & Maintenance](#-panduan-pemeliharaan--maintenance)
   - [Pembersihan Cache & Optimalisasi](#1-pembersihan-cache--optimalisasi)
   - [Manajemen Database & Migrasi](#2-manajemen-database--migrasi)
   - [Mode Maintenance Server](#3-mode-maintenance-server)
   - [Prosedur Backup & Restore](#4-prosedur-backup--restore)
   - [Monitoring Log & Activity Log](#5-monitoring-log--activity-log)
   - [Penyelesaian Masalah (Troubleshooting)](#6-penyelesaian-masalah-troubleshooting)
7. [Ringkasan Perintah Penting (Cheat Sheet)](#-ringkasan-perintah-penting-cheat-sheet)
8. [Struktur Proyek](#-struktur-proyek)

---

## ℹ️ Tentang Sistem

Sistem ini dikembangkan khusus untuk mendukung akuntabilitas dan transparansi keuangan di **PMI Kabupaten Nganjuk**. Mengadopsi standar akuntansi nirlaba, aplikasi ini membantu menyusun laporan penerimaan kas, pengeluaran kas, serta laporan keuangan berkala seperti Laba Rugi, Posisi Keuangan (Neraca), Perubahan Aset Netto, dan Arus Kas secara otomatis.

---

## ✨ Fitur Utama

- 💰 **Transaksi Kas**:
  - **Penerimaan Kas (Cash Receipts)**: Pencatatan kas masuk dengan auto-suggest akun & deskripsi.
  - **Pengeluaran Kas (Cash Disbursements)**: Pencatatan kas keluar dengan penomoran dokumen otomatis (`DocumentNumberService`).
- 📝 **Jurnal Penyesuaian (Adjusting Entries)**: Fitur penyesuaian pencatatan periode akuntansi.
- 📖 **Buku Besar (General Ledger)**: Tampilan rinci mutasi debit-kredit per kode akun (COA).
- 📊 **Laporan Keuangan Otomatis & Export**:
  - Laporan Laba Rugi (Profit & Loss)
  - Laporan Posisi Keuangan / Neraca (Balance Sheet)
  - Laporan Perubahan Aset Netto (Analysis Notes)
  - Laporan Arus Kas (Cash Flow)
  - Export data ke format **Excel (.xlsx)** & **PDF**.
- 🗂️ **Bagan Akun (Chart of Accounts / COA)**: Pengelolaan kategori, subkategori, dan kode akun.
- 🎯 **Manajemen Program & Profil Organisasi**: Pengelompokan transaksi berdasarkan program PMI.
- 👥 **Manajemen Pengguna & Role Access (RBAC)**:
  - **Admin**: Akses penuh ke pengguna, pengaturan sistem, dan laporan.
  - **Staf Keuangan**: Mengelola transaksi kas, jurnal penyesuaian, dan laporan keuangan.
  - **Karyawan / Staff**: Akses terbatas sesuai otoritas operasional.
  - **User / Pengguna Umum**: Akses dasar informasi.
- 🛡️ **Audit Trail (Activity Log)**: Pelacakan riwayat aktivitas pengguna (Spatie Activitylog).

---

## 🛠️ Teknologi & Spesifikasi

- **Backend**: PHP >= 8.3, Laravel Framework 12.x
- **Frontend**: Blade Templating, Tailwind CSS v4, Alpine.js, Livewire v4
- **Build Tools**: Vite 8.x
- **Database**: SQLite (Default lokal) / MySQL / PostgreSQL
- **Pustaka Utama**:
  - `maatwebsite/excel` (Export Excel)
  - `spatie/laravel-activitylog` (Audit Log)
  - `puppeteer` & `docx` (Pencetakan / Dokumen)

---

## 🚀 Panduan Instalasi & Pengaturan Awal

### 1. Prasyarat Sistem
Pastikan perangkat server/pengembang telah terpasang:
- **PHP** >= 8.3 (dengan ekstensi: `pdo`, `mbstring`, `openssl`, `xml`, `curl`, `sqlite3` atau `mysqli`)
- **Composer** >= 2.x
- **Node.js** >= 20.x & **NPM**

### 2. Langkah-langkah Instalasi

```bash
# 1. Clone repositori
git clone <URL_REPOSITORI>
cd laravel-pmi-new

# 2. Install dependensi PHP (Composer)
composer install

# 3. Install dependensi JavaScript/CSS (Node.js)
npm install

# 4. Salin file konfigurasi lingkungan (.env)
cp .env.example .env

# 5. Generate Application Key
php artisan key:generate

# 6. Konfigurasi Database pada file .env
# Untuk SQLite (default):
# pastikan DB_CONNECTION=sqlite di .env dan buat file sqlite kosong jika belum ada:
# touch database/database.sqlite (di Linux/Mac) atau New-Item database/database.sqlite (di Windows PowerShell)

# 7. Jalankan Migrasi Database beserta Seeder Data Awal
php artisan migrate --seed

# 8. Buat link simbolis storage
php artisan storage:link

# 9. Build aset frontend (Environment Pengembangan vs Produksi)
# Untuk Pengembangan (Hot Reload):
npm run dev

# Untuk Produksi:
npm run build
```

### 3. Menjalankan Server Lokal

```bash
php artisan serve
```
Buka browser di URL: `http://127.0.0.1:8000`

---

## 🔑 Akun Bawaan (Kredensial Default)

Setelah menjalankan `php artisan db:seed` atau `migrate --seed`, gunakan kredensial berikut untuk masuk ke sistem:

| Role | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@pmi-nganjuk.or.id` | `password` | Kelola Pengguna, Setting, Finance, Laporan |
| **Staf Keuangan** | `stafkeuangan@pmi-nganjuk.or.id` | `password` | Kelola Transaksi Kas, Jurnal, COA, Laporan |
| **Karyawan** | `karyawan@pmi-nganjuk.or.id` | `password` | Akses Tingkat Staf / View Dashboard |
| **Pengguna Umum** | `pengguna@pmi-nganjuk.or.id` | `password` | Akses Dasar Sistem |

> ⚠️ **PENTING UNTUK PRODUKSI**: Segera ubah password seluruh akun default ini di lingkungan produksi!

---

## ⚙️ Panduan Pemeliharaan & Maintenance

Halaman ini berisi petunjuk operasional lengkap bagi **Administrator** dan **Tim IT** untuk merawat, memperbarui, serta memulihkan sistem jika terjadi kendala.

### 1. Pembersihan Cache & Optimalisasi

Cache Laravel dapat menyebabkan perubahan tidak muncul jika tidak dibersihkan saat ada pembaruan kode.

#### A. Pembersihan Cache (Development / Saat Ada Masalah)
```bash
# Membersihkan seluruh cache sistem sekaligus
php artisan optimize:clear

# Atau bersihkan satu per satu:
php artisan config:clear    # Cache Konfigurasi
php artisan route:clear     # Cache Routing
php artisan view:clear      # Cache View Blade
php artisan cache:clear     # Cache Aplikasi/Session
```

#### B. Optimalisasi Cache (Wajib Di-run pada Server Produksi)
```bash
# Menggabungkan caching konfigurasi, route, dan view untuk kecepatan maksimal
php artisan optimize
npm run build
```

---

### 2. Manajemen Database & Migrasi

#### A. Menjalankan Migrasi Baru (Update Fitur Baru)
Setiap ada pembaruan kode dari tim pengembang yang menambah tabel/kolom database:
```bash
php artisan migrate --force
```

#### B. Reset Database Ke Kondisi Awal (Development Only)
> 🚨 **PERHATIAN**: Perintah di bawah ini akan **MENGHAPUS SELURUH DATA** database dan mengisinya kembali dari seeder!
```bash
php artisan migrate:fresh --seed
```

---

### 3. Mode Maintenance Server

Gunakan Mode Maintenance saat sedang melakukan perbaikan besar, pembaruan skema database, atau migrasi server agar pengguna tidak mengakses sistem secara bersamaan.

#### A. Mengaktifkan Mode Maintenance
```bash
# Mengunci aplikasi dengan pesan custom dan retry interval
php artisan down --message="Sistem Keuangan PMI Nganjuk sedang dalam pemeliharaan rutin. Silakan coba beberapa saat lagi." --retry=60
```

#### B. Mengakses Aplikasi Saat Mode Maintenance (Bypass)
Jika admin ingin tetap bisa mengakses aplikasi saat maintenance aktif:
```bash
php artisan down --secret="pmi-maintenance-pass-2026"
```
Admin dapat mengakses aplikasi melalui URL: `http://domain-pmi.or.id/pmi-maintenance-pass-2026` untuk menyimpan cookie bypass.

#### C. Mematikan Mode Maintenance (Aplikasi Kembali Online)
```bash
php artisan up
```

---

### 4. Prosedur Backup & Restore

#### A. Backup Database
- **SQLite (Default)**:
  Cukup salin file `database/database.sqlite` ke lokasi aman/cloud storage secara berkala.
- **MySQL / MariaDB**:
  ```bash
  mysqldump -u [username] -p [nama_database] > backup_pmi_$(date +%Y%m%d_%H%M%S).sql
  ```

#### B. Backup File Upload / Storage
Salin folder `storage/app/public` dan `storage/app/documents` (jika ada file bukti transfer/dokumen yang diunggah).

#### C. Restore Database
- **SQLite**: Timpa file `database/database.sqlite` dengan file cadangan.
- **MySQL / MariaDB**:
  ```bash
  mysql -u [username] -p [nama_database] < backup_pmi_filename.sql
  ```

---

### 5. Monitoring Log & Activity Log

#### A. Log Aplikasi Laravel
File log utama tersimpan di:
`storage/logs/laravel.log`

Untuk melihat log secara *realtime* di terminal:
```bash
# Menggunakan Laravel Pail (Fitur Laravel 12)
php artisan pail

# Atau menggunakan perintah tail (Linux/Mac)
tail -f storage/logs/laravel.log
```

#### B. Activity Log (Audit Trail User)
Setiap transaksi penerimaan, pengeluaran, dan perubahan data dicatat otomatis di tabel `activity_log`.
Admin dapat mengecek aktivitas user melalui menu aplikasi atau kueri database jika diperlukan investigasi audit.

---

### 6. Penyelesaian Masalah (Troubleshooting)

| Gejala Masalah | Penyebab Umum | Solusi / Perbaikan |
| :--- | :--- | :--- |
| **Halaman Blank / Error 500** | Permisi folder / Cache crash / `.env` error | 1. Cek `storage/logs/laravel.log`<br>2. Jalankan `php artisan optimize:clear`<br>3. Pastikan `APP_KEY` terisi |
| **Gagal Upload / Simpan Dokumen** | Folder `storage` tidak writable / symlink putus | 1. Beri akses write: `chmod -R 775 storage bootstrap/cache`<br>2. Buat symlink: `php artisan storage:link` |
| **Asset CSS/JS Tidak Muat / 404** | Build Vite belum di-run di produksi | Jalankan `npm run build` |
| **Laporan / Export Excel Gagal** | Ekstensi PHP `zip` / `gd` / `xml` tidak aktif | Pastikan ekstensi `php-zip`, `php-gd`, `php-xml` terpasang dan diaktifkan di `php.ini` |
| **Login Gagal Terus / CSRF Token Expired** | Session driver / Permission folder session | Jalankan `php artisan config:clear` dan cek permisi folder `storage/framework/sessions` |

---

## 📋 Ringkasan Perintah Penting (Cheat Sheet)

```bash
# 🛠️ PERINTAH PENGEMBANGAN (DEVELOPMENT)
php artisan serve                   # Jalankan web server lokal
npm run dev                         # Jalankan Vite Hot Reload

# 🧹 PERINTAH PEMELIHARAAN CACHE
php artisan optimize:clear          # Bersihkan semua jenis cache
php artisan optimize                # Cache ulang untuk kecepatan produksi

# 🗄️ PERINTAH DATABASE
php artisan migrate                 # Jalankan migrasi baru
php artisan db:seed                 # Isi data awal/dummy
php artisan migrate:fresh --seed    # Reset total DB & seeder

# 🚨 PERINTAH MAINTENANCE SERVER
php artisan down                    # Mode perbaikan (Lock app)
php artisan up                      # Matikan mode perbaikan (Online)

# 📄 MONITORING LOG
php artisan pail                    # Stream log interaktif
```

---

## 📂 Struktur Proyek Utama

```text
laravel-pmi-new/
├── app/
│   ├── Enums/                 # RoleEnum & konstanta enum aplikasi
│   ├── Exports/               # Class export Excel (Laba Rugi, Neraca, DLL)
│   ├── Http/
│   │   ├── Controllers/       # Controller (Kas, Jurnal, COA, Laporan, User)
│   │   └── Middleware/        # Middleware autentikasi & otoritas
│   ├── Models/                # Eloquent Models (Transaction, COA, User, dll)
│   ├── Repositories/          # Query logic & pola repository
│   └── Services/              # Business logic (CashReceipt, CashDisbursement, Laporan)
├── database/
│   ├── factories/             # Factory testing
│   ├── migrations/            # Skema migrasi database
│   └── seeders/               # Data seeder awal (COA, User, Financial Report Types)
├── resources/
│   ├── views/                 # Blade Templates & Components
│   └── css / js/              # Tailwind CSS v4 & Alpine.js scripts
├── routes/
│   └── web.php                # Rute web utama aplikasi
├── storage/                   # File log, upload, dan cache framework
└── vite.config.js             # Konfigurasi Vite asset bundler
```

---

## 📄 Lisensi & Hak Cipta

Hak Cipta © 2026 **Palang Merah Indonesia (PMI) Kabupaten Nganjuk**. All rights reserved.

