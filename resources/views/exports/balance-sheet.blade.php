@php
    $format = function($val) {
        if ($val == 0 || empty($val)) {
            return '-';
        }
        return number_format($val, 0, ',', '.');
    };

    $style = function($val) {
        return ($val == 0 || empty($val)) ? 'text-align: center;' : 'text-align: right;';
    };

    // Kode per baris sesuai PDF
    $kode = [
        'aset_lancar' => [
            'Kas'                    => '1.1',
            'Bank'                   => '1.2',
            'Piutang Lain-lain'      => '1.3',
            'Persediaan'             => '1.4',
            'Uang Muka Kerja'        => '1.5',
            'Biaya Dibayar Di Muka'  => '1.6',
        ],
        'aset_tidak_lancar' => [
            'Tanah dan Bangunan'            => '2.1',
            'Aset Tetap Lainnya'            => '2.2',
            'Akumulasi Penyusutan'          => '2.3',
            'Aset Tidak Lancar Lainnya'     => '2.4',
            'Investasi pada entitas anak'   => '2.5',
        ],
        'liabilitas_lancar' => [
            'Hutang Kepada Lembaga Lain'        => '3.1',
            'Hutang Lain-lain'                  => '3.2',
            'Hutang Pajak'                      => '3.3',
            'Biaya Yang Masih Harus Dibayar'    => '3.4',
        ],
        'liabilitas_tidak_lancar' => [
            'Hutang Usaha Jangka Panjang Inter Co'  => '3.5',
            'Liabilitas Tidak Lancar Lainnya'       => '3.6',
        ],
        'aset_netto' => [
            'Akumulasi Aset Netto Tidak Terikat'              => '4.1',
            'Akumulasi Aset Netto Terikat'                    => '4.2',
            'Pendapatan Netto Tidak Terikat Periode Berjalan' => '4.3',
            'Pendapatan Netto Terikat Periode Berjalan'       => '4.4',
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
                LAPORAN POSISI KEUANGAN
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="4"
                style="font-size: 10px; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                Periode sampai dengan 31 Desember {{ $report['year'] }}
            </td>
        </tr>
        <tr style="height: 6px;">
            <td></td>
            <td colspan="4"
                style="background-color: #4472C4;
                       ">
            </td>
        </tr>

        {{-- Spasi antara header dan tabel --}}
        <tr style="height: 8px;"><td></td><td colspan="4"></td></tr>

        {{-- ═══ COLUMN HEADERS ═══ --}}
        {{-- background-color di-set per <td> agar PhpSpreadsheet tidak mengabaikannya --}}
        {{-- Kolom B = Kode, C = Uraian, D = tahun berjalan, E = tahun sebelumnya --}}
        <tr>
            <td style="width: 20px;"></td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom : 1px solid #000000; padding: 7px 8px; width: 140px;
                       ">
            </td>
            <td style="font-weight: bold; text-align: left;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom : 1px solid #000000; padding: 7px 8px; width: 140px;
                       ">
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom : 1px solid #000000; padding: 7px 8px; width: 140px;
                       ">
                {{ $report['year'] }}
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom : 1px solid #000000; padding: 17px 18px; width: 140px;
                       ">
                {{ $report['previous_year'] }}
            </td>
        </tr>
    </thead>

    <tbody>

        {{-- ═══════════════════════════════════════ --}}
        {{--              ASET LANCAR               --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: semi-bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aset Lancar
            </td>
        </tr>

        @foreach ($report['aset_lancar'] as $row)
            <tr>
                <td></td>
                <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    {{ $kode['aset_lancar'][$row['name']] ?? '' }}
                </td>
                <td style="border: 1px solid #white; padding: 5px 8px 5px 16px;">
                    {{ $row['name'] }}
                </td>
                <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($row['current']) }}">
                    {{ $format($row['current']) }}
                </td>
                <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($row['previous']) }}">
                    {{ $format($row['previous']) }}
                </td>
            </tr>
        @endforeach

        {{-- Total Aset Lancar — background biru gelap, teks putih --}}
        <tr>
            <td></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">Total Aset Lancar</td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_aset_lancar']['current']) }}">
                {{ $format($report['total_aset_lancar']['current']) }}
            </td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_aset_lancar']['previous']) }}">
                {{ $format($report['total_aset_lancar']['previous']) }}
            </td>
        </tr>

        <!-- <tr style="height: 8px;"><td></td><td colspan="4"></td></tr> -->

        {{-- ═══════════════════════════════════════ --}}
        {{--           ASET TIDAK LANCAR            --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: semi-bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aset Tidak Lancar
            </td>
        </tr>

        @foreach ($report['aset_tidak_lancar'] as $row)
            <tr>
                <td></td>
                <td style="text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    {{ $kode['aset_tidak_lancar'][$row['name']] ?? '' }}
                </td>
                <td style="padding: 5px 8px 5px 16px;">
                    {{ $row['name'] }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['current']) }}">
                    {{ $format($row['current']) }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['previous']) }}">
                    {{ $format($row['previous']) }}
                </td>
            </tr>
        @endforeach

        {{-- Total Aset Tidak Lancar --}}
        <tr>
            <td></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4;"></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic;">Total Aset Tidak Lancar</td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_aset_tidak_lancar']['current']) }}">
                {{ $format($report['total_aset_tidak_lancar']['current']) }}
            </td>
            <td style="border-top : 1px solid #000000; padding: padding: 6px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_aset_tidak_lancar']['previous']) }}">
                {{ $format($report['total_aset_tidak_lancar']['previous']) }}
            </td>
        </tr>

        {{-- Total Assets --}}
        <tr>
            <td></td>
            <td style="border-bottom : 1px solid #000000; padding: 7px 8px; background-color: #D6DCE4;"></td>
            <td style="border-bottom : 1px solid #000000; padding: 7px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold;">Total Assets</td>
            <td style="border-bottom : 1px solid #000000; padding: 7px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; {{ $style($report['total_aset']['current']) }}">
                {{ $format($report['total_aset']['current']) }}
            </td>
            <td style="border-bottom : 1px solid #000000; padding: padding: 7px 8px; background-color: #D6DCE4; color: #0000FF; font-weight: bold; {{ $style($report['total_aset']['previous']) }}">
                {{ $format($report['total_aset']['previous']) }}
            </td>
        </tr>

        <!-- <tr style="height: 12px;"><td></td><td colspan="4"></td></tr> -->

        {{-- ═══════════════════════════════════════ --}}
        {{--           LIABILITAS LANCAR            --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="border-top: 1px solid #000000; font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Liabilitas Lancar
            </td>
        </tr>

        @foreach ($report['liabilitas_lancar'] as $row)
            <tr>
                <td></td>
                <td style="text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    {{ $kode['liabilitas_lancar'][$row['name']] ?? '' }}
                </td>
                <td style="padding: 5px 8px 5px 16px;">
                    {{ $row['name'] }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['current']) }}">
                    {{ $format($row['current']) }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['previous']) }}">
                    {{ $format($row['previous']) }}
                </td>
            </tr>
        @endforeach

        {{-- Total Liabilitas Lancar --}}
        <tr>
            <td></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #white;"></td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic;">Total Liabilitas Lancar</td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_liabilitas_lancar']['current']) }}">
                {{ $format($report['total_liabilitas_lancar']['current']) }}
            </td>
            <td style="border-top : 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_liabilitas_lancar']['previous']) }}">
                {{ $format($report['total_liabilitas_lancar']['previous']) }}
            </td>
        </tr>

        <!-- <tr style="height: 8px;"><td></td><td colspan="4"></td></tr> -->

        {{-- ═══════════════════════════════════════ --}}
        {{--        LIABILITAS TIDAK LANCAR         --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Liabilitas Tidak Lancar
            </td>
        </tr>

        @foreach ($report['liabilitas_tidak_lancar'] as $row)
            <tr>
                <td></td>
                <td style="text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    {{ $kode['liabilitas_tidak_lancar'][$row['name']] ?? '' }}
                </td>
                <td style="padding: 5px 8px 5px 16px;">
                    {{ $row['name'] }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['current']) }}">
                    {{ $format($row['current']) }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['previous']) }}">
                    {{ $format($row['previous']) }}
                </td>
            </tr>
        @endforeach

        {{-- Total Liabilitas Tidak Lancar --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic;">Total Liabilitas Tidak Lancar</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_liabilitas_tidak_lancar']['current']) }}">
                {{ $format($report['total_liabilitas_tidak_lancar']['current']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic; {{ $style($report['total_liabilitas_tidak_lancar']['previous']) }}">
                {{ $format($report['total_liabilitas_tidak_lancar']['previous']) }}
            </td>
        </tr>

        <tr style="height: 12px;"><td></td><td colspan="4"></td></tr>

        {{-- ═══════════════════════════════════════ --}}
        {{--              ASET NETTO                --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="3" style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Aset Netto
            </td>
        </tr>

        @foreach ($report['aset_netto'] as $row)
            <tr>
                <td></td>
                <td style="text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    {{ $kode['aset_netto'][$row['name']] ?? '' }}
                </td>
                <td style="padding: 5px 8px 5px 16px;">
                    {{ $row['name'] }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['current']) }}">
                    {{ $format($row['current']) }}
                </td>
                <td style="padding: 5px 8px; {{ $style($row['previous']) }}">
                    {{ $format($row['previous']) }}
                </td>
            </tr>
        @endforeach

        {{-- Total Asset Netto --}}
        <tr>
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #0000FF; font-weight: bold; font-style: italic;">Total Asset Netto</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #000000; font-weight: bold; font-style: italic; {{ $style($report['total_aset_netto']['current']) }}">
                {{ $format($report['total_aset_netto']['current']) }}
            </td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #white; color: #000000; font-weight: bold; font-style: italic; {{ $style($report['total_aset_netto']['previous']) }}">
                {{ $format($report['total_aset_netto']['previous']) }}
            </td>
        </tr>

        <tr style="height: 6px;"><td></td><td colspan="4"></td></tr>

        {{-- ═══════════════════════════════════════ --}}
        {{--    TOTAL LIABILITAS DAN ASET NETTO     --}}
        {{-- ═══════════════════════════════════════ --}}
        <tr>
            <td></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;"></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold;">
                Total Liabilitas dan Aset Netto
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $style($report['total_liabilitas_dan_aset_netto']['current']) }}">
                {{ $format($report['total_liabilitas_dan_aset_netto']['current']) }}
            </td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #white; color: #0000FF; font-weight: bold; {{ $style($report['total_liabilitas_dan_aset_netto']['previous']) }}">
                {{ $format($report['total_liabilitas_dan_aset_netto']['previous']) }}
            </td>
        </tr>

        {{-- Baris kosong bawah --}}
        <tr style="height: 10px;"><td></td><td colspan="4"></td></tr>

    </tbody>
</table>