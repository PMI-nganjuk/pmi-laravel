# Product Requirements Document (PRD)
## Sistem Manajemen Keuangan PMI

| | |
|---|---|
| **Versi** | 2.0 |
| **Status** | Draft |
| **Tanggal** | Mei 2026 |
| **Diperbarui dari** | v1.0 (Laravel 12 + Filament) |

---

## 1. Informasi Proyek

| Atribut | Detail |
|---|---|
| **Nama Proyek** | Sistem Manajemen Keuangan PMI (Palang Merah Indonesia) |
| **Platform** | Web Application (Admin Dashboard) |
| **Tujuan Utama** | Mendigitalisasi dan mengotomatisasi pencatatan transaksi kas, penyusunan jurnal, buku besar, hingga pembuatan laporan keuangan yang terstruktur sesuai standar organisasi. |

---

## 2. Tech Stack & Infrastructure

### 2.1 Server / Backend

| Komponen | Teknologi | Versi | Keterangan |
|---|---|---|---|
| **PHP** | PHP | ^8.3 | Minimum wajib untuk Laravel 13 |
| **Framework** | Laravel | ^13.0 (13.5.x) | Rilis Maret 2026, zero breaking changes dari v12 |
| **Autentikasi** | Laravel Sanctum | ^4.0 | Session-based auth untuk web |
| **Otorisasi** | spatie/laravel-permission | ^6.0 | RBAC — roles & permissions |
| **Audit Trail** | spatie/laravel-activitylog | ^4.0 | Catat semua perubahan data transaksi |
| **Export Excel** | maatwebsite/excel | ^3.1 | Export laporan keuangan ke format XLSX |
| **Database** | MySQL / PostgreSQL | — | Via Eloquent ORM dengan double-entry logic |

### 2.2 Frontend / UI

| Komponen | Teknologi | Versi | Keterangan |
|---|---|---|---|
| **UI Framework** | Livewire | ^4.2 | Komponen reaktif full-stack, pengganti Filament |
| **CSS Framework** | Tailwind CSS | ^4.2.4 | Pure Tailwind, tanpa Bootstrap |
| **JavaScript Reaktif** | Alpine.js | Bundled Livewire 4 | Sudah include otomatis, tidak install terpisah |
| **Build Tool** | Vite | ^6.0 | Bundling & hot reload aset frontend |
| **Tailwind Plugin Vite** | @tailwindcss/vite | ^4.2.4 | Integrasi Tailwind dengan Vite |
| **Tailwind Plugin Form** | @tailwindcss/forms | ^0.5 | Reset style bawaan browser untuk elemen form |
| **Node.js** | Node.js | ^20 LTS | Runtime untuk build frontend |

### 2.3 Catatan Arsitektur

- **Tidak menggunakan Filament** — UI dibangun manual dengan Livewire 4 + Blade + Tailwind CSS v4.
- **Tidak menggunakan Bootstrap** — 100% Tailwind CSS untuk menjaga bundle size minimal.
- **Tidak ada export PDF** — laporan keuangan hanya tersedia dalam format XLSX.
- **Tailwind v4 CSS-first** — tidak ada `tailwind.config.js`, semua konfigurasi tema menggunakan `@theme` di dalam file CSS.

---

## 3. Arsitektur Sistem

Proyek menggunakan pola **Controller → Service → Repository** untuk memisahkan tanggung jawab setiap layer.

```
HTTP Request
    │
    ▼
Controller (validasi input via FormRequest)
    │
    ▼
Service Layer (semua logika bisnis & akuntansi)
    │
    ▼
Repository (abstraksi query Eloquent)
    │
    ▼
Model / Database
    │
    ▼
Observer (side-effects otomatis saat data berubah)
```

### Layer Utama

