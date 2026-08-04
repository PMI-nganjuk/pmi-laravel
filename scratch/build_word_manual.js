import {
    Document,
    Packer,
    Paragraph,
    TextRun,
    Table,
    TableRow,
    TableCell,
    ImageRun,
    HeadingLevel,
    AlignmentType,
    BorderStyle,
    WidthType,
    ShadingType,
    PageBreak,
    Header,
    Footer,
    PageNumber
} from 'docx';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const baseDir = path.join(__dirname, '..');
const imagesDir = path.join(baseDir, 'public/manual-book/images');
const wordOutputPath = path.join(baseDir, 'public/manual-book/Manual_Book_PMI_Nganjuk.docx');
const wordRootPath = path.join(baseDir, 'Manual_Book_PMI_Nganjuk.docx');

function getImageBuffer(filename) {
    const filePath = path.join(imagesDir, filename);
    if (!fs.existsSync(filePath)) return null;
    return fs.readFileSync(filePath);
}

function createImageParagraph(buffer, width = 500, height = 281, captionText = '') {
    if (!buffer) return new Paragraph({ text: '[Gambar Tidak Ditemukan]' });

    const children = [
        new Paragraph({
            alignment: AlignmentType.CENTER,
            space: { before: 200, after: 100 },
            children: [
                new ImageRun({
                    data: buffer,
                    transformation: { width, height }
                })
            ]
        })
    ];

    if (captionText) {
        children.push(
            new Paragraph({
                alignment: AlignmentType.CENTER,
                space: { before: 50, after: 200 },
                children: [
                    new TextRun({
                        text: captionText,
                        italic: true,
                        size: 18, // 9pt
                        color: "64748B"
                    })
                ]
            })
        );
    }

    return children;
}

function createHeading1(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_1,
        space: { before: 360, after: 140 },
        children: [
            new TextRun({
                text: text,
                bold: true,
                size: 28, // 14pt
                color: "991B1B"
            })
        ]
    });
}

function createHeading2(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_2,
        space: { before: 240, after: 100 },
        children: [
            new TextRun({
                text: text,
                bold: true,
                size: 24, // 12pt
                color: "1E293B"
            })
        ]
    });
}

function createBodyParagraph(text, isBold = false) {
    return new Paragraph({
        space: { before: 60, after: 100 },
        lineSpacing: { line: 360 }, // 1.5 line spacing
        children: [
            new TextRun({
                text: text,
                bold: isBold,
                size: 22, // 11pt
                color: "334155"
            })
        ]
    });
}

function createBulletItem(boldText, normalText) {
    return new Paragraph({
        bullet: { level: 0 },
        space: { before: 40, after: 60 },
        children: [
            new TextRun({
                text: boldText + " ",
                bold: true,
                size: 22,
                color: "0F172A"
            }),
            new TextRun({
                text: normalText,
                size: 22,
                color: "334155"
            })
        ]
    });
}

