@php
    $fmt = function ($v) {
        if ($v === null || $v === '') {
            return '';
        }
        $s = number_format((float) $v, 2, '.', '');
        return rtrim(rtrim($s, '0'), '.');
    };
    $fmtAktual = function ($v) {
        if ($v === null || $v === '') {
            return '';
        }
        $s = number_format((float) $v, 10, '.', '');
        return rtrim(rtrim($s, '0'), '.');
    };
    $fmtPct = function ($v) {
        if ($v === null || $v === '') {
            return '';
        }
        return round(((float) $v) * 100) . '%';
    };
    $disp = function ($text, $num) use ($fmtAktual) {
        if ($text !== null && $text !== '') {
            return $text;
        }
        return $fmtAktual($num);
    };
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
        .merged { text-align: center; font-weight: bold; background: #f2f2f2; }
        .title { text-align: center; font-size: 12pt; font-weight: bold; color: #8B0000; }
        .subtitle { text-align: center; font-size: 10pt; font-weight: bold; color: #8B0000; }
        .periode { text-align: right; font-weight: bold; margin-top: 2pt; margin-bottom: 3pt; }
        .info-label { font-weight: bold; background: #f2f2f2; white-space: nowrap; }
        .info-col { vertical-align: top; }
        .note { height: 8mm; vertical-align: top; }
        .small { font-size: 6pt; table-layout: fixed; }
        .ach-head { font-size: 5.5pt; }
        .total-row td { background: #e53935; }
        .total-row { color: #ffffff; }
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
                <div class="subtitle">INDIVIDUAL PERFORMANCE PLAN</div>
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
            <col style="width: 2%;">
            <col style="width: 4%;">
            <col style="width: 4%;">
            <col style="width: 4%;">
            <col style="width: 6.5%;">
            <col style="width: 5.5%;">
            <col style="width: 5.5%;">
            <col style="width: 7%;">
            <col style="width: 7%;">
            <col style="width: 3.5%;">
            <col style="width: 3.5%;">
            <col style="width: 3.5%;">
            <col style="width: 5.5%;">
            @if ($showScore)
            <col style="width: 3.2%;">
            <col style="width: 3.2%;">
            <col style="width: 3.2%;">
            <col style="width: 4%;">
            <col style="width: 3.6%;">
            <col style="width: 2.4%;">
            <col style="width: 3.2%;">
            <col style="width: 3.2%;">
            <col style="width: 3.2%;">
            <col style="width: 4%;">
            <col style="width: 3.6%;">
            <col style="width: 2.4%;">
            @else
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            <col style="width: 6.5%;">
            @endif
        </colgroup>
        <tr>
            <td class="merged" rowspan="3">No</td>
            <td class="merged" colspan="3" rowspan="3">Objective</td>
            <td class="merged" rowspan="3">Key Performance Area (KPA)</td>
            <td class="merged" colspan="2" rowspan="3">Key Performance Indicator (KPI)</td>
            <td class="merged" rowspan="3">Classification</td>
            <td class="merged" rowspan="3">Unit Of Measurement</td>
            <td class="merged" rowspan="3">Weight</td>
            <td class="merged" colspan="2" rowspan="3">Target</td>
            <td class="merged" rowspan="3">Due date</td>
            <td class="merged" colspan="{{ $showScore ? 12 : 6 }}">Actual Result</td>
        </tr>
        <tr>
            <td class="merged" colspan="{{ $showScore ? 6 : 3 }}" style="background:#fdd960;">Mid Year</td>
            <td class="merged" colspan="{{ $showScore ? 6 : 3 }}" style="background:#f4b482;">End Year</td>
        </tr>
        <tr>
            <td class="merged ach-head" style="background:#FEF4CF;">Akt. Dept</td>
            <td class="merged ach-head" style="background:#FEF4CF;">Akt. Karyawan</td>
            <td class="merged ach-head" style="background:#FEF4CF;">Akt. Atasan</td>
            @if ($showScore)
            <td class="merged ach-head" style="background:#FEF4CF;">Ach. Category</td>
            <td class="merged ach-head" style="background:#FEF4CF;">Ach. Score</td>
            <td class="merged ach-head" style="background:#FEF4CF;">Scr.Scale</td>
            @endif
            <td class="merged ach-head" style="background:#fbe6d5;">Akt. Dept</td>
            <td class="merged ach-head" style="background:#fbe6d5;">Akt. Karyawan</td>
            <td class="merged ach-head" style="background:#fbe6d5;">Akt. Atasan</td>
            @if ($showScore)
            <td class="merged ach-head" style="background:#fbe6d5;">Ach. Category</td>
            <td class="merged ach-head" style="background:#fbe6d5;">Ach. Score</td>
            <td class="merged ach-head" style="background:#fbe6d5;">Scr.Scale</td>
            @endif
        </tr>
        @foreach ($rows as $row)
        <tr>
            <td class="center">{{ $row['no_urut'] }}</td>
            <td colspan="3">{{ $row['objective'] }}</td>
            <td>{{ $row['kpa'] }}</td>
            <td colspan="2">{{ $row['kpi'] }}</td>
            <td>{{ $row['classification'] }}</td>
            <td>{{ $row['uom'] }}</td>
            <td class="center">{{ $fmt($row['weight']) }}</td>
            <td colspan="2" class="center">
                @if (!empty($row['target_display']))
                    {!! nl2br(e($row['target_display'])) !!}
                @else
                    {{ $row['target'] }}
                @endif
            </td>
            <td>{{ $row['due_date'] }}</td>
            <td class="center">{{ $disp($row['mid_departemen_text'] ?? null, $row['mid_departemen']) }}</td>
            <td class="center">{{ $disp($row['mid_karyawan_text'] ?? null, $row['mid_karyawan']) }}</td>
            <td class="center">{{ $disp($row['mid_actual_text'] ?? null, $row['mid_actual']) }}</td>
            @if ($showScore)
            <td class="center">{{ $row['mid_achievement_category'] }}</td>
            <td class="center">{{ $fmtPct($row['mid_achievement_score']) }}</td>
            <td class="center">{{ $fmt($row['mid_score_scale']) }}</td>
            @endif
            <td class="center">{{ $disp($row['end_departemen_text'] ?? null, $row['end_departemen']) }}</td>
            <td class="center">{{ $disp($row['end_karyawan_text'] ?? null, $row['end_karyawan']) }}</td>
            <td class="center">{{ $disp($row['end_actual_text'] ?? null, $row['end_actual']) }}</td>
            @if ($showScore)
            <td class="center">{{ $row['end_achievement_category'] }}</td>
            <td class="center">{{ $fmtPct($row['end_achievement_score']) }}</td>
            <td class="center">{{ $fmt($row['end_score_scale']) }}</td>
            @endif
        </tr>
        @endforeach
        <tr class="bold total-row">
            <td class="center">&nbsp;</td>
            <td colspan="3">&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="center">{{ $fmt($totalWeight) }}</td>
            <td colspan="2">&nbsp;</td>
            <td>&nbsp;</td>
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            @if ($showScore)
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            <td class="center">{{ $fmt($totalMidScore) }}</td>
            @endif
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            @if ($showScore)
            <td class="center">&nbsp;</td>
            <td class="center">&nbsp;</td>
            <td class="center">{{ $fmt($totalEndScore) }}</td>
            @endif
        </tr>
    </table>

    <table class="noborder" style="margin-top: 2mm;">
        <tr>
            <td class="noborder" style="width: {{ $showScore ? '68%' : '100%' }}; padding-right: 2mm;">
                @php
                    $noteOf = function ($t, $r) use ($notes) {
                        $v = ($notes[$t][$r] ?? null);
                        if ($v === null || trim((string) $v) === '') {
                            return '-';
                        }
                        return e($v);
                    };
                @endphp
                <table>
                    @if ($fillMid && $fillEnd)
                    <tr>
                        <td class="note center" style="width:50%;"><strong>{{ $midT }}</strong></td>
                        <td class="note center" style="width:50%;"><strong>{{ $endT }}</strong></td>
                    </tr>
                    <tr>
                        <td class="note">Note (Assoc. Manager):<br>{{ $noteOf($midT, 'associate_manager') }}</td>
                        <td class="note">Note (Assoc. Manager):<br>{{ $noteOf($endT, 'associate_manager') }}</td>
                    </tr>
                    <tr>
                        <td class="note">Note (Manager):<br>{{ $noteOf($midT, 'manager') }}</td>
                        <td class="note">Note (Manager):<br>{{ $noteOf($endT, 'manager') }}</td>
                    </tr>
                    @elseif ($fillMid)
                    <tr><td class="note center"><strong>{{ $midT }}</strong></td></tr>
                    <tr><td class="note">Note (Assoc. Manager):<br>{{ $noteOf($midT, 'associate_manager') }}</td></tr>
                    <tr><td class="note">Note (Manager):<br>{{ $noteOf($midT, 'manager') }}</td></tr>
                    @elseif ($fillEnd)
                    <tr><td class="note center"><strong>{{ $endT }}</strong></td></tr>
                    <tr><td class="note">Note (Assoc. Manager):<br>{{ $noteOf($endT, 'associate_manager') }}</td></tr>
                    <tr><td class="note">Note (Manager):<br>{{ $noteOf($endT, 'manager') }}</td></tr>
                    @endif
                </table>
            </td>
            @if ($showScore)
            <td class="noborder" style="width: 36%; vertical-align: top;">
                @if ($fillMid && $fillEnd)
                <table style="width: 100%; margin-bottom: 2mm;">
                    <tr>
                        <td rowspan="2" style="background:#fee796; width: 34%; text-align: center; font-weight: bold; font-size: 8pt; vertical-align: middle;">
                            MID YEAR
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            SCORE:
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            PA IPP:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $totalMidScore !== null ? number_format($totalMidScore, 2, '.', '') : '-' }}
                        </td>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $midPa ?? '-' }}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%;">
                    <tr>
                        <td rowspan="2" style="background:#f4b382; width: 34%; text-align: center; font-weight: bold; font-size: 8pt; vertical-align: middle;">
                            END YEAR
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            SCORE:
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            PA IPP:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $totalEndScore !== null ? number_format($totalEndScore, 2, '.', '') : '-' }}
                        </td>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $endPa ?? '-' }}
                        </td>
                    </tr>
                </table>
                @elseif ($fillMid)
                <table style="width: 100%;">
                    <tr>
                        <td rowspan="2" style="background:#fee796; width: 34%; text-align: center; font-weight: bold; font-size: 8pt; vertical-align: middle;">
                            MID YEAR
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            SCORE:
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            PA IPP:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $totalMidScore !== null ? number_format($totalMidScore, 2, '.', '') : '-' }}
                        </td>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $midPa ?? '-' }}
                        </td>
                    </tr>
                </table>
                @elseif ($fillEnd)
                <table style="width: 100%;">
                    <tr>
                        <td rowspan="2" style="background:#f4b382; width: 34%; text-align: center; font-weight: bold; font-size: 8pt; vertical-align: middle;">
                            END YEAR
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            SCORE:
                        </td>
                        <td style="width: 33%; font-weight: bold; font-size: 6pt; padding: 1pt 2pt; vertical-align: top;">
                            PA IPP:
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $totalEndScore !== null ? number_format($totalEndScore, 2, '.', '') : '-' }}
                        </td>
                        <td style="font-weight: bold; font-size: 11pt; text-align: center; vertical-align: middle; padding: 2pt 1pt;">
                            {{ $endPa ?? '-' }}
                        </td>
                    </tr>
                </table>
                @endif
            </td>
            @endif
        </tr>
    </table>

@if(!empty($approval))
@foreach($approval as $blok)
<div style="margin-top: 14px; page-break-inside: avoid;">
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 4px; color: #000;">STATUS PERSETUJUAN — {{ $blok['tahap'] }}</div>
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