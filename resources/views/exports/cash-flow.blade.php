@php
    $fmt = function($val) {
        if ($val == 0 || $val === null) return '-';
        if ($val < 0) return '(' . number_format(abs($val), 0, ',', '.') . ')';
        return number_format($val, 0, ',', '.');
    };
    $s = function($val) {
        return ($val == 0 || $val === null) ? 'text-align: center;' : 'text-align: right;';
    };

    // Kode per baris sesuai format laporan
    $kode = [
        'aktivitas_operasi' => [
            'Penerimaan dari Penyumbang'                        => '1.1',
            'Penerimaan Bunga'                                  => '1.2',
            'Penerimaan Lain-lain'                              => '1.3',
            'Pembayaran kepada Pemasok dan Penerima Sumbangan'  => '1.4',
            'Pembayaran kepada Pegawai dan Sukarelawan'         => '1.5',
        ],
        'aktivitas_investasi' => [
            'Pembelian Aset Tetap'          => '2.1',
            'Pembelian Aset Tak Berwujud'   => '2.2',
            'Investasi pada Entitas Anak'   => '2.3',
        ],
        'rekonsiliasi' => [
            'Total Penambahan (Pengurangan) Kas'    => '3.1',
            'Saldo Kas Awal Periode'                => '3.2',
        ],
    ];