async function buildWordDoc() {
    console.log('Generating Word (.docx) Manual Book...');

    const doc = new Document({
        sections: [
            {
                properties: {
                    page: {
                        margin: {
                            top: 1440,    // 1 inch
                            bottom: 1440,
                            left: 1440,
                            right: 1440
                        }
                    }
                },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                children: [
                                    new TextRun({
                                        text: "Manual Book - Sistem Informasi Keuangan PMI Nganjuk",
                                        size: 16,
                                        color: "94A3B8"
                                    })
                                ]
                            })
                        ]
                    })
                },
                footers: {
                    default: new Footer({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.CENTER,
                                children: [
                                    new TextRun({
                                        text: "Palang Merah Indonesia Kabupaten Nganjuk | Halaman ",
                                        size: 18,
                                        color: "64748B"
                                    }),
                                    new TextRun({
                                        children: [PageNumber.CURRENT],
                                        size: 18,
                                        bold: true,
                                        color: "991B1B"
                                    })
                                ]
                            })
                        ]
                    })
                },
                children: [
                    // ── COVER PAGE ──────────────────────────────────────────────
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        space: { before: 720, after: 200 },
                        children: [
                            new TextRun({
                                text: "PALANG MERAH INDONESIA KABUPATEN NGANJUK",
                                bold: true,
                                size: 24,
                                color: "64748B"
                            })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        space: { before: 200, after: 100 },
                        children: [
                            new TextRun({
                                text: "BUKU PANDUAN PENGGUNA\n(USER MANUAL DOCUMENTATION)",
                                bold: true,
                                size: 36, // 18pt
                                color: "B91C1C"
                            })
                        ]
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        space: { before: 100, after: 720 },
                        children: [
                            new TextRun({
                                text: "SISTEM INFORMASI MANAJEMEN KEUANGAN & PELAPORAN TERPADU",
                                bold: true,
                                size: 24,
                                color: "334155"
                            })
                        ]
                    }),

                    // Metadata Card Table
                    new Table({
                        alignment: AlignmentType.CENTER,
                        width: { size: 90, type: WidthType.PERCENTAGE },
                        rows: [
                            new TableRow({
                                children: [
                                    new TableCell({
                                        width: { size: 40, type: WidthType.PERCENTAGE },
                                        children: [new Paragraph({ children: [new TextRun({ text: "Kode Dokumen", bold: true, size: 20 })] })]
                                    }),
                                    new TableCell({
                                        width: { size: 60, type: WidthType.PERCENTAGE },
                                        children: [new Paragraph({ children: [new TextRun({ text: ": MAN-PMI-FIN-2026-001", size: 20 })] })]
                                    })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: "Versi Dokumen", bold: true, size: 20 })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: ": 1.0.0 (Enterprise Release)", size: 20 })] })]
                                    })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: "Status Otorisasi", bold: true, size: 20 })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: ": Diterbitkan / Resmi", size: 20 })] })]
                                    })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: "Tanggal Efektif", bold: true, size: 20 })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: ": 4 Agustus 2026", size: 20 })] })]
                                    })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: "Pemilik Dokumen", bold: true, size: 20 })] })]
                                    }),
                                    new TableCell({
                                        children: [new Paragraph({ children: [new TextRun({ text: ": Pengurus & Pengelola Keuangan PMI Nganjuk", size: 20 })] })]
                                    })
                                ]
                            })
                        ]
                    }),

                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        space: { before: 1440, after: 0 },
                        children: [
                            new TextRun({
                                text: "Hak Cipta © 2026 Palang Merah Indonesia Kabupaten Nganjuk. Seluruh hak cipta dilindungi undang-undang.\nDilarang menggandakan atau menyebarluaskan dokumen ini tanpa izin tertulis dari Pengurus PMI Nganjuk.",
                                size: 18,
                                color: "64748B"
                            })
                        ]
                    }),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── DAFTAR ISI ─────────────────────────────────────────────
                    new Paragraph({
                        heading: HeadingLevel.HEADING_1,
                        space: { before: 200, after: 240 },
                        children: [
                            new TextRun({
                                text: "DAFTAR ISI",
                                bold: true,
                                size: 32,
                                color: "991B1B"
                            })
                        ]
                    }),

                    createBodyParagraph("BAB I: PENDAHULUAN", true),
                    createBulletItem("1.1", "Maksud dan Tujuan Dokumen"),
                    createBulletItem("1.2", "Ruang Lingkup Sistem"),
                    createBulletItem("1.3", "Matriks Otoritas Peran (Role-Based Access Control)"),

                    createBodyParagraph("BAB II: AUTENTIKASI & STRUKTUR NAVIGASI", true),
                    createBulletItem("2.1", "Autentikasi Pengguna (Halaman Login)"),
                    createBulletItem("2.2", "Struktur Navigasi Utama (Sidebar Menu)"),

                    createBodyParagraph("BAB III: MODUL EXECUTIVE DASHBOARD", true),
                    createBulletItem("3.1", "Ikhtisar Dashboard & Telemetri Realtime"),
                    createBulletItem("3.2", "Rincian Indikator & Telemetri Sistem"),

                    createBodyParagraph("BAB IV: OPERASIONAL MODUL TRANSAKSI KEUANGAN", true),
                    createBulletItem("4.1", "Pencatatan Transaksi Penerimaan Kas (BKMUDD)"),
                    createBulletItem("4.2", "Pencatatan Transaksi Pengeluaran Kas (BKKUDD)"),

                    createBodyParagraph("BAB V: MODUL BAGAN AKUN & JURNAL PENYESUAIAN", true),
                    createBulletItem("5.1", "Pengelolaan Chart of Accounts (COA)"),
                    createBulletItem("5.2", "Modul Jurnal Penyesuaian (BKJUDD)"),

                    createBodyParagraph("BAB VI: MODUL LAPORAN KEUANGAN TERPADU", true),
                    createBulletItem("6.1", "Laporan Buku Besar (General Ledger)"),
                    createBulletItem("6.2", "Laporan Laba Rugi (Profit & Loss Statement)"),
                    createBulletItem("6.3", "Laporan Posisi Keuangan (Balance Sheet / Neraca)"),
                    createBulletItem("6.4", "Laporan Arus Kas (Cash Flow Statement)"),
                    createBulletItem("6.5", "Laporan Perubahan Aset Netto (Statement of Changes in Net Assets)"),

                    createBodyParagraph("BAB VII: MODUL ADMINISTRASI SISTEM & PROFIL", true),
                    createBulletItem("7.1", "Manajemen Akun Pengguna"),
                    createBulletItem("7.2", "Konfigurasi Parameter Profil Organisasi"),
                    createBulletItem("7.3", "Manajemen Master Program Kerja"),
                    createBulletItem("7.4", "Pengaturan Profil Pengguna (Profil Saya)"),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB I: PENDAHULUAN ──────────────────────────────────────
                    createHeading1("BAB I: PENDAHULUAN"),
                    createHeading2("1.1 Maksud dan Tujuan Dokumen"),
                    createBodyParagraph("Dokumen Buku Panduan Pengguna (User Manual) ini disusun sebagai pedoman standar operasional penggunaan Sistem Informasi Manajemen Keuangan Palang Merah Indonesia (PMI) Kabupaten Nganjuk. Dokumen ini bertujuan untuk memberikan petunjuk teknis pelaksanaan pencatatan transaksi, pengalokasian anggaran, penyusunan jurnal akuntansi, hingga pencetakan laporan keuangan terpadu."),

                    createHeading2("1.2 Ruang Lingkup Sistem"),
                    createBodyParagraph("Sistem Informasi Manajemen Keuangan mencakup beberapa fungsi utama bisnis organisasi, antara lain:"),
                    createBulletItem("• Autentikasi & Keamanan Sistem:", "Otentikasi terenkripsi berbasis sesi dan proteksi header keamanan HTTP."),
                    createBulletItem("• Executive Dashboard & Telemetri Realtime:", "Pengawasan performa server, sesi pengguna aktif, dan saldo kas terintegrasi."),
                    createBulletItem("• Manajemen Transaksi Kas:", "Pencatatan Kas Masuk (BKMUDD), Kas Keluar (BKKUDD), dan Jurnal Penyesuaian (BKJUDD)."),
                    createBulletItem("• Pelaporan Akuntansi Standar:", "Penyusunan otomatis Laporan Buku Besar, Laba Rugi, Posisi Keuangan (Neraca), Arus Kas, dan Perubahan Aset Netto."),
                    createBulletItem("• Administrasi & Otoritas Peran:", "Manajemen pengguna berbasis Role-Based Access Control (RBAC)."),

                    createHeading2("1.3 Matriks Otoritas Peran (Role-Based Access Control)"),
                    new Table({
                        alignment: AlignmentType.CENTER,
                        width: { size: 100, type: WidthType.PERCENTAGE },
                        rows: [
                            new TableRow({
                                children: [
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Peran Pengguna (Role)", bold: true, size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Hak Akses Operasional Modul", bold: true, size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Wewenang Dokumen", bold: true, size: 20 })] })] })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Administrator (Admin)", bold: true, size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Akses penuh pada seluruh modul sistem, Manajemen Pengguna, dan Pengaturan Profil Organisasi.", size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Create, Read, Update, Delete, System Override", size: 20 })] })] })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Staf Keuangan (Finance Staff)", bold: true, size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Akses modul Transaksi Kas (Penerimaan, Pengeluaran, Penyesuaian), COA, dan Ekspor Laporan Keuangan.", size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Create, Read, Update (Terbatas)", size: 20 })] })] })
                                ]
                            }),
                            new TableRow({
                                children: [
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Karyawan & Pengguna Umum", bold: true, size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Akses baca (Read-only) pada modul laporan keuangan dan ikhtisar informasi publik organisasi.", size: 20 })] })] }),
                                    new TableCell({ children: [new Paragraph({ children: [new TextRun({ text: "Read-Only", size: 20 })] })] })
                                ]
                            })
                        ]
                    }),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB II: AUTENTIKASI & NAVIGASI ───────────────────────────
                    createHeading1("BAB II: AUTENTIKASI & STRUKTUR NAVIGASI SISTEM"),
                    createHeading2("2.1 Autentikasi Pengguna (Halaman Login)"),
                    createBodyParagraph("Seluruh pengguna wajib melakukan autentikasi kredensial melalui antarmuka login terenkripsi sebelum dapat mengakses data aplikasi."),
                    ...createImageParagraph(getImageBuffer('ss_login.png'), 480, 270, "Gambar 2.1: Antarmuka Autentikasi Login Sistem"),
                    createBulletItem("1.", "Input Kredensial Email: Masukkan alamat email resmi yang terdaftar pada bidang input Email Address."),
                    createBulletItem("2.", "Input Kata Sandi: Masukkan kata sandi rahasia akun pengguna pada bidang Password."),
                    createBulletItem("3.", "Eksekusi Autentikasi: Klik tombol 'Masuk'. Sistem akan mengotentikasi kredensial dan menerbitkan cookie sesi aktif."),

                    createHeading2("2.2 Struktur Navigasi Utama (Sidebar Menu)"),
                    createBodyParagraph("Menu navigasi sidebar kiri tersusun secara sistematis untuk mempermudah perpindahan antar modul aplikasi."),
                    ...createImageParagraph(getImageBuffer('ss_sidebar.png'), 250, 420, "Gambar 2.2: Modul Sidebar Navigasi Utama"),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB III: EXECUTIVE DASHBOARD ────────────────────────────
                    createHeading1("BAB III: MODUL EXECUTIVE DASHBOARD"),
                    createHeading2("3.1 Ikhtisar Dashboard & Telemetri Realtime"),
                    createBodyParagraph("Halaman Dashboard menyajikan indikator kinerja utama (KPI) keuangan serta telemetri performa sistem secara seketika (realtime)."),
                    ...createImageParagraph(getImageBuffer('ss_dashboard.png'), 500, 281, "Gambar 3.1: Antarmuka Executive Dashboard"),
                    createHeading2("3.2 Rincian Indikator Dashboard"),
                    createBulletItem("• Kartu Bento Statistik:", "Menampilkan Total Pengguna Terdaftar, Sesi Pengguna Aktif, dan Beban CPU Server (Telemetri Realtime)."),
                    createBulletItem("• Grafik Tren Keuangan 6 Bulan:", "Visualisasi perbandingan Penerimaan Kas (Debit) vs Pengeluaran Kas (Kredit)."),
                    createBulletItem("• Tabel Otoritas Peran:", "Ringkasan definisi kebijakan keamanan Role-Based Access Control (RBAC)."),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB IV: TRANSAKSI KEUANGAN ──────────────────────────────
                    createHeading1("BAB IV: OPERASIONAL MODUL TRANSAKSI KEUANGAN"),
                    createHeading2("4.1 Pencatatan Transaksi Penerimaan Kas (BKMUDD)"),
                    createBodyParagraph("Modul Penerimaan Kas digunakan untuk menginput data pendapatan, pencairan donasi, dan hibah yang masuk ke akun kas/bank PMI Nganjuk."),
                    ...createImageParagraph(getImageBuffer('ss_receipts.png'), 500, 281, "Gambar 4.1: Formulir & Daftar Transaksi Penerimaan Kas"),
                    createBulletItem("1.", "Nomor Dokumen: Otomatis dikodekan oleh sistem sesuai penomoran registrasi resmi (Format: BKMUDD/YYYY/MM/...)."),
                    createBulletItem("2.", "Tanggal Transaksi: Tentukan tanggal efektif valuta penerimaan kas."),
                    createBulletItem("3.", "Pemilihan Akun Kas & Lawan: Pilih akun Kas/Bank penerima serta akun pendapatan sumber dana."),
                    createBulletItem("4.", "Penyimpanan: Klik tombol 'Simpan Transaksi'. Sistem akan menerbitkan entri Jurnal Umum dan memperbarui saldo kas."),

                    createHeading2("4.2 Pencatatan Transaksi Pengeluaran Kas (BKKUDD)"),
                    createBodyParagraph("Modul Pengeluaran Kas digunakan untuk mencatat alokasi beban belanja operasional, program kerja, dan biaya penyediaan kantong darah."),
                    ...createImageParagraph(getImageBuffer('ss_disbursements.png'), 500, 281, "Gambar 4.2: Formulir Pengeluaran Kas"),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB V: BAGAN AKUN & PENYESUAIAN ─────────────────────────
                    createHeading1("BAB V: MODUL BAGAN AKUN & JURNAL PENYESUAIAN"),
                    createHeading2("5.1 Pengelolaan Chart of Accounts (COA)"),
                    createBodyParagraph("Modul COA menyajikan susunan hierarki bagan akun akuntansi standar yang digunakan dalam pengkodean transaksi keuangan organisasi."),
                    ...createImageParagraph(getImageBuffer('ss_coa.png'), 500, 281, "Gambar 5.1: Tabel Kode Bagan Akun Standar (COA)"),

                    createHeading2("5.2 Modul Jurnal Penyesuaian (BKJUDD)"),
                    createBodyParagraph("Digunakan untuk melakukan penyesuaian saldo akun akhir periode akuntansi, penyusunan amortisasi, dan depresiasi aset tetap."),
                    ...createImageParagraph(getImageBuffer('ss_adjusting_entries.png'), 500, 281, "Gambar 5.2: Antarmuka Modul Jurnal Penyesuaian"),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB VI: LAPORAN KEUANGAN ────────────────────────────────
                    createHeading1("BAB VI: MODUL LAPORAN KEUANGAN TERPADU"),
                    createHeading2("6.1 Laporan Buku Besar (General Ledger)"),
                    createBodyParagraph("Penyajikan rincian mutasi transaksi debet dan kredit secara kronologis untuk setiap kode rekening akun akuntansi."),
                    ...createImageParagraph(getImageBuffer('ss_general_ledger.png'), 500, 281, "Gambar 6.1: Laporan Buku Besar"),

                    createHeading2("6.2 Laporan Laba Rugi (Profit & Loss Statement)"),
                    createBodyParagraph("Penyajikan kinerja keuangan operasional organisasi yang membandingkan total pendapatan dengan total beban operasional."),
                    ...createImageParagraph(getImageBuffer('ss_profit_loss.png'), 500, 281, "Gambar 6.2: Laporan Laba Rugi"),

                    createHeading2("6.3 Laporan Posisi Keuangan (Balance Sheet / Neraca)"),
                    createBodyParagraph("Menampilkan posisi keseimbangan aset, kewajiban (liabilitas), dan ekuitas (aset netto) organisasi pada periode tertentu."),
                    ...createImageParagraph(getImageBuffer('ss_balance_sheet.png'), 500, 281, "Gambar 6.3: Laporan Posisi Keuangan (Neraca)"),

                    createHeading2("6.4 Laporan Arus Kas (Cash Flow Statement)"),
                    createBodyParagraph("Modul Laporan Arus Kas menyajikan informasi aliran kas masuk (penerimaan) dan kas keluar (pengeluaran) organisasi Palang Merah Indonesia Kabupaten Nganjuk yang diklasifikasikan ke dalam 3 (tiga) kategori aktivitas utama akuntansi: Aktivitas Operasi, Aktivitas Investasi, dan Aktivitas Pendanaan. Dilengkapi saldo kas awal & akhir tahun serta ekspor Excel (.xlsx)."),
                    ...createImageParagraph(getImageBuffer('ss_cash_flow.png'), 500, 281, "Gambar 6.4: Antarmuka Laporan Arus Kas (Cash Flow Statement)"),

                    createHeading2("6.5 Laporan Perubahan Aset Netto (Statement of Changes in Net Assets / Netto)"),
                    createBodyParagraph("Modul Laporan Perubahan Aset Netto menyajikan rekapitulasi mutasi dan perubahan saldo ekuitas organisasi nirlaba sesuai dengan standar pelaporan akuntansi entitas nirlaba (ISAK 35 / PSAK 45) untuk Aset Netto Tidak Terikat dan Aset Netto Terikat."),
                    ...createImageParagraph(getImageBuffer('ss_analysis_notes.png'), 500, 281, "Gambar 6.5: Antarmuka Laporan Perubahan Aset Netto"),

                    new Paragraph({ children: [new PageBreak()] }),

                    // ── BAB VII: ADMINISTRASI SISTEM ────────────────────────────
                    createHeading1("BAB VII: MODUL ADMINISTRASI SISTEM & PROFIL"),
                    createHeading2("7.1 Manajemen Akun Pengguna"),
                    createBodyParagraph("Fasilitas pengelolaan otorisasi pengguna untuk pendaftaran akun staf baru, pembaruan peranan (Role), dan pemulihan akses."),
                    ...createImageParagraph(getImageBuffer('ss_users.png'), 500, 281, "Gambar 7.1: Antarmuka Manajemen Pengguna"),

                    createHeading2("7.2 Konfigurasi Parameter Profil Organisasi"),
                    createBodyParagraph("Pengaturan entitas resmi Palang Merah Indonesia Kabupaten Nganjuk, konfigurasi penandatangan laporan, dan identitas instansi."),
                    ...createImageParagraph(getImageBuffer('ss_settings.png'), 500, 281, "Gambar 7.2: Pengaturan Profil Organisasi"),

                    createHeading2("7.3 Manajemen Master Program Kerja"),
                    createBodyParagraph("Modul Manajemen Program Kerja digunakan untuk mendata seluruh perencanaan kegiatan dan program operasional PMI Kabupaten Nganjuk beserta Person In Charge (PIC) pelaksana."),
                    ...createImageParagraph(getImageBuffer('ss_programs.png'), 500, 281, "Gambar 7.3: Antarmuka Kelola Master Program Kerja"),

                    createHeading2("7.4 Pengaturan Profil Pengguna (Profil Saya)"),
                    createBodyParagraph("Modul Profil Saya memungkinkan setiap pengguna yang sedang aktif (login) untuk mengelola data akun pribadi secara mandiri, seperti pembaruan Nama Lengkap, Email, dan Kata Sandi."),
                    ...createImageParagraph(getImageBuffer('ss_profile.png'), 500, 281, "Gambar 7.4: Antarmuka Pengaturan Profil Saya & Keamanan Akun")
                ]
            }
        ]
    });

    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(wordOutputPath, buffer);
    fs.writeFileSync(wordRootPath, buffer);

    console.log(`Professional Word Manual Book generated successfully at:\n - ${wordOutputPath}\n - ${wordRootPath}`);
}

buildWordDoc().catch(err => {
    console.error('Error generating Word document:', err);
    process.exit(1);
});
