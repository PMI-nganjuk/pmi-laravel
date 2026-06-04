@php
    $format = function($val) {
        if ($val == 0) {
            return '-';
        }
        return number_format($val, 0, ',', '.');
    };
    $style = function($val) {
        return $val == 0 ? 'text-align: center;' : 'text-align: right;';
    };
@endphp
<table>
    <thead>
        <tr>
            <td></td>
            <th colspan="4" style="font-size: 14px; font-weight: bold; text-align: center; background-color: #2f5597; color: #ffffff;">PALANG MERAH INDONESIA KABUPATEN NGANJUK</th>
        </tr>
        <tr>
            <td></td>
            <th colspan="4" style="font-size: 12px; font-weight: bold; text-align: center; background-color: #2f5597; color: #ffffff;">Laporan Aktivitas</th>
        </tr>
        <tr>
            <td></td>
            <th colspan="4" style="font-size: 10px; text-align: center; background-color: #2f5597; color: #ffffff;">
                Periode 01 Januari {{ \Carbon\Carbon::parse($report['period']['start'])->format('Y') }} sampai dengan 31 Desember {{ \Carbon\Carbon::parse($report['period']['end'])->format('Y') }}
            </th>
        </tr>
        <tr>
            <td></td>
            <th colspan="4" style="background-color: #2f5597;"></th>
        </tr>
        <tr>
            <td></td>
            <td colspan="4"></td>
        </tr>
        <tr style="background-color: #2f5597; color: #ffffff;">
            <td></td>
            <th style="font-weight: bold; text-align: left; border: 1px solid #000000; color: #ffffff;">Uraian</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; color: #ffffff;">Pendapatan Tidak Terikat</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; color: #ffffff;">Pendapatan Terikat</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; color: #ffffff;">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
            <td style="font-weight: bold; color: #2f5597; text-align: left; border: 1px solid #d1d5db;">
                Pendapatan
            </td>
            <td style="border: 1px solid #d1d5db;"></td>
            <td style="border: 1px solid #d1d5db;"></td>
            <td style="border: 1px solid #d1d5db;"></td>
        </tr>

        @foreach ($report['pendapatan'] as $section)
            <tr>
                <td></td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #d1d5db;">
                    &nbsp;&nbsp;&nbsp;&nbsp;{{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #d1d5db;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr style="background-color: #aeaaaa; font-weight: bold;">
            <td></td>
            <td style="border: 1px solid #000000; font-weight: bold; font-style: italic; color: #2f5597;">Total Pendapatan</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_pendapatan']['tidak_terikat']) }}">{{ $format($report['total_pendapatan']['tidak_terikat']) }}</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_pendapatan']['terikat']) }}">{{ $format($report['total_pendapatan']['terikat']) }}</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_pendapatan']['total']) }}">{{ $format($report['total_pendapatan']['total']) }}</td>
        </tr>

        <tr>
            <td></td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td></td>
            <td style="font-weight: bold; color: #2f5597; text-align: left; border: 1px solid #d1d5db;">
                Beban Program
            </td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_program']['tidak_terikat']) }}">{{ $format($report['total_beban_program']['tidak_terikat']) }}</td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_program']['terikat']) }}">{{ $format($report['total_beban_program']['terikat']) }}</td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_program']['total']) }}">{{ $format($report['total_beban_program']['total']) }}</td>
        </tr>

        @foreach ($report['beban_program'] as $section)
            <tr>
                <td></td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #d1d5db;">
                    &nbsp;&nbsp;&nbsp;&nbsp;{{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #d1d5db;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td></td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td></td>
            <td style="font-weight: bold; color: #2f5597; text-align: left; border: 1px solid #d1d5db;">
                Beban Manajemen Umum
            </td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_manajemen_umum']['tidak_terikat']) }}">{{ $format($report['total_beban_manajemen_umum']['tidak_terikat']) }}</td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_manajemen_umum']['terikat']) }}">{{ $format($report['total_beban_manajemen_umum']['terikat']) }}</td>
            <td style="font-weight: bold; color: #2f5597; border: 1px solid #d1d5db; {{ $style($report['total_beban_manajemen_umum']['total']) }}">{{ $format($report['total_beban_manajemen_umum']['total']) }}</td>
        </tr>

        @foreach ($report['beban_manajemen_umum'] as $section)
            <tr>
                <td></td>
                <td style="font-weight: bold; text-align: left; border: 1px solid #d1d5db;">
                    &nbsp;&nbsp;&nbsp;&nbsp;{{ $section['name'] }}
                </td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['tidak_terikat']) }}">{{ $format($section['subtotal']['tidak_terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['terikat']) }}">{{ $format($section['subtotal']['terikat']) }}</td>
                <td style="font-weight: bold; border: 1px solid #d1d5db; {{ $style($section['subtotal']['total']) }}">{{ $format($section['subtotal']['total']) }}</td>
            </tr>

            @foreach ($section['accounts'] as $account)
                @if (count($section['accounts']) === 1 && $account['nama'] === $section['name'])
                    @continue
                @endif
                <tr>
                    <td></td>
                    <td style="font-style: italic; text-align: left; border: 1px solid #d1d5db;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $account['nama'] }}
                    </td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['tidak_terikat']) }}">{{ $format($account['tidak_terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['terikat']) }}">{{ $format($account['terikat']) }}</td>
                    <td style="border: 1px solid #d1d5db; {{ $style($account['total']) }}">{{ $format($account['total']) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td></td>
            <td colspan="4"></td>
        </tr>

        <tr style="background-color: #aeaaaa; font-weight: bold;">
            <td></td>
            <td style="border: 1px solid #000000; font-weight: bold; font-style: italic;">Total Biaya</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_beban']['tidak_terikat']) }}">{{ $format($report['total_beban']['tidak_terikat']) }}</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_beban']['terikat']) }}">{{ $format($report['total_beban']['terikat']) }}</td>
            <td style="border: 1px solid #000000; {{ $style($report['total_beban']['total']) }}">{{ $format($report['total_beban']['total']) }}</td>
        </tr>

        <tr>
            <td></td>
            <td colspan="4"></td>
        </tr>

        <tr style="background-color: #b4c6e7; color: #2f5597; font-weight: bold;">
            <td></td>
            <td style="border: 1px solid #000000; font-weight: bold; font-style: italic; color: #2f5597;">Total Pendapatan Komprehensif</td>
            <td style="border: 1px solid #000000; color: #2f5597; {{ $style($report['surplus']['tidak_terikat']) }}">{{ $format($report['surplus']['tidak_terikat']) }}</td>
            <td style="border: 1px solid #000000; color: #2f5597; {{ $style($report['surplus']['terikat']) }}">{{ $format($report['surplus']['terikat']) }}</td>
            <td style="border: 1px solid #000000; color: #2f5597; {{ $style($report['surplus']['total']) }}">{{ $format($report['surplus']['total']) }}</td>
        </tr>
    </tbody>
</table>