| Layer | Lokasi | Tanggung Jawab |
|---|---|---|
| **Controller** | `app/Http/Controllers/` | Terima request, validasi, delegate ke Service |
| **Livewire Component** | `app/Http/Livewire/` | UI reaktif (form, tabel, filter) |
| **Service** | `app/Services/` | Logika bisnis akuntansi (posting GL, double-entry) |
| **Repository** | `app/Repositories/` | Abstraksi query database |
| **Model** | `app/Models/` | Representasi tabel, relasi, casts |
| **Observer** | `app/Observers/` | Side-effects otomatis (update GL, audit log) |
| **Enum** | `app/Enums/` | Standarisasi tipe data (RoleEnum, TipeTransaksiEnum) |
| **Export** | `app/Exports/` | Logika export Excel per jenis laporan |

---

## 4. Manajemen Pengguna & Hak Akses (RBAC)

Sistem menggunakan RBAC berbasis **spatie/laravel-permission** dengan empat role:

| Role | Hak Akses |
|---|---|
| **Admin** | Akses penuh ke seluruh sistem. Manajemen user, konfigurasi profil organisasi, dan semua fitur operasional. |
| **Manager Keuangan** | Akses *read-only* level manajerial. Dapat melihat laporan keuangan, buku besar, dan realisasi transaksi tanpa mengubah data. |
| **Staf Keuangan** | Operator utama. Input transaksi kas, jurnal penyesuaian, pengelolaan COA, dan export laporan. |
| **Pegawai** | Akses terbatas. Hanya dapat melihat informasi yang relevan dengan tugasnya (contoh: Program Kerja). |

### Matriks Akses per Modul

| Modul | Admin | Manager Keuangan | Staf Keuangan | Pegawai |
|---|:---:|:---:|:---:|:---:|
| Profil Organisasi | CRUD | R | R | — |
| Manajemen User | CRUD | — | — | — |
| Chart of Accounts | CRUD | R | CRUD | — |
| Program Kerja | CRUD | R | CRU | R |
| Transaksi Kas | CRUD | R | CR | — |
| Jurnal Penyesuaian | CRUD | R | CR | — |
| Buku Besar (GL) | R | R | R | — |
| Laporan Keuangan | R + Export | R + Export | R + Export | — |

---

## 5. Fitur Utama & Modul

### 5.1 Manajemen Profil Organisasi

**Deskripsi:** Pengaturan identitas PMI cabang yang bersangkutan.

**Fungsi:**
- Mengatur nama organisasi, logo, alamat, dan informasi kontak.
- Data profil dipanggil secara dinamis sebagai kop surat / header pada laporan keuangan yang diekspor.

---

### 5.2 Bagan Akun (Chart of Accounts)

**Deskripsi:** Pengelolaan kode dan nama akun keuangan sebagai standar pencatatan (COA).

**Fungsi:**
- CRUD data akun keuangan.
- Klasifikasi hierarki tiga level:
  - **Kategori Satu** — Kategori utama: Aset, Kewajiban, Ekuitas, Pendapatan, Beban.
  - **Kategori Dua** — Sub-klasifikasi per kategori utama.
  - **Chart of Accounts** — Akun spesifik dengan kode akun unik.
- Data COA di-*cache* (`Cache::remember`) karena jarang berubah dan sering diquery. Cache diinvalidasi otomatis via Observer saat ada perubahan akun.

---

### 5.3 Manajemen Program Kerja

**Deskripsi:** Pencatatan program atau kegiatan operasional yang memiliki alokasi dana.

**Fungsi:**
- Mencatat nama program kerja, deskripsi, dan target anggaran.
- Berfungsi sebagai *cost center*. Setiap transaksi dapat dikaitkan ke Program Kerja tertentu untuk pelacakan realisasi anggaran vs. rencana.

---

### 5.4 Transaksi Kas

**Deskripsi:** Modul operasional utama untuk merekam arus kas masuk dan keluar.

**Fungsi:**

| Sub-Fitur | Detail |
|---|---|
| **Kas Masuk (IN)** | Nomor dokumen auto-generate format `BKMUDD-XXX` |
| **Kas Keluar (OUT)** | Nomor dokumen auto-generate format `BKKUDD-XXX` |
| **Atribut Wajib** | Tanggal, Akun Kas, Akun Lawan, Nominal, Program Kerja, User Pencatat, Keterangan |
| **Posting Otomatis** | Setiap transaksi yang disimpan otomatis membuat 2 baris di General Ledger (debit & kredit) via Observer |
| **Validasi Double-Entry** | Total debit harus sama dengan total kredit; jika tidak, seluruh operasi di-rollback via `DB::transaction()` |
| **Soft Delete** | Transaksi tidak dapat dihapus permanen — menggunakan `SoftDeletes` untuk menjaga integritas audit |

