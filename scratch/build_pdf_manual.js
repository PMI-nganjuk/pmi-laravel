import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const baseDir = path.join(__dirname, '..');
const imagesDir = path.join(baseDir, 'public/manual-book/images');
const pdfOutputPath = path.join(baseDir, 'public/manual-book/Manual_Book_PMI_Nganjuk.pdf');
const pdfRootPath = path.join(baseDir, 'Manual_Book_PMI_Nganjuk.pdf');

function getBase64Image(filename) {
    const filePath = path.join(imagesDir, filename);
    if (!fs.existsSync(filePath)) return '';
    const fileBuffer = fs.readFileSync(filePath);
    return `data:image/png;base64,${fileBuffer.toString('base64')}`;
}

const ssLogin = getBase64Image('ss_login.png');
const ssDashboard = getBase64Image('ss_dashboard.png');
const ssSidebar = getBase64Image('ss_sidebar.png');
const ssReceipts = getBase64Image('ss_receipts.png');
const ssDisbursements = getBase64Image('ss_disbursements.png');
const ssAdjusting = getBase64Image('ss_adjusting_entries.png');
const ssCoa = getBase64Image('ss_coa.png');
const ssGeneralLedger = getBase64Image('ss_general_ledger.png');
const ssProfitLoss = getBase64Image('ss_profit_loss.png');
const ssBalanceSheet = getBase64Image('ss_balance_sheet.png');
const ssCashFlow = getBase64Image('ss_cash_flow.png');
const ssAnalysisNotes = getBase64Image('ss_analysis_notes.png');
const ssUsers = getBase64Image('ss_users.png');
const ssSettings = getBase64Image('ss_settings.png');
const ssPrograms = getBase64Image('ss_programs.png');
const ssProfile = getBase64Image('ss_profile.png');