@endphp
<table>
    <thead>
        {{-- Baris kosong atas --}}
        <tr style="height: 10px;">
            <td></td><td></td><td></td><td></td><td></td>
        </tr>

        {{-- ═══ HEADER ORGANISASI ═══ --}}
        <tr>
            <td style="width: 20px;"></td>
            <td colspan="4"
                style="font-size: 14px; font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 10px 8px 4px 8px;">
                PALANG MERAH INDONESIA KABUPATEN NGANJUK
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="4"
                style="font-size: 12px; font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                LAPORAN ARUS KAS
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="4"
                style="font-size: 10px; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                Tahun yang Berakhir pada 31 Desember {{ $report['year'] }} dan {{ $report['previous_year'] }}
            </td>
        </tr>
        <tr style="height: 6px;">
            <td></td>
            <td colspan="4"
                style="background-color: #4472C4;">
            </td>
        </tr>

        {{-- Spasi antara header dan tabel --}}
        <tr style="height: 8px;"><td></td><td colspan="4"></td></tr>

        {{-- ═══ COLUMN HEADERS ═══ --}}
        {{-- Kolom B = Kode, C = Uraian, D = tahun berjalan, E = tahun sebelumnya --}}
        <tr>
            <td style="width: 20px;"></td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px; width: 140px;">
            </td>
            <td style="font-weight: bold; text-align: left;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px; width: 140px;">
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px; width: 140px;">
                {{ $report['year'] }}
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 17px 18px; width: 140px;">
                {{ $report['previous_year'] }}
            </td>
        </tr>
    </thead>

    <tbody>

        {{-- ═══════════════════════════════════════ --}}
        {{--          AKTIVITAS OPERASI             --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: semi-bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aktivitas Operasi
            </td>
        </tr>

        {{-- Penerimaan --}}
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;"></td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px; color: #37474F; font-weight: bold;">
                Penerimaan:
            </td>
            <td style="border: 1px solid #white;"></td>
            <td style="border: 1px solid #white;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_operasi']['Penerimaan dari Penyumbang'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 28px;">
                Penerimaan dari Penyumbang
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['penerimaan_penyumbang']) }}">
                {{ $fmt($report['current']['penerimaan_penyumbang']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['penerimaan_penyumbang']) }}">
                {{ $fmt($report['previous']['penerimaan_penyumbang']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_operasi']['Penerimaan Bunga'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 28px;">
                Penerimaan Bunga
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['penerimaan_bunga']) }}">
                {{ $fmt($report['current']['penerimaan_bunga']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['penerimaan_bunga']) }}">
                {{ $fmt($report['previous']['penerimaan_bunga']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_operasi']['Penerimaan Lain-lain'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 28px;">
                Penerimaan Lain-lain
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['penerimaan_lain_lain']) }}">
                {{ $fmt($report['current']['penerimaan_lain_lain']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['penerimaan_lain_lain']) }}">
                {{ $fmt($report['previous']['penerimaan_lain_lain']) }}
            </td>
        </tr>

        {{-- Pembayaran --}}
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;"></td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px; color: #37474F; font-weight: bold;">
                Pembayaran:
            </td>
            <td style="border: 1px solid #white;"></td>
            <td style="border: 1px solid #white;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_operasi']['Pembayaran kepada Pemasok dan Penerima Sumbangan'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 28px;">
                Pembayaran kepada Pemasok dan Penerima Sumbangan
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['pembayaran_pemasok']) }}">
                {{ $fmt($report['current']['pembayaran_pemasok']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['pembayaran_pemasok']) }}">
                {{ $fmt($report['previous']['pembayaran_pemasok']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_operasi']['Pembayaran kepada Pegawai dan Sukarelawan'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 28px;">
                Pembayaran kepada Pegawai dan Sukarelawan
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['pembayaran_karyawan']) }}">
                {{ $fmt($report['current']['pembayaran_karyawan']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['pembayaran_karyawan']) }}">
                {{ $fmt($report['previous']['pembayaran_karyawan']) }}
            </td>
        </tr>

        {{-- Total Aktivitas Operasi --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">
                Total Penambahan (Pengurangan) Kas dari Aktivitas Operasional
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['current']['kas_neto_operasi']) }}">
                {{ $fmt($report['current']['kas_neto_operasi']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['previous']['kas_neto_operasi']) }}">
                {{ $fmt($report['previous']['kas_neto_operasi']) }}
            </td>
        </tr>

        {{-- ═══════════════════════════════════════ --}}
        {{--         AKTIVITAS INVESTASI            --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aktivitas Investasi
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_investasi']['Pembelian Aset Tetap'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Pembelian Aset Tetap
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['pembelian_aset_tetap']) }}">
                {{ $fmt($report['current']['pembelian_aset_tetap']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['pembelian_aset_tetap']) }}">
                {{ $fmt($report['previous']['pembelian_aset_tetap']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_investasi']['Pembelian Aset Tak Berwujud'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Pembelian Aset Tak Berwujud
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['pembelian_aset_tak_berwujud']) }}">
                {{ $fmt($report['current']['pembelian_aset_tak_berwujud']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['pembelian_aset_tak_berwujud']) }}">
                {{ $fmt($report['previous']['pembelian_aset_tak_berwujud']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['aktivitas_investasi']['Investasi pada Entitas Anak'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Investasi pada Entitas Anak
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['investasi_entitas_anak']) }}">
                {{ $fmt($report['current']['investasi_entitas_anak']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['investasi_entitas_anak']) }}">
                {{ $fmt($report['previous']['investasi_entitas_anak']) }}
            </td>
        </tr>

        {{-- Total Aktivitas Investasi --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">
                Total Penambahan (Pengurangan) Kas dari Aktivitas Investasi
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['current']['kas_neto_investasi']) }}">
                {{ $fmt($report['current']['kas_neto_investasi']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['previous']['kas_neto_investasi']) }}">
                {{ $fmt($report['previous']['kas_neto_investasi']) }}
            </td>
        </tr>

        {{-- ═══════════════════════════════════════ --}}
        {{--          REKONSILIASI KAS              --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="border-top: 1px solid #000000; font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Rekonsiliasi Saldo Kas
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['rekonsiliasi']['Total Penambahan (Pengurangan) Kas'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Total Penambahan (Pengurangan) Kas
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['kenaikan_neto_kas']) }}">
                {{ $fmt($report['current']['kenaikan_neto_kas']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['kenaikan_neto_kas']) }}">
                {{ $fmt($report['previous']['kenaikan_neto_kas']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['rekonsiliasi']['Saldo Kas Awal Periode'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Saldo Kas Awal Periode
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['current']['saldo_kas_awal']) }}">
                {{ $fmt($report['current']['saldo_kas_awal']) }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px; {{ $s($report['previous']['saldo_kas_awal']) }}">
                {{ $fmt($report['previous']['saldo_kas_awal']) }}
            </td>
        </tr>

        {{-- Saldo Kas Akhir --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">
                Saldo Kas Akhir Periode
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['current']['saldo_kas_akhir']) }}">
                {{ $fmt($report['current']['saldo_kas_akhir']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $s($report['previous']['saldo_kas_akhir']) }}">
                {{ $fmt($report['previous']['saldo_kas_akhir']) }}
            </td>
        </tr>

        <tr style="height: 6px;"><td></td><td colspan="4"></td></tr>

        {{-- Cash at the End of the Month --}}
        <tr>
            <td></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;"></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;">
                Cash at the End of the Month
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $s($report['current']['selisih_kas']) }}">
                {{ $fmt($report['current']['selisih_kas']) }}
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $s($report['previous']['selisih_kas']) }}">
                {{ $fmt($report['previous']['selisih_kas']) }}
            </td>
        </tr>

        {{-- Catatan rekonsiliasi --}}
        <tr style="height: 6px;"><td></td><td colspan="4"></td></tr>
        <tr>
            <td></td>
            <td colspan="4" style="font-size: 9px; color: #6b7280; padding: 4px 8px; font-style: italic;">
                * Saldo Kas Akhir harus sama dengan total Kas + Bank di Laporan Posisi Keuangan.
                @if($report['current']['rekonsiliasi_matches'])
                    ✓ Rekonsiliasi Tahun Berjalan OK.
                @else
                    ⚠ Selisih Tahun Berjalan: {{ number_format(abs($report['current']['saldo_kas_akhir'] - $report['current']['bs_kas_akhir']), 0, ',', '.') }}
                @endif
            </td>
        </tr>

        {{-- Baris kosong bawah --}}
        <tr style="height: 10px;"><td></td><td colspan="4"></td></tr>

    </tbody>
</table>