**Alur Transaksi:**
1. Staf Keuangan input form via Livewire component `CashTransactionForm`.
2. Data divalidasi via `TransaksiKasRequest` (FormRequest).
3. `TransaksiService::simpan()` dipanggil dari Controller.
4. Observer `TransaksiObserver@created` fire event `TransaksiPosted`.
5. `GeneralLedgerService::postingJurnal()` membuat 2 baris GL dalam satu DB transaction.
6. Audit log dicatat otomatis via spatie/activitylog.

---

### 5.5 Jurnal Penyesuaian

**Deskripsi:** Pencatatan transaksi non-kas di akhir periode pelaporan.

**Fungsi:**
- Input penyusutan, akrual, dan koreksi pencatatan.
- Proses sama dengan Transaksi Kas (form → service → GL) namun bertipe `TipeTransaksiEnum::PENYESUAIAN`.
- Memastikan saldo Buku Besar menampilkan nilai akurat sebelum penutupan buku.

---

### 5.6 Buku Besar (General Ledger)

**Deskripsi:** Konsolidasi riwayat pergerakan debit dan kredit per akun.

**Fungsi:**
- Menampilkan riwayat mutasi per akun COA dengan filter periode.
- Mendukung pelacakan (*tracing*) dan audit data transaksi historis.
- Tampilan via Livewire component `GeneralLedgerTable` dengan lazy loading dan pagination 50 baris per halaman.
- Index database pada kolom `coa_id`, `tanggal`, `periode` untuk performa query.

---

### 5.7 Laporan Keuangan

**Deskripsi:** Modul pelaporan tingkat akhir untuk menyajikan informasi finansial kepada pengurus PMI.

**Fungsi:**
- Filter berdasarkan rentang tanggal (tanggal awal s/d tanggal akhir).
- Kalkulasi agregasi otomatis: Total Pemasukan, Total Pengeluaran, Saldo.
- **Export ke Excel (XLSX)** — format rapi siap cetak, termasuk kop surat dari profil organisasi.
- Export besar dijalankan via **Queue** (job `GenerateLaporanExport implements ShouldQueue`) agar tidak memblokir server. User mendapat notifikasi saat file siap diunduh.
- Data laporan periode lampau di-*cache* karena nilai historis tidak berubah.

---

## 6. Non-Functional Requirements (NFR)

### 6.1 Keamanan (Security)

