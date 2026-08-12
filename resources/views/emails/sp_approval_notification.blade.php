<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Approval Surat Peringatan</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .banner {
            background: linear-gradient(135deg, #14856f, #0f766e);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .banner h1 {
            margin: 0 0 8px 0;
            font-size: 20pt;
            font-weight: bold;
        }
        .banner p {
            margin: 0;
            font-size: 11pt;
            opacity: 0.9;
        }
        .content {
            padding: 25px 25px;
        }
        .salutation {
            font-size: 12pt;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 12px;
        }
        .intro-text {
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 20px;
            color: #475569;
        }
        .detail-card {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .detail-card h3 {
            text-align: center;
            margin: 0 0 15px 0;
            color: #0f766e;
            font-size: 12pt;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .detail-table td {
            padding: 7px 0;
            border-bottom: 1px dashed #cbd5e1;
        }
        .detail-table tr:last-child td {
            border-bottom: none;
        }
        .label {
            color: #0f766e;
            font-weight: bold;
            width: 38%;
        }
        .value {
            color: #1e293b;
            font-weight: 500;
        }
        .btn-container {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        .btn-approve {
            display: inline-block;
            background-color: #14856f;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11pt;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 8.5pt;
            color: #94a3b8;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

@php
    $emp = $sp->employee ?? null;
    $divisiName = null;
    $bagianName = null;

    if ($emp) {
        if (!empty($emp->kode_divisi)) {
            $div = \DB::table('pkw_divisi')->where('id', $emp->kode_divisi)->orWhere('kode_divisi', $emp->kode_divisi)->first();
            if ($div) {
                $divisiName = $div->nama_divisi ?? $div->kode_divisi;
            } else {
                $dept = \DB::table('departments')->where('id', $emp->kode_divisi)->first();
                $divisiName = $dept ? $dept->name : $emp->kode_divisi;
            }
        }
        if (!empty($emp->kode_bagian)) {
            $bag = \DB::table('pkw_bagian')->where('id', $emp->kode_bagian)->orWhere('kode_bagian', $emp->kode_bagian)->first();
            $bagianName = $bag ? ($bag->nama_bagian ?? $bag->kode_bagian) : $emp->kode_bagian;
        }
    }

    $deptBagianStr = '-';
    if ($divisiName && $bagianName) {
        $deptBagianStr = $divisiName . ' - ' . $bagianName;
    } elseif ($divisiName) {
        $deptBagianStr = $divisiName;
    } elseif ($bagianName) {
        $deptBagianStr = $bagianName;
    }

    $deptHeadName = $sp->deptHead ? $sp->deptHead->name : 'Dept Head';
    $approvalUrl = url('/sp-pelanggaran/approval');
    $isMangkir = ($sp->sumber_data === 'MANGKIR');
    $jenisSp = $isMangkir ? 'SP Mangkir' : 'SP Pelanggaran';
    $titleBanner = $isMangkir ? 'Approval SP Mangkir Karyawan' : 'Approval Surat Peringatan';
    $subtitleBanner = $isMangkir ? 'Data mangkir/alpha karyawan menunggu persetujuan Anda' : 'Permintaan persetujuan baru menunggu Anda';
    $kodeLabel = $isMangkir ? ($sp->kode_admin ?: 'Mangkir ' . $sp->mangkir_ke) : ($sp->kode_admin ?: '-');
    $bentukLabel = $isMangkir 
        ? ('Karyawan tercatat mangkir/alpha' . ($sp->mangkir_ke ? ' ke-' . $sp->mangkir_ke . ' dalam bulan tersebut' : '')) 
        : ($sp->alasan ?: '-');
@endphp

<div class="email-container">
    <div class="banner">
        <h1>{{ $titleBanner }}</h1>
        <p>{{ $subtitleBanner }}</p>
    </div>

    <div class="content">
        <div class="salutation">Yth. Bapak/Ibu {{ strtoupper($deptHeadName) }},</div>
        
        <div class="intro-text">
            Ada pengajuan <strong>{{ $jenisSp }}</strong> baru yang membutuhkan persetujuan Anda sebagai <strong>Manager / Dept Head</strong>:
        </div>

        <div class="detail-card">
            <h3>Detail Pengajuan {{ $jenisSp }}</h3>
            <table class="detail-table">
                <tr>
                    <td class="label">Nama Karyawan</td>
                    <td class="value"><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="value"><code>{{ $sp->employee->nik ?? '-' }}</code></td>
                </tr>
                <tr>
                    <td class="label">Departemen / Bagian</td>
                    <td class="value">{{ $deptBagianStr }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Kejadian</td>
                    <td class="value">{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">{{ $isMangkir ? 'Kode Mangkir' : 'Kode Pelanggaran' }}</td>
                    <td class="value"><strong>{{ $kodeLabel }}</strong></td>
                </tr>
                @if(!$isMangkir)
                <tr>
                    <td class="label">Jenis SP</td>
                    <td class="value">{{ $sp->jenis_pelanggaran ?: '(Akan ditetapkan IR Staff)' }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">{{ $isMangkir ? 'Keterangan' : 'Bentuk Pelanggaran' }}</td>
                    <td class="value">{{ $bentukLabel }}</td>
                </tr>
            </table>
        </div>

        <div class="btn-container">
            <a href="{{ $approvalUrl }}" class="btn-approve" target="_blank">Check & Approval SP</a>
        </div>
    </div>

    <div class="footer">
        Email notifikasi otomatis dari Sistem SP Online PT Bumi Alam Segar. Mohon tidak membalas email ini.
    </div>
</div>

</body>
</html>
