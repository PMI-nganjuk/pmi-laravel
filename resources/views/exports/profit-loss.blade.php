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
@endphp
<table>
    <thead>
        {{-- Baris kosong atas --}}
        <tr style="height: 10px;">
            <td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>

        {{-- ═══ HEADER ORGANISASI ═══ --}}
        <tr>
            <td style="width: 20px;"></td>
            <td colspan="5"
                style="font-size: 14px; font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 10px 8px 4px 8px;">
                PALANG MERAH INDONESIA KABUPATEN NGANJUK
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="5"
                style="font-size: 12px; font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                LAPORAN AKTIVITAS
            </td>
        </tr>
        <tr>
            <td></td>
            <td colspan="5"
                style="font-size: 10px; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       padding: 2px 8px;">
                Periode 01 Januari {{ \Carbon\Carbon::parse($report['period']['start'])->format('Y') }} sampai dengan 31 Desember {{ \Carbon\Carbon::parse($report['period']['end'])->format('Y') }}
            </td>
        </tr>
        <tr style="height: 6px;">
            <td></td>
            <td colspan="5" style="background-color: #4472C4;"></td>
        </tr>

        {{-- Spasi antara header dan tabel --}}
        <tr style="height: 8px;"><td></td><td colspan="5"></td></tr>

        {{-- ═══ COLUMN HEADERS ═══ --}}
        <tr>
            <td style="width: 20px;"></td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px;">
            </td>
            <td style="font-weight: bold; text-align: left;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px;">
                Uraian
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px;">
                Pendapatan Tidak Terikat
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px;">
                Pendapatan Terikat
            </td>
            <td style="font-weight: bold; text-align: center;
                       background-color: #4472C4; color: #ffffff;
                       border-bottom: 1px solid #000000; padding: 7px 8px;">
                Total Pendapatan
            </td>
        </tr>
    </thead>
    <tbody>

        {{-- PENDAPATAN --}}
        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td colspan="4" style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Pendapatan
            </td>
        </tr>

        @php $noPendapatan = 1; @endphp
        @foreach ($report['pendapatan'] as $section)
            <tr>
                <td></td>
                <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    1.{{ $noPendapatan }}
                </td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 16px;">
                    {{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @php $noAcc = 1; @endphp
            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                        1.{{ $noPendapatan }}.{{ $noAcc }}
                    </td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 28px;">
                        {{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
                @php $noAcc++; @endphp
            @endforeach
            @php $noPendapatan++; @endphp
        @endforeach

        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #b4c6e7;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7;">Total Pendapatan</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_pendapatan']['tidak_terikat']) }}">{{ $format($report['total_pendapatan']['tidak_terikat']) }}</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_pendapatan']['terikat']) }}">{{ $format($report['total_pendapatan']['terikat']) }}</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_pendapatan']['total']) }}">{{ $format($report['total_pendapatan']['total']) }}</td>
        </tr>

        <tr style="height: 12px;"><td></td><td colspan="5"></td></tr>

        {{-- BEBAN PROGRAM --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Beban Program
            </td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_program']['tidak_terikat']) }}">{{ $format($report['total_beban_program']['tidak_terikat']) }}</td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_program']['terikat']) }}">{{ $format($report['total_beban_program']['terikat']) }}</td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_program']['total']) }}">{{ $format($report['total_beban_program']['total']) }}</td>
        </tr>

        @php $noBebanProg = 1; @endphp
        @foreach ($report['beban_program'] as $section)
            <tr>
                <td></td>
                <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    2.{{ $noBebanProg }}
                </td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 16px;">
                    {{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @php $noAcc = 1; @endphp
            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                        2.{{ $noBebanProg }}.{{ $noAcc }}
                    </td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 28px;">
                        {{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
                @php $noAcc++; @endphp
            @endforeach
            @php $noBebanProg++; @endphp
        @endforeach

        <tr style="height: 12px;"><td></td><td colspan="5"></td></tr>

        {{-- BEBAN MANAJEMEN UMUM --}}
        <tr>
            <td></td>
            <td style="padding: 6px 8px; background-color: #white;"></td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white;">
                Beban Manajemen Umum
            </td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_manajemen_umum']['tidak_terikat']) }}">{{ $format($report['total_beban_manajemen_umum']['tidak_terikat']) }}</td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_manajemen_umum']['terikat']) }}">{{ $format($report['total_beban_manajemen_umum']['terikat']) }}</td>
            <td style="font-weight: bold; color: #0000FF; padding: 6px 8px; background-color: #white; {{ $style($report['total_beban_manajemen_umum']['total']) }}">{{ $format($report['total_beban_manajemen_umum']['total']) }}</td>
        </tr>

        @php $noBebanM = 1; @endphp
        @foreach ($report['beban_manajemen_umum'] as $section)
            <tr>
                <td></td>
                <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                    3.{{ $noBebanM }}
                </td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 16px;">
                    {{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #white; padding: 5px 8px; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @php $noAcc = 1; @endphp
            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="border: 1px solid #white; text-align: center; padding: 5px 8px; font-size: 10px; color: #6b7280;">
                        3.{{ $noBebanM }}.{{ $noAcc }}
                    </td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #white; padding: 5px 8px 5px 28px;">
                        {{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #white; padding: 5px 8px; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
                @php $noAcc++; @endphp
            @endforeach
            @php $noBebanM++; @endphp
        @endforeach

        <tr style="height: 12px;"><td></td><td colspan="5"></td></tr>

        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; background-color: #b4c6e7;"></td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7;">Total Biaya</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_beban']['tidak_terikat']) }}">{{ $format($report['total_beban']['tidak_terikat']) }}</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_beban']['terikat']) }}">{{ $format($report['total_beban']['terikat']) }}</td>
            <td style="border-top: 1px solid #000000; padding: 6px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7; {{ $style($report['total_beban']['total']) }}">{{ $format($report['total_beban']['total']) }}</td>
        </tr>

        <tr style="height: 12px;"><td></td><td colspan="5"></td></tr>

        <tr style="background-color: #b4c6e7;">
            <td></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; background-color: #b4c6e7;"></td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; font-weight: bold; font-style: italic; color: #0000FF; background-color: #b4c6e7;">Total Pendapatan Komprehensif</td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; font-weight: bold; color: #0000FF; background-color: #b4c6e7; {{ $style($report['surplus']['tidak_terikat']) }}">{{ $fmtSigned($report['surplus']['tidak_terikat']) }}</td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; font-weight: bold; color: #0000FF; background-color: #b4c6e7; {{ $style($report['surplus']['terikat']) }}">{{ $fmtSigned($report['surplus']['terikat']) }}</td>
            <td style="border-bottom: 2px solid #000000; padding: 9px 8px; font-weight: bold; color: #0000FF; background-color: #b4c6e7; {{ $style($report['surplus']['total']) }}">{{ $fmtSigned($report['surplus']['total']) }}</td>
        </tr>

        <tr style="height: 10px;"><td></td><td colspan="5"></td></tr>

    </tbody>
</table>
