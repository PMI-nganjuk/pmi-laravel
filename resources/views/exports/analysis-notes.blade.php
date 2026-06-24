@php
    $format = function($val) {
        if ($val == 0 || $val === null) {
            return '-';
        }
        return number_format(abs($val), 0, ',', '.');
    };

    $style = function($val) {
        return ($val == 0 || $val === null) ? 'text-align: center;' : 'text-align: right;';
    };

    $fmtSigned = function($val) use ($format) {
        if ($val == 0) return '-';
        if ($val < 0) return '(' . number_format(abs($val), 0, ',', '.') . ')';
        return number_format($val, 0, ',', '.');
    };

    // Kode per baris sesuai format laporan
    $kode = [
        'tidak_terikat' => [
            'Saldo Awal'                                        => '1.1',
            'Pendapatan Netto Tidak Terikat Periode Berjalan'   => '1.2',
        ],
        'terikat' => [
            'Saldo Awal'                                    => '2.1',
            'Pendapatan Netto Terikat Periode Berjalan'     => '2.2',
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
                LAPORAN PERUBAHAN ASET NETTO
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="4"
                style="font-size: 10px; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                Periode: Tahun {{ $report['year'] }} (dibandingkan {{ $report['previous_year'] }})
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
                Uraian
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

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- ASET NETTO TIDAK TERIKAT                                            --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: semi-bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aset Netto Tidak Terikat
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['tidak_terikat']['Saldo Awal'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Saldo Awal
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['tidak_terikat']['saldo_awal']['current']) }}">
                {{ $fmtSigned($report['tidak_terikat']['saldo_awal']['current']) }}
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['tidak_terikat']['saldo_awal']['previous']) }}">
                {{ $fmtSigned($report['tidak_terikat']['saldo_awal']['previous']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['tidak_terikat']['Pendapatan Netto Tidak Terikat Periode Berjalan'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Pendapatan Netto Tidak Terikat Periode Berjalan
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['tidak_terikat']['pendapatan_netto']['current']) }}">
                {{ $fmtSigned($report['tidak_terikat']['pendapatan_netto']['current']) }}
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['tidak_terikat']['pendapatan_netto']['previous']) }}">
                {{ $fmtSigned($report['tidak_terikat']['pendapatan_netto']['previous']) }}
            </td>
        </tr>

        {{-- Total Aset Netto Tidak Terikat --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">
                Saldo Akhir Aset Netto Tidak Terikat
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['tidak_terikat']['saldo_akhir']['current']) }}">
                {{ $fmtSigned($report['tidak_terikat']['saldo_akhir']['current']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['tidak_terikat']['saldo_akhir']['previous']) }}">
                {{ $fmtSigned($report['tidak_terikat']['saldo_akhir']['previous']) }}
            </td>
        </tr>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- ASET NETTO TERIKAT                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aset Netto Terikat
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['terikat']['Saldo Awal'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Saldo Awal
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['terikat']['saldo_awal']['current']) }}">
                {{ $fmtSigned($report['terikat']['saldo_awal']['current']) }}
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['terikat']['saldo_awal']['previous']) }}">
                {{ $fmtSigned($report['terikat']['saldo_awal']['previous']) }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                {{ $kode['terikat']['Pendapatan Netto Terikat Periode Berjalan'] ?? '' }}
            </td>
            <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                Pendapatan Netto Terikat Periode Berjalan
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['terikat']['pendapatan_netto']['current']) }}">
                {{ $fmtSigned($report['terikat']['pendapatan_netto']['current']) }}
            </td>
            <td style="border: 1px solid #white; padding: 4px 8px; {{ $style($report['terikat']['pendapatan_netto']['previous']) }}">
                {{ $fmtSigned($report['terikat']['pendapatan_netto']['previous']) }}
            </td>
        </tr>

        {{-- Total Aset Netto Terikat --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">
                Saldo Akhir Aset Netto Terikat
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['terikat']['saldo_akhir']['current']) }}">
                {{ $fmtSigned($report['terikat']['saldo_akhir']['current']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['terikat']['saldo_akhir']['previous']) }}">
                {{ $fmtSigned($report['terikat']['saldo_akhir']['previous']) }}
            </td>
        </tr>

        <tr style="height: 6px;"><td></td><td colspan="4"></td></tr>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TOTAL ASET NETTO                                                    --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;"></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;">
                Total Aset Netto
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $style($report['total_aset_netto']['current']) }}">
                {{ $fmtSigned($report['total_aset_netto']['current']) }}
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $style($report['total_aset_netto']['previous']) }}">
                {{ $fmtSigned($report['total_aset_netto']['previous']) }}
            </td>
        </tr>

        {{-- Catatan Rekonsiliasi --}}
        <tr style="height: 6px;"><td></td><td colspan="4"></td></tr>
        <tr>
            <td></td>
            <td colspan="4" style="font-size: 9px; color: #6b7280; padding: 4px 8px; font-style: italic;">
                * Total Aset Netto harus konsisten (rekonsiliasi) dengan Ekuitas pada Laporan Posisi Keuangan per 31 Desember.
            </td>
        </tr>

        {{-- Baris kosong bawah --}}
        <tr style="height: 10px;"><td></td><td colspan="4"></td></tr>

    </tbody>
</table>
