@php
    $fmtNilai = function ($v) {
        return $v === null ? '' : (string) $v;
    };
    $fmtTotal = function ($v) {
        return $v === null ? '-' : number_format($v, 2, '.', '');
    };
    $kolom = 9 + ($fillMid ? 1 : 0) + ($fillEnd ? 1 : 0);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 7pt; color: #000; margin: 3mm 5mm; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 0.5pt solid #000; padding: 0.75pt 1.5pt; vertical-align: middle; word-wrap: break-word; overflow-wrap: break-word; }
        tr { page-break-inside: avoid; }
        .noborder { border: none !important; }
        .noborder > tr > td { border: none; }
        .center { text-align: center; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .title { text-align: center; font-size: 12pt; font-weight: bold; color: #8B0000; }
        .subtitle { text-align: center; font-size: 10pt; font-weight: bold; color: #8B0000; }
        .periode { text-align: right; font-weight: bold; margin-top: 2pt; margin-bottom: 3pt; }
        .info-label { font-weight: bold; background: #f2f2f2; white-space: nowrap; }
        .info-col { vertical-align: top; }
        .small { font-size: 6pt; table-layout: fixed; }
        .head-red th { background: #c00000; color: #ffffff; text-align: center; }
        .section-row td { background: #f2f2f2; font-weight: bold; }
        .total-row td { background: #e53935; color: #ffffff; font-weight: bold; }
    </style>
</head>
<body>

    <table class="noborder" style="margin-bottom: 2mm;">
        <tr>
            <td class="noborder" style="width: 22%; vertical-align: middle;">
                @if (!empty($logoSrc))
                    <img src="{{ $logoSrc }}" alt="BAS" style="height: 26mm; max-width: 140mm;">
                @endif
            </td>
            <td class="noborder" style="width: 56%;">
                <div class="title">PERFORMANCE MANAGEMENT SYSTEM {{ $header['year'] }}</div>
                <div class="subtitle">INDIVIDUAL PERFORMANCE PLAN &mdash; LEVEL 2-3</div>
            </td>
            <td class="noborder" style="width: 22%; vertical-align: middle; text-align: right;">
                @if (!empty($logoWingsSrc))
                    <img src="{{ $logoWingsSrc }}" alt="WINGS" style="height: 13mm; max-width: 70mm;">
                @endif
            </td>
        </tr>
    </table>
    <div class="periode">PERIODE PENILAIAN : {{ $header['year'] }}</div>

    <table class="noborder">
        <tr>
            <td class="info-col noborder" style="width: 46%; padding-right: 2mm;">
                <table>
                    @foreach ([
                        'Nama' => $header['nama'],
                        'NIK' => $header['nik'],
                        'Jabatan' => $header['jabatan'],
                        'Departemen' => $header['departemen'],
                        'Bagian' => $header['bagian'],
                        'Level' => $header['level'],
                    ] as $label => $value)
                    <tr>
                        <td class="info-label" colspan="2">{{ $label }}</td>
                        <td style="width: 4%;" class="center">:</td>
                        <td colspan="4">{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td class="info-col noborder" style="width: 54%;">
                <table>
                    @foreach ([
                        'Manager' => $header['manager'],
                        'Atasan Langsung' => $header['atasan_langsung'],
                        'Jumlah Bawahan Langsung' => $header['jumlah_bawahan'],
                        'Pendidikan' => $header['pendidikan'],
                        'Tanggal Join' => $header['tgl_join'],
                        'Usia' => $header['usia'],
                    ] as $label => $value)
                    <tr>
                        <td class="info-label" colspan="3">{{ $label }}</td>
                        <td style="width: 3%;" class="center">:</td>
                        <td colspan="5">{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <table class="small" style="margin-top: 3mm;">
        <colgroup>
            <col style="width: 3%;">
            <col style="width: 9%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            @if ($fillMid)
            <col style="width: 8.5%;">
            @endif
            @if ($fillEnd)
            <col style="width: 8.5%;">
            @endif
        </colgroup>
        <thead>
            <tr class="head-red">
                <th rowspan="2">NO</th>
                <th rowspan="2">AREA</th>
                <th rowspan="2">COMPETENCY BEHAVIOR</th>
                <th rowspan="2">KEY BEHAVIOR</th>
                <th colspan="5">SCORING CRITERIA</th>
                @if ($fillMid)
                    <th rowspan="2">LEADER ASSESSMENT<br>{{ strtoupper(\App\Support\IppTahapan::label($midT)) }}</th>
                @endif
                @if ($fillEnd)
                    <th rowspan="2">LEADER ASSESSMENT<br>{{ strtoupper(\App\Support\IppTahapan::label($endT)) }}</th>
                @endif
            </tr>
            <tr class="head-red">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
            </tr>
        </thead>
        <tbody>
            @php $sectionNow = null; @endphp
            @foreach ($rows as $row)
                @if ($row['section'] && $row['section'] !== $sectionNow)
                    @php $sectionNow = $row['section']; @endphp
                    <tr class="section-row">
                        <td colspan="{{ $kolom }}">{{ $sectionNow }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="center bold">{{ $row['no_urut'] }}</td>
                    <td>{{ $row['area'] }}</td>
                    <td>{{ $row['competency_behavior'] }}</td>
                    <td>{{ $row['key_behavior'] }}</td>
                    <td>{{ $row['kriteria_1'] }}</td>
                    <td>{{ $row['kriteria_2'] }}</td>
                    <td>{{ $row['kriteria_3'] }}</td>
                    <td>{{ $row['kriteria_4'] }}</td>
                    <td>{{ $row['kriteria_5'] }}</td>
                    @if ($fillMid)
                        <td class="center bold" style="font-size: 9pt;">{{ $showScore ? $fmtNilai($row['mid_nilai']) : '' }}</td>
                    @endif
                    @if ($fillEnd)
                        <td class="center bold" style="font-size: 9pt;">{{ $showScore ? $fmtNilai($row['end_nilai']) : '' }}</td>
                    @endif
                </tr>
            @endforeach
            @if ($showTotal)
            <tr class="total-row">
                <td colspan="9" class="center">TOTAL SCORE (RATA-RATA)</td>
                @if ($fillMid)
                    <td class="center">{{ $fmtTotal($totalMidScore) }}@if ($midPa) ({{ $midPa }})@endif</td>
                @endif
                @if ($fillEnd)
                    <td class="center">{{ $fmtTotal($totalEndScore) }}@if ($endPa) ({{ $endPa }})@endif</td>
                @endif
            </tr>
            @endif
        </tbody>
    </table>

    @if (!empty($notes))
    <div style="margin-top: 3mm; font-size: 7pt;">
        @foreach ($notes as $tahapan => $roles)
            @foreach ($roles as $role => $note)
                <div><strong>Catatan {{ \App\Support\IppTahapan::label($tahapan) }}
                    ({{ $role === 'associate_manager' ? 'Associate Manager' : 'Manager' }}):</strong> {{ $note }}</div>
            @endforeach
        @endforeach
    </div>
    @endif

@if(!empty($approval))
@foreach($approval as $blok)
<div style="margin-top: 14px; page-break-inside: avoid;">
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 4px; color: #000;">STATUS PERSETUJUAN &mdash; {{ $blok['tahap'] }}</div>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; font-size: 7.5pt;">
        <thead>
            <tr>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 18%;">Tahap</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 30%;">Nama</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 15%;">NIK</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 16%;">Status</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 21%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['associate_manager', 'manager'] as $roleKey)
                @php $row = $blok[$roleKey]; @endphp
                @if($roleKey === 'manager' || $row['exists'])
                @php
                    $statusText = $row['status'] === 'approved' ? 'Disetujui' : ($row['status'] === 'rejected' ? 'Ditolak' : 'Belum disetujui');
                    $statusColor = $row['status'] === 'approved' ? '#1b5e20' : '#a80000';
                @endphp
                <tr>
                    <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">{{ $row['label'] }}</td>
                    <td style="border: 1px solid #000; padding: 5px 8px; background: #fff;">{{ $row['nama'] }}</td>
                    <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ $row['nik'] }}</td>
                    <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff; color: {{ $statusColor }};">{{ $statusText }}</td>
                    <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ !empty($row['at']) ? \Carbon\Carbon::parse($row['at'])->format('d M Y H:i') : '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endif

</body>
</html>