| Aspek | Implementasi |
|---|---|
| **Authentication** | Laravel Sanctum + middleware `auth`, `verified` |
| **Authorization** | spatie/laravel-permission + Laravel Policy per model |
| **CSRF** | Laravel 13 `PreventRequestForgery` — validasi header `Origin` + token fallback |
| **Mass Assignment** | Selalu `$fillable` eksplisit di semua model — tidak pernah `$guarded = []` |
| **Input Validation** | Semua input melalui `FormRequest` dengan rule ketat (tipe numerik, regex desimal, cek referensi FK) |
| **SQL Injection** | Eloquent ORM + parameter binding — tidak ada raw query string concatenation |
| **XSS** | Blade auto-escape `{{ }}` untuk semua output ke view |
| **Security Headers** | Middleware custom: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` |
| **Rate Limiting** | `RateLimiter` pada endpoint login dan export laporan |
| **Audit Trail** | spatie/activitylog mencatat user, waktu, nilai lama vs. baru pada setiap perubahan transaksi |
| **Soft Delete** | Semua model transaksi menggunakan `SoftDeletes` — tidak ada hard delete data finansial |
| **Environment** | `APP_DEBUG=false`, `APP_ENV=production` di server; semua credentials di `.env` |

### 6.2 Performa

| Aspek | Implementasi |
|---|---|
| **Database Index** | Index pada kolom `coa_id`, `tanggal`, `periode` di tabel GL dan transaksi |
| **Eager Loading** | Selalu `->with([...])` saat query relasi untuk mencegah N+1 |
| **Caching** | Cache COA, kategori, dan saldo periode lampau via `Cache::remember()` |
| **Pagination** | Tabel transaksi dan GL menggunakan `->paginate(50)` |
| **Chunking** | Export dan rekap data besar menggunakan `->chunk(500)` untuk mencegah OOM |
| **Lazy Loading UI** | Livewire component berat (GL, laporan) menggunakan atribut `lazy` |
| **Queue Export** | Job export Excel dijalankan async via Queue dengan timeout 300 detik |
| **CSS Build** | Tailwind v4 otomatis purge class yang tidak dipakai saat `npm run build` |

### 6.3 User Interface

- Dibangun dengan **Livewire 4 + Tailwind CSS v4 + Alpine.js** (bundled).
- Responsif — nyaman digunakan di PC maupun perangkat mobile.
- Loading state pada setiap aksi async menggunakan `wire:loading`.
- Tanpa Bootstrap — 100% Tailwind untuk menjaga bundle minimal.
- Dark mode didukung via CSS variable Tailwind.

---

## 7. Struktur Folder Utama

```
app/
├── Enums/              # RoleEnum, TipeTransaksiEnum, StatusEnum
├── Events/             # TransaksiPosted, dll.
├── Exports/            # LaporanKeuanganExport, dll.
├── Http/
│   ├── Controllers/    # Controller tipis — validasi & delegate
│   ├── Livewire/       # Komponen UI reaktif
│   │   ├── Transaksi/
│   │   ├── Laporan/
│   │   └── GeneralLedger/
│   └── Requests/       # FormRequest per modul
├── Imports/            # Import data via Excel
├── Jobs/               # GenerateLaporanExport, dll.
├── Listeners/          # UpdateGlListener, dll.
├── Models/             # Eloquent models
├── Observers/          # TransaksiObserver, CoaObserver, dll.
├── Policies/           # Policy per model untuk otorisasi
├── Repositories/       # Abstraksi query Eloquent
└── Services/           # Business logic akuntansi

resources/
├── css/
│   └── app.css         # @import "tailwindcss" + @theme + @layer components
├── js/
│   └── app.js          # Entry point JS (Alpine bundled via Livewire)
└── views/
    ├── components/     # Blade components reusable (btn, card, input)
    ├── layouts/        # Layout utama aplikasi
    └── livewire/       # View untuk tiap Livewire component
```

---

## 8. Dependensi Lengkap

### Composer (PHP)

```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^13.0",
    "laravel/sanctum": "^4.0",
    "livewire/livewire": "^4.2",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-activitylog": "^4.0",
    "maatwebsite/excel": "^3.1"
  },
  "require-dev": {
    "laravel/pint": "^1.0",
    "pestphp/pest": "^3.0",
    "pestphp/pest-plugin-livewire": "^3.0"
  }
}
```

### NPM (Frontend)

```json
{
  "devDependencies": {
    "vite": "^6.0",
    "laravel-vite-plugin": "^1.0",
    "tailwindcss": "^4.2.4",
    "@tailwindcss/vite": "^4.2.4",
    "@tailwindcss/forms": "^0.5"
  }
}
```

---

## 9. Perubahan dari Versi Sebelumnya (v1.0)

| Aspek | v1.0 (Lama) | v2.0 (Baru) |
|---|---|---|
| Laravel | 12.0 | 13.0 (13.5.x) |
| PHP Minimum | 8.2 | 8.3 (wajib) |
| Admin UI | Filament v4 | Livewire 4 + Blade |
| CSS | Filament / Bootstrap | Tailwind CSS v4 (pure) |
| Bootstrap | Ya | **Dihapus** |
| Export PDF | Tersedia | **Dihapus** |
| Export Excel | Ya | Ya (via Queue untuk file besar) |
| RBAC | RoleEnum custom | spatie/laravel-permission |
| Audit Trail | — | spatie/laravel-activitylog |
| Testing | — | Pest v3 + pest-plugin-livewire |
| CSRF | Token-based | PreventRequestForgery (Laravel 13) |