const htmlContent = `
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Panduan Pengguna - Sistem Informasi Keuangan PMI Kabupaten Nganjuk</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #0f172a;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .cover {
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            page-break-after: always;
        }
        .cover-org {
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .cover-title {
            font-size: 26px;
            font-weight: 800;
            color: #b91c1c;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        .cover-subtitle {
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 35px;
        }
        .doc-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 16px 24px;
            text-align: left;
            width: 80%;
            margin-bottom: 40px;
            font-size: 12px;
        }
        .doc-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .doc-card td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .cover-meta {
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            width: 85%;
        }
        h1 {
            font-size: 18px;
            color: #991b1b;
            border-bottom: 2px solid #fee2e2;
            padding-bottom: 6px;
            margin-top: 26px;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            page-break-after: avoid;
        }
        h2 {
            font-size: 14px;
            color: #1e293b;
            margin-top: 18px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }
        p, li {
            font-size: 12px;
            color: #334155;
            text-align: justify;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 11px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
        }
        .step-list {
            list-style: none;
            padding-left: 0;
            margin: 10px 0;
        }
        .step-list li {
            position: relative;
            padding-left: 28px;
            margin-bottom: 10px;
        }
        .step-num {
            position: absolute;
            left: 0;
            top: 2px;
            width: 18px;
            height: 18px;
            background-color: #b91c1c;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-container {
            text-align: center;
            margin: 14px 0 20px 0;
            page-break-inside: avoid;
        }
        .img-container img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #cbd5e1;
        }
        .img-caption {
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
            font-weight: 600;
        }
        .note-box {
            background-color: #f8fafc;
            border-left: 4px solid #0284c7;
            padding: 10px 14px;
            margin: 12px 0;
            font-size: 11px;
        }
        .page-break {
            page-break-after: always;
        }

        /* TOC Styles */
        .toc-title {
            font-size: 18px;
            font-weight: 800;
            color: #991b1b;
            text-transform: uppercase;
            border-bottom: 2px solid #fee2e2;
            padding-bottom: 6px;
            margin-top: 10px;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .toc-chapter {
            font-weight: 700;
            font-size: 12px;
            color: #0f172a;
            margin-top: 12px;
            margin-bottom: 4px;
            display: flex;
            align-items: baseline;
        }
        .toc-sub {
            font-weight: 500;
            font-size: 11px;
            color: #334155;
            padding-left: 16px;
            margin-bottom: 4px;
            display: flex;
            align-items: baseline;
        }
        .toc-dots {
            flex-grow: 1;
            border-bottom: 1px dotted #cbd5e1;
            margin: 0 8px;
            position: relative;
            top: -3px;
        }
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div class="cover">
        <div class="cover-org">PALANG MERAH INDONESIA KABUPATEN NGANJUK</div>
        <div class="cover-title">BUKU PANDUAN PENGGUNA<br>(USER MANUAL DOCUMENTATION)</div>
        <div class="cover-subtitle">SISTEM INFORMASI MANAJEMEN KEUANGAN & PELAPORAN TERPADU</div>

        <div class="doc-card">
            <table>
                <tr>
                    <td style="width: 35%;"><strong>Kode Dokumen</strong></td>
                    <td>: MAN-PMI-FIN-2026-001</td>
                </tr>
                <tr>
                    <td><strong>Versi Dokumen</strong></td>
                    <td>: 1.0.0 (Enterprise Release)</td>
                </tr>
                <tr>
                    <td><strong>Status Otorisasi</strong></td>
                    <td>: Diterbitkan / Resmi</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Efektif</strong></td>
                    <td>: 4 Agustus 2026</td>
                </tr>
                <tr>
                    <td><strong>Pemilik Dokumen</strong></td>
                    <td>: Pengurus & Pengelola Keuangan PMI Nganjuk</td>
                </tr>
            </table>
        </div>

        <div class="cover-meta">
            Hak Cipta © 2026 Palang Merah Indonesia Kabupaten Nganjuk. Seluruh hak cipta dilindungi undang-undang.<br>
            Dilarang menggandakan atau menyebarluaskan dokumen ini tanpa izin tertulis dari Pengurus PMI Nganjuk.
        </div>
    </div>

    <!-- Halaman Daftar Isi -->
    <div class="page-break">
        <div class="toc-title">DAFTAR ISI</div>
        <div>
            <div class="toc-chapter">
                <span>BAB I: PENDAHULUAN</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>1.1 Maksud dan Tujuan Dokumen</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>1.2 Ruang Lingkup Sistem</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>1.3 Matriks Otoritas Peran (Role-Based Access Control)</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB II: AUTENTIKASI & STRUKTUR NAVIGASI</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>2.1 Autentikasi Pengguna (Halaman Login)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>2.2 Struktur Navigasi Utama (Sidebar Menu)</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB III: MODUL EXECUTIVE DASHBOARD</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>3.1 Ikhtisar Dashboard & Telemetri Realtime</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>3.2 Rincian Indikator & Telemetri Sistem</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB IV: OPERASIONAL MODUL TRANSAKSI KEUANGAN</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>4.1 Pencatatan Transaksi Penerimaan Kas (BKMUDD)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>4.2 Pencatatan Transaksi Pengeluaran Kas (BKKUDD)</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB V: MODUL BAGAN AKUN & JURNAL PENYESUAIAN</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>5.1 Pengelolaan Chart of Accounts (COA)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>5.2 Modul Jurnal Penyesuaian (BKJUDD)</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB VI: MODUL LAPORAN KEUANGAN TERPADU</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>6.1 Laporan Buku Besar (General Ledger)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>6.2 Laporan Laba Rugi (Profit & Loss Statement)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>6.3 Laporan Posisi Keuangan (Balance Sheet / Neraca)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>6.4 Laporan Arus Kas (Cash Flow Statement)</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>6.5 Laporan Perubahan Aset Netto (Statement of Changes in Net Assets)</span><span class="toc-dots"></span></div>

            <div class="toc-chapter">
                <span>BAB VII: MODUL ADMINISTRASI SISTEM & PROFIL</span>
                <span class="toc-dots"></span>
            </div>
            <div class="toc-sub"><span>7.1 Manajemen Akun Pengguna</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>7.2 Konfigurasi Parameter Profil Organisasi</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>7.3 Manajemen Master Program Kerja</span><span class="toc-dots"></span></div>
            <div class="toc-sub"><span>7.4 Pengaturan Profil Pengguna (Profil Saya)</span><span class="toc-dots"></span></div>
        </div>
    </div>

    <!-- BAB 1: PENDAHULUAN -->
    <div class="page-break">
        <h1>BAB I: PENDAHULUAN</h1>
        
        <h2>1.1 Maksud dan Tujuan Dokumen</h2>
        <p>Dokumen Buku Panduan Pengguna (User Manual) ini disusun sebagai pedoman standar operasional penggunaan Sistem Informasi Manajemen Keuangan Palang Merah Indonesia (PMI) Kabupaten Nganjuk. Dokumen ini bertujuan untuk memberikan petunjuk teknis pelaksanaan pencatatan transaksi, pengalokasian anggaran, penyusunan jurnal akuntansi, hingga pencetakan laporan keuangan terpadu.</p>

        <h2>1.2 Ruang Lingkup Sistem</h2>
        <p>Sistem Informasi Manajemen Keuangan mencakup beberapa fungsi utama bisnis organisasi, antara lain:</p>
        <ul>
            <li><strong>Autentikasi & Keamanan Sistem:</strong> Otentikasi terenkripsi berbasis sesi dan proteksi header keamanan HTTP.</li>
            <li><strong>Executive Dashboard & Telemetri Realtime:</strong> Pengawasan performa server, sesi pengguna aktif, dan saldo kas terintegrasi.</li>
            <li><strong>Manajemen Transaksi Kas:</strong> Pencatatan Kas Masuk (BKMUDD), Kas Keluar (BKKUDD), dan Jurnal Penyesuaian (BKJUDD).</li>
            <li><strong>Pelaporan Akuntansi Standar:</strong> Penyusunan otomatis Laporan Buku Besar, Laba Rugi, Posisi Keuangan (Neraca), Arus Kas, dan Perubahan Aset Netto.</li>
            <li><strong>Administrasi & Otoritas Peran:</strong> Manajemen pengguna berbasis Role-Based Access Control (RBAC).</li>
        </ul>

        <h2>1.3 Matriks Otoritas Peran (Role-Based Access Control)</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Peran Pengguna (Role)</th>
                    <th>Hak Akses Operasional Modul</th>
                    <th>Wewenang Dokumen</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Administrator (Admin)</strong></td>
                    <td>Akses penuh pada seluruh modul sistem, Manajemen Pengguna, dan Pengaturan Profil Organisasi.</td>
                    <td>Create, Read, Update, Delete, System Override</td>
                </tr>
                <tr>
                    <td><strong>Staf Keuangan (Finance Staff)</strong></td>
                    <td>Akses modul Transaksi Kas (Penerimaan, Pengeluaran, Penyesuaian), COA, dan Ekspor Laporan Keuangan.</td>
                    <td>Create, Read, Update (Terbatas)</td>
                </tr>
                <tr>
                    <td><strong>Karyawan & Pengguna Umum</strong></td>
                    <td>Akses baca (Read-only) pada modul laporan keuangan dan ikhtisar informasi publik organisasi.</td>
                    <td>Read-Only</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- BAB 2: AUTENTIKASI & STRUKTUR NAVIGASI -->
    <div class="page-break">
        <h1>BAB II: AUTENTIKASI & NAVIGASI SISTEM</h1>

        <h2>2.1 Autentikasi Pengguna (Halaman Login)</h2>
        <p>Seluruh pengguna wajib melakukan autentikasi kredensial melalui antarmuka login terenkripsi sebelum dapat mengakses data aplikasi.</p>

        <div class="img-container">
            <img src="${ssLogin}" alt="Autentikasi Login">
            <div class="img-caption">Gambar 2.1: Antarmuka Autentikasi Login Sistem</div>
        </div>

        <ul class="step-list">
            <li>
                <div class="step-num">1</div>
                <strong>Input Kredensial Email:</strong> Masukkan alamat email resmi yang terdaftar pada bidang input <code>Email Address</code>.
            </li>
            <li>
                <div class="step-num">2</div>
                <strong>Input Kata Sandi:</strong> Masukkan kata sandi rahasia akun pengguna pada bidang <code>Password</code>.
            </li>
            <li>
                <div class="step-num">3</div>
                <strong>Eksekusi Autentikasi:</strong> Klik tombol <strong>"Masuk"</strong>. Sistem akan mengotentikasi kredensial dan menerbitkan cookie sesi aktif.
            </li>
        </ul>

        <h2>2.2 Struktur Navigasi Utama (Sidebar Menu)</h2>
        <p>Menu navigasi sidebar kiri tersusun secara sistematis untuk mempermudah perpindahan antar modul aplikasi.</p>

        <div class="img-container">
            <img src="${ssSidebar}" alt="Sidebar Navigasi" style="max-height: 450px;">
            <div class="img-caption">Gambar 2.2: Modul Sidebar Navigasi Utama</div>
        </div>

        <ul class="step-list">
            <li>
                <div class="step-num">1</div>
                <strong>Brand Header:</strong> Identitas nama instansi Palang Merah Indonesia Kabupaten Nganjuk.
            </li>
            <li>
                <div class="step-num">2</div>
                <strong>Menu Utama (Dashboard):</strong> Navigasi menuju halaman executive dashboard & telemetri.
            </li>
            <li>
                <div class="step-num">3</div>
                <strong>Modul Input Keuangan:</strong> Sub-menu pencatatan transaksi kas masuk, kas keluar, penyesuaian, dan COA.
            </li>
            <li>
                <div class="step-num">4</div>
                <strong>Modul Laporan Keuangan:</strong> Sub-menu penyajian Buku Besar, Laba Rugi, Neraca, Arus Kas, dan Aset Netto.
            </li>
            <li>
                <div class="step-num">5</div>
                <strong>Modul Manajemen Organisasi:</strong> Sub-menu pengelolaan akun pengguna dan konfigurasi parameter sistem.
            </li>
            <li>
                <div class="step-num">6</div>
                <strong>Modul Unduh Panduan:</strong> Tautan cepat pengunduhan berkas Buku Panduan Pengguna (PDF).
            </li>
        </ul>
    </div>

    <!-- BAB 3: EXECUTIVE DASHBOARD -->
    <div class="page-break">
        <h1>BAB III: MODUL EXECUTIVE DASHBOARD</h1>

        <h2>3.1 Ikhtisar Dashboard & Telemetri Realtime</h2>
        <p>Halaman Dashboard menyajikan indikator kinerja utama (KPI) keuangan serta telemetri performa sistem secara seketika (realtime).</p>

        <div class="img-container">
            <img src="${ssDashboard}" alt="Executive Dashboard">
            <div class="img-caption">Gambar 3.1: Antarmuka Executive Dashboard (Tampilan Rasio Aspect 16:9)</div>
        </div>

        <h2>3.2 Rincian Indikator Dashboard</h2>
        <ul class="step-list">
            <li>
                <div class="step-num">1</div>
                <strong>Kartu Bento Statistik & Telemetri:</strong>
                <ul>
                    <li><strong>Total Pengguna Terdaftar:</strong> Agregasi jumlah akun staf yang memiliki otorisasi sistem.</li>
                    <li><strong>Sesi Pengguna Aktif:</strong> Jumlah sesi koneksi pengguna yang aktif terdeteksi pada database realtime.</li>
                    <li><strong>Beban CPU Server:</strong> Indikator beban penggunaan daya prosesor server (Telemetri Realtime).</li>
                </ul>
            </li>
            <li>
                <div class="step-num">2</div>
                <strong>Grafik Tren Keuangan 6 Bulan:</strong> Visualisasi analisis perbandingan akumulasi Penerimaan Kas (Debit) vs Pengeluaran Kas (Kredit) selama periode 6 bulan terakhir.
            </li>
            <li>
                <div class="step-num">3</div>
                <strong>Tabel Otoritas Peran:</strong> Ringkasan definisi kebijakan keamanan Role-Based Access Control (RBAC).
            </li>
        </ul>
    </div>

    <!-- BAB 4: MODUL TRANSAKSI KEUANGAN -->
    <div class="page-break">
        <h1>BAB IV: OPERASIONAL MODUL TRANSAKSI KEUANGAN</h1>

        <h2>4.1 Pencatatan Transaksi Penerimaan Kas (BKMUDD)</h2>
        <p>Modul Penerimaan Kas digunakan untuk menginput data pendapatan, pencairan donasi, dan hibah yang masuk ke akun kas/bank PMI Nganjuk.</p>

        <div class="img-container">
            <img src="${ssReceipts}" alt="Form Penerimaan Kas">
            <div class="img-caption">Gambar 4.1: Formulir & Daftar Transaksi Penerimaan Kas</div>
        </div>

        <ul class="step-list">
            <li>
                <div class="step-num">1</div>
                <strong>Nomor Dokumen:</strong> Otomatis dikodekan oleh sistem sesuai penomoran registrasi resmi (Format: <code>BKMUDD/YYYY/MM/...</code>).
            </li>
            <li>
                <div class="step-num">2</div>
                <strong>Tanggal Transaksi:</strong> Tentukan tanggal efektif valuta penerimaan kas.
            </li>
            <li>
                <div class="step-num">3</div>
                <strong>Pemilihan Akun Kas & Lawan:</strong> Pilih akun Kas/Bank penerima serta akun pendapatan sumber dana.
            </li>
            <li>
                <div class="step-num">4</div>
                <strong>Penyimpanan:</strong> Klik tombol <strong>"Simpan Transaksi"</strong>. Sistem akan menerbitkan entri Jurnal Umum dan memperbarui saldo kas.
            </li>
        </ul>

        <h2>4.2 Pencatatan Transaksi Pengeluaran Kas (BKKUDD)</h2>
        <p>Modul Pengeluaran Kas digunakan untuk mencatat alokasi beban belanja operasional, program kerja, dan biaya penyediaan kantong darah.</p>

        <div class="img-container">
            <img src="${ssDisbursements}" alt="Form Pengeluaran Kas">
            <div class="img-caption">Gambar 4.2: Formulir Pengeluaran Kas</div>
        </div>
    </div>

    <!-- BAB 5: JURNAL & BAGAN AKUN -->
    <div class="page-break">
        <h1>BAB V: MODUL BAGAN AKUN & JURNAL PENYESUAIAN</h1>

        <h2>5.1 Pengelolaan Chart of Accounts (COA)</h2>
        <p>Modul COA menyajikan susunan hierarki bagan akun akuntansi standar yang digunakan dalam pengkodean transaksi keuangan organisasi.</p>

        <div class="img-container">
            <img src="${ssCoa}" alt="Chart of Accounts">
            <div class="img-caption">Gambar 5.1: Tabel Kode Bagan Akun Standar (COA)</div>
        </div>

        <h2>5.2 Modul Jurnal Penyesuaian (BKJUDD)</h2>
        <p>Digunakan untuk melakukan penyesuaian saldo akun akhir periode akuntansi, penyusunan amortisasi, dan depresiasi aset tetap.</p>

        <div class="img-container">
            <img src="${ssAdjusting}" alt="Jurnal Penyesuaian">
            <div class="img-caption">Gambar 5.2: Antarmuka Modul Jurnal Penyesuaian</div>
        </div>
    </div>

    <!-- BAB 6: LAPORAN KEUANGAN -->
    <div class="page-break">
        <h1>BAB VI: MODUL LAPORAN KEUANGAN TERPADU</h1>

        <h2>6.1 Laporan Buku Besar (General Ledger)</h2>
        <p>Penyajian rincian mutasi transaksi debet dan kredit secara kronologis untuk setiap kode rekening akun akuntansi.</p>
        <div class="img-container">
            <img src="${ssGeneralLedger}" alt="Laporan Buku Besar">
            <div class="img-caption">Gambar 6.1: Laporan Buku Besar</div>
        </div>

        <h2>6.2 Laporan Laba Rugi (Profit & Loss Statement)</h2>
        <p>Penyajian kinerja keuangan operasional organisasi yang membandingkan total pendapatan dengan total beban operasional.</p>
        <div class="img-container">
            <img src="${ssProfitLoss}" alt="Laporan Laba Rugi">
            <div class="img-caption">Gambar 6.2: Laporan Laba Rugi</div>
        </div>

        <h2>6.3 Laporan Posisi Keuangan (Balance Sheet / Neraca)</h2>
        <p>Menampilkan posisi keseimbangan aset, kewajiban (liabilitas), dan ekuitas (aset netto) organisasi pada periode tertentu.</p>
        <div class="img-container">
            <img src="${ssBalanceSheet}" alt="Laporan Posisi Keuangan">
            <div class="img-caption">Gambar 6.3: Laporan Posisi Keuangan (Neraca)</div>
        </div>

        <h2>6.4 Laporan Arus Kas (Cash Flow Statement)</h2>
        <p>Modul Laporan Arus Kas menyajikan informasi aliran kas masuk (penerimaan) dan kas keluar (pengeluaran) organisasi Palang Merah Indonesia Kabupaten Nganjuk yang diklasifikasikan ke dalam 3 (tiga) kategori aktivitas utama akuntansi:</p>
        <ul>
            <li><strong>Aktivitas Operasi:</strong> Mencakup penerimaan dari penyumbang/donatur, penerimaan bunga, penerimaan jasa pengolahan darah, pembayaran kepada pemasok/penerima hibah, serta beban gaji & kesejahteraan pegawai.</li>
            <li><strong>Aktivitas Investasi:</strong> Mencakup arus kas terkait perolehan atau pelepasan aset tetap dan investasi jangka panjang.</li>
            <li><strong>Aktivitas Pendanaan:</strong> Mencakup penerimaan dan penolakan/pembayaran kembali kewajiban utang antar lembaga atau pinjaman operasional.</li>
        </ul>
        <p>Di bagian akhir laporan disajikan perhitungan <em>Kenaikan/Penurunan Bersih Kas</em>, <em>Saldo Kas Awal Tahun</em>, serta <em>Saldo Kas Akhir Tahun</em> yang secara otomatis terekonsiliasi dan cocok dengan saldo kas pada Laporan Posisi Keuangan (Neraca). Pengguna juga dapat memilih filter tahun anggaran dan mengunduh berkas fisik dalam format Excel (<code>.xlsx</code>).</p>
        <div class="img-container">
            <img src="${ssCashFlow}" alt="Laporan Arus Kas">
            <div class="img-caption">Gambar 6.4: Antarmuka Laporan Arus Kas (Cash Flow Statement)</div>
        </div>

        <h2>6.5 Laporan Perubahan Aset Netto (Statement of Changes in Net Assets / Netto)</h2>
        <p>Modul Laporan Perubahan Aset Netto menyajikan rekapitulasi mutasi dan perubahan saldo ekuitas organisasi nirlaba sesuai dengan standar pelaporan akuntansi entitas nirlaba (ISAK 35 / PSAK 45). Laporan ini terbagi menjadi dua klasifikasi utama aset netto:</p>
        <ul>
            <li><strong>Aset Netto Tidak Terikat:</strong> Sumber daya yang penggunaannya tidak dibatasi oleh donor/pemberi hibah. Kenaikan/penurunan berasal dari surplus/defisit operasional berjalan.</li>
            <li><strong>Aset Netto Terikat:</strong> Sumber daya yang penggunaannya dibatasi oleh donor untuk tujuan atau periode waktu tertentu.</li>
        </ul>
        <p>Laporan ini menampilkan <em>Saldo Aset Netto Awal Tahun</em>, <em>Pendapatan Netto / Perubahan Periode Berjalan</em>, serta <em>Saldo Aset Netto Akhir Tahun</em> yang terintegrasi secara otomatis dengan pos Ekuitas (Aset Netto) pada Laporan Posisi Keuangan (Neraca). Dilengkapi dengan fitur penyesuaian tahun laporan dan unduh dokumen spreadsheet (<code>.xlsx</code>).</p>
        <div class="img-container">
            <img src="${ssAnalysisNotes}" alt="Laporan Perubahan Aset Netto">
            <div class="img-caption">Gambar 6.5: Antarmuka Laporan Perubahan Aset Netto</div>
        </div>
    </div>

    <!-- BAB 7: ADMINISTRASI SISTEM -->
    <div class="page-break">
        <h1>BAB VII: MODUL ADMINISTRASI SISTEM</h1>

        <h2>7.1 Manajemen Akun Pengguna</h2>
        <p>Fasilitas pengelolaan otorisasi pengguna untuk pendaftaran akun staf baru, pembaruan peranan (Role), dan pemulihan akses.</p>
        <div class="img-container">
            <img src="${ssUsers}" alt="Manajemen Akun Pengguna">
            <div class="img-caption">Gambar 7.1: Antarmuka Manajemen Pengguna</div>
        </div>

        <h2>7.2 Konfigurasi Parameter Profil Organisasi</h2>
        <p>Pengaturan entitas resmi Palang Merah Indonesia Kabupaten Nganjuk, konfigurasi penandatangan laporan, dan identitas instansi.</p>
        <div class="img-container">
            <img src="${ssSettings}" alt="Konfigurasi Profil Organisasi">
            <div class="img-caption">Gambar 7.2: Pengaturan Profil Organisasi</div>
        </div>

        <h2>7.3 Manajemen Master Program Kerja</h2>
        <p>Modul Manajemen Program Kerja digunakan untuk mendata seluruh perencanaan kegiatan dan program operasional PMI Kabupaten Nganjuk beserta Person In Charge (PIC) pelaksana. Administrator dapat menginput program kerja baru, memperbarui informasi PIC, dan mengelola direktori program secara terpusat.</p>
        <div class="img-container">
            <img src="${ssPrograms}" alt="Manajemen Program Kerja">
            <div class="img-caption">Gambar 7.3: Antarmuka Kelola Master Program Kerja</div>
        </div>

        <h2>7.4 Pengaturan Profil Pengguna (Profil Saya)</h2>
        <p>Modul Profil Saya memungkinkan setiap pengguna yang sedang aktif (login) untuk mengelola data akun pribadi secara mandiri. Pengguna dapat memperbarui Nama Lengkap, Alamat Email terdaftar, serta melakukan penggantian Kata Sandi (Password) secara berkala demi menjaga keamanan akun.</p>
        <div class="img-container">
            <img src="${ssProfile}" alt="Pengaturan Profil Pengguna">
            <div class="img-caption">Gambar 7.4: Antarmuka Pengaturan Profil Saya & Keamanan Akun</div>
        </div>

        <div class="note-box">
            <strong>Dukungan Teknis & Layanan Bantuan:</strong><br>
            Apabila menemukan kendala operasional atau gangguan keamanan pada sistem, harap segera menghubungi Tim Administrator Pengelola Sistem Keuangan PMI Kabupaten Nganjuk melalui Sekretariat Pengurus PMI Nganjuk.
        </div>
    </div>

</body>
</html>
`;

async function generatePDF() {
    console.log('Generating Professional User Manual PDF...');
    const browser = await puppeteer.launch({
        executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        headless: 'new'
    });
    const page = await browser.newPage();
    await page.setContent(htmlContent, { waitUntil: 'networkidle0' });

    await page.pdf({
        path: pdfOutputPath,
        format: 'A4',
        printBackground: true,
        margin: { top: '16mm', right: '14mm', bottom: '16mm', left: '14mm' }
    });

    fs.copyFileSync(pdfOutputPath, pdfRootPath);

    await browser.close();
    console.log(`Professional PDF Manual Book generated successfully at:\n - ${pdfOutputPath}\n - ${pdfRootPath}`);
}

generatePDF().catch(err => {
    console.error('Error generating PDF:', err);
    process.exit(1);
});
