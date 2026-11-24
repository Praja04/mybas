<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Penilaian Karya</title>
    <style>
        @page { size: A4 landscape; margin: 12px 14px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7pt; color: #000; }
        .head { width: 100%; margin-bottom: 6px; }
        .head td { vertical-align: middle; }
        .logo-pas { width: 280px; text-align: left; }
        .logo-pas img { max-height: 80px; max-width: 260px; }
        .logo-wings { width: 140px; text-align: right; }
        .logo-wings img { max-height: 36px; max-width: 130px; }
        .title-box { text-align: left; padding-left: 10px; }
        .title-pms { font-size: 13pt; font-weight: bold; color: #c00000; line-height: 1.2; }
        .title-pk { font-size: 11pt; font-weight: bold; color: #c00000; text-decoration: underline; line-height: 1.2; margin-top: 2px; }
        .periode-badge { display: inline-block; background-color: #c00000; color: #ffffff; font-weight: bold; font-size: 9pt; padding: 3px 8px; border-radius: 2px; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.info td { border: 1px solid #777; padding: 2px 5px; font-size: 7pt; vertical-align: middle; }
        table.info td.lbl { background: #b7b7b7; font-weight: bold; color: #000; }

        table.val { width: 100%; table-layout: fixed; border-collapse: collapse; }
        table.val th { background: #c00000; color: #fff; padding: 3px 2px; font-size: 6.5pt; border: 1px solid #000; font-weight: bold; text-align: center; word-wrap: break-word; overflow: hidden; }
        table.val th.th-assess { background: #fdd960; color: #000; font-size: 5.8pt; line-height: 1.1; }
        table.val td { border: 1px solid #000; padding: 2px 2px; font-size: 5.8pt; vertical-align: top; word-wrap: break-word; overflow: hidden; }
        table.val td.ctr { text-align: center; font-weight: bold; }
        table.val tr.sec-row td { background: #d8d8d8; font-weight: bold; font-size: 7pt; color: #000; padding: 3px 5px; }

    </style>
</head>
<body>

<table class="head">
    <tr>
        <td class="logo-pas">
            @if($logoSrc)<img src="{{ $logoSrc }}">@endif
        </td>
        <td class="title-box">
            <div class="title-pms">PERFORMANCE MANAGEMENT SYSTEM {{ $header['periode'] ?: '' }}</div>
            <div class="title-pk">PENILAIAN KARYA</div>
        </td>
        <td style="text-align: right; vertical-align: middle;">
            <div class="periode-badge">PERIODE PENILAIAN : {{ $header['periode'] ?: '-' }}</div>
        </td>
        <td class="logo-wings">
            @if($logoWingsSrc)<img src="{{ $logoWingsSrc }}">@endif
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <td class="lbl" style="width: 12%;">Nama</td>
        <td style="width: 38%;">: {{ $header['nama'] }}</td>
        <td class="lbl" style="width: 16%;">Manager</td>
        <td style="width: 34%;">: {{ $header['manager'] ?: '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">NIK</td>
        <td>: {{ $header['nik'] }}</td>
        <td class="lbl">Atasan Langsung</td>
        <td>: {{ $header['associate_manager'] ?: ($header['manager'] ?: '-') }}</td>
    </tr>
    <tr>
        <td class="lbl">Jabatan</td>
        <td>: {{ $header['jabatan'] }}</td>
        <td class="lbl">Jumlah Bawahan Langsung</td>
        <td>: {{ $header['jumlah_bawahan'] !== null ? $header['jumlah_bawahan'] : '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Departemen</td>
        <td>: {{ $header['departemen'] }}</td>
        <td class="lbl">Pendidikan</td>
        <td>: {{ $header['pendidikan'] ?: '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Bagian</td>
        <td>: {{ $header['sub_departemen'] ?: '-' }}</td>
        <td class="lbl">Tanggal Join</td>
        <td>: {{ $header['tgl_masuk'] ?: '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Level</td>
        <td>: {{ $header['level'] }}</td>
        <td class="lbl">Usia</td>
        <td>: {{ $header['usia'] ?: '-' }}</td>
    </tr>
</table>

<table class="val">
    <colgroup>
        @if($showLeader)
            <col style="width: 3%;">
            <col style="width: 8%;">
            <col style="width: 13.5%;">
            <col style="width: 16.5%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 4.5%;">
            <col style="width: 4.5%;">
        @else
            <col style="width: 3%;">
            <col style="width: 8.5%;">
            <col style="width: 15%;">
            <col style="width: 18%;">
            <col style="width: 10.1%;">
            <col style="width: 10.1%;">
            <col style="width: 10.1%;">
            <col style="width: 10.1%;">
            <col style="width: 10.1%;">
            <col style="width: 5%;">
        @endif
    </colgroup>
    <thead>
        <tr>
            <th rowspan="2">NO</th>
            <th rowspan="2">AREA</th>
            <th rowspan="2">COMPETENCY BEHAVIOR</th>
            <th rowspan="2">KEY BEHAVIOR</th>
            <th colspan="5" style="text-align: center;">SCORING CRITERIA</th>
            <th rowspan="2" class="th-assess">Self<br>Assessment</th>
            @if($showLeader)
            <th rowspan="2" class="th-assess">Leader<br>Assessment</th>
            @endif
        </tr>
        <tr>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
        </tr>
    </thead>
    <tbody>
        @php $lastSec = null; @endphp
        @foreach ($rows as $r)
            @if($r['section'] !== $lastSec)
                @php $lastSec = $r['section']; @endphp
                <tr class="sec-row">
                    <td colspan="{{ $showLeader ? 11 : 10 }}">{{ $lastSec }}</td>
                </tr>
            @endif
            <tr>
                <td class="ctr" style="font-size: 6.5pt;">{{ $r['no_urut'] }}</td>
                <td style="background: #fbe6d5; font-weight: bold; font-size: 6.2pt;">{!! nl2br(e($r['area'] ?: '-')) !!}</td>
                <td style="font-size: 6pt;">
                    @php
                        $cb = (string) ($r['competency_behavior'] ?? '');
                        $lines = preg_split('/\R/', $cb, 2);
                    @endphp
                    <b>{{ $lines[0] ?? '' }}</b>
                    @if(!empty($lines[1]))
                        <div style="margin-top: 2px; font-size: 5.5pt; color: #222;">{!! nl2br(e(trim($lines[1]))) !!}</div>
                    @endif
                </td>
                <td style="font-size: 5.8pt;">{!! nl2br(e($r['key_behavior'] ?? '-')) !!}</td>
                <td style="font-size: 5.4pt; vertical-align: top;">{!! nl2br(e($r['kriteria_1'] ?? '-')) !!}</td>
                <td style="font-size: 5.4pt; vertical-align: top;">{!! nl2br(e($r['kriteria_2'] ?? '-')) !!}</td>
                <td style="font-size: 5.4pt; vertical-align: top;">{!! nl2br(e($r['kriteria_3'] ?? '-')) !!}</td>
                <td style="font-size: 5.4pt; vertical-align: top;">{!! nl2br(e($r['kriteria_4'] ?? '-')) !!}</td>
                <td style="font-size: 5.4pt; vertical-align: top;">{!! nl2br(e($r['kriteria_5'] ?? '-')) !!}</td>
                <td class="ctr" style="font-size: 7.5pt; vertical-align: middle;">{{ $r['nilai_self'] !== null ? $r['nilai_self'] : '-' }}</td>
                @if($showLeader)
                <td class="ctr" style="font-size: 7.5pt; vertical-align: middle;">{{ $r['nilai_leader'] !== null ? $r['nilai_leader'] : '-' }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

@if($showAccumulation)
<div style="margin-top: 14px; page-break-inside: avoid;">
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 4px; color: #000;">PENILAIAN KARYA ACCUMULATION @if(!empty($isFinal))<span style="display:inline-block; border: 1px solid #a80000; color: #a80000; border-radius: 4px; padding: 0 6px; font-size: 7.5pt;">FINAL</span>@endif</div>
    <table style="width: 440px; border-collapse: collapse; border: 1px solid #000; font-size: 7.5pt;">
        <thead>
            <tr>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 220px;">Category</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 110px;">Self Assessment</th>
                <th style="background-color: #c00000; color: #ffffff; text-align: center; font-weight: bold; padding: 5px 8px; border: 1px solid #000; width: 110px;">Leader Assessment</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">WINGS CORE VALUE</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ $accumulation['wings_self'] !== null ? number_format($accumulation['wings_self'], 2) : '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ $accumulation['wings_leader'] !== null ? number_format($accumulation['wings_leader'], 2) : '-' }}</td>
            </tr>
            @if(trim((string) ($header['group'] ?? '')) === '2-3' || ($accumulation['behavior_self'] ?? null) !== null || ($accumulation['behavior_leader'] ?? null) !== null)
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">BEHAVIOR</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ ($accumulation['behavior_self'] ?? null) !== null ? number_format($accumulation['behavior_self'], 2) : '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ ($accumulation['behavior_leader'] ?? null) !== null ? number_format($accumulation['behavior_leader'], 2) : '-' }}</td>
            </tr>
            @endif
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">LEADERSHIP COMPETENCY</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ !empty($no_leadership) ? '' : ($accumulation['leadership_self'] !== null ? number_format($accumulation['leadership_self'], 2) : '-') }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff;">{{ !empty($no_leadership) ? '' : ($accumulation['leadership_leader'] !== null ? number_format($accumulation['leadership_leader'], 2) : '-') }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<div style="margin-top: 14px; page-break-inside: avoid;">
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 4px; color: #000;">STATUS PERSETUJUAN</div>
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
            @if(!empty($approval['am']['nama']) || !empty($approval['am']['nik']))
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">Associate Manager</td>
                <td style="border: 1px solid #000; padding: 5px 8px; background: #fff;">{{ $approval['am']['nama'] ?: '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ $approval['am']['nik'] ?: '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff; color: {{ $approval['am']['approved'] ? '#1b5e20' : '#a80000' }};">{{ $approval['am']['approved'] ? 'Disetujui' : 'Belum disetujui' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ !empty($approval['am']['approved_at']) ? \Carbon\Carbon::parse($approval['am']['approved_at'])->format('d M Y H:i') : '-' }}</td>
            </tr>
            @endif
            <tr>
                <td style="border: 1px solid #000; padding: 5px 8px; font-weight: bold; text-align: center; background: #fff;">Manager</td>
                <td style="border: 1px solid #000; padding: 5px 8px; background: #fff;">{{ $approval['manager']['nama'] ?: '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ $approval['manager']['nik'] ?: '-' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; font-weight: bold; background: #fff; color: {{ $approval['manager']['approved'] ? '#1b5e20' : '#a80000' }};">{{ $approval['manager']['approved'] ? 'Disetujui' : 'Belum disetujui' }}</td>
                <td style="border: 1px solid #000; padding: 5px 8px; text-align: center; background: #fff;">{{ !empty($approval['manager']['approved_at']) ? \Carbon\Carbon::parse($approval['manager']['approved_at'])->format('d M Y H:i') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
