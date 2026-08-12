<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Peringatan - PT Bumi Alam Segar</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.8cm 1.5cm 1.8cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 2px;
            margin-top: 10px;
            margin-bottom: 2px;
        }
        .header-subtitle {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-data td {
            padding: 2px 0;
            vertical-align: top;
        }
        .paragraph {
            text-align: justify;
            margin-bottom: 10px;
            text-indent: 0;
        }
        .signature-section {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-section td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-space {
            height: 60px;
        }
        .legal-notice {
            margin-top: 25px;
            font-size: 8pt;
            font-style: italic;
            color: #333333;
            line-height: 1.2;
        }
        .footer-code {
            margin-top: 20px;
            text-align: left;
            font-size: 8.5pt;
            color: #666666;
            line-height: 1.2;
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

    // Group lookup from pkw_group
    $groupVal = $emp ? ($emp->kode_group ?? $emp->group ?? null) : null;
    $groupStr = '-';
    if (!empty($groupVal)) {
        $grp = \DB::table('pkw_group')->where('id', $groupVal)->orWhere('kode_group', $groupVal)->first();
        $groupStr = $grp ? ($grp->nama_group ?? $grp->kode_group) : $groupVal;
    }
    if (empty($groupStr)) { $groupStr = '-'; }

    $noSpRaw = $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT');
    $displayNoSp = (stripos($noSpRaw, 'No.') === 0) ? $noSpRaw : "No. {$noSpRaw}";

    $logoPath = public_path('assets/images/logo_kop_bas.png');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

    <!-- Kop Surat Resmi PT Bumi Alam Segar (Kop Baru BAS) -->
    <div class="kop-header" style="text-align: center; margin-bottom: 12px; border-bottom: 1.5px solid #000000; padding-bottom: 6px;">
        @if(!empty($logoSrc))
            <img src="{{ $logoSrc }}" style="width: 100%; max-width: 680px; height: auto;" alt="Kop Surat PT Bumi Alam Segar">
        @else
            <img src="data:image/png;base64,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" style="width: 100%; max-width: 680px; height: auto;" alt="Kop Surat PT Bumi Alam Segar">
        @endif
    </div>

    <div class="header-title">S U R A T   P E R I N G A T A N</div>
    <div class="header-subtitle">{{ $displayNoSp }}</div>

    <p style="margin-bottom: 8px;">Surat Peringatan ini diberikan kepada :</p>

    <table class="table-data">
        <tr>
            <td style="width: 5%;">I.</td>
            <td style="width: 28%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 65%;"><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $sp->employee->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>Dept/Bagian</td>
            <td>:</td>
            <td>{{ $deptBagianStr }}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>Group</td>
            <td>:</td>
            <td>{{ $groupStr }}</td>
        </tr>

        <tr><td colspan="4" style="padding: 3px 0;"></td></tr>

        <tr>
            <td>II.</td>
            <td>Tingkat Peringatan</td>
            <td>:</td>
            <td><strong>{{ $sp->jenis_pelanggaran }}</strong></td>
        </tr>

        <tr><td colspan="4" style="padding: 3px 0;"></td></tr>

        <tr>
            <td>III.</td>
            <td>Bentuk Pelanggaran</td>
            <td>:</td>
            <td>{{ $sp->alasan }}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>Tanggal Pelanggaran</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->translatedFormat('j F Y') }}</td>
        </tr>

        <tr><td colspan="4" style="padding: 3px 0;"></td></tr>

        <tr>
            <td>IV.</td>
            <td>Dasar Pertimbangan SP</td>
            <td>:</td>
            <td>{{ $sp->pasal_dilanggar ?: 'Perjanjian Kerja Bersama Tahun 2024 - 2026' }}</td>
        </tr>

        <tr><td colspan="4" style="padding: 3px 0;"></td></tr>

        <tr>
            <td>V.</td>
            <td>Masa Berlaku SP</td>
            <td>:</td>
            <td>6 ( Enam ) Bulan.</td>
        </tr>
    </table>

    <div style="margin-top: 15px;">
        <p class="paragraph">
            Surat peringatan ini merupakan bentuk pembinaan yang diberikan agar saudara/i dapat memperbaiki diri dan tidak mengulangi pelanggaran yang sama atau pelanggaran lain dikemudian hari.
        </p>
        <p class="paragraph">
            Bahwa apabila dalam masa berlakunya surat peringatan ini yang bersangkutan kembali melakukan pelanggaran, maka akan diberikan sanksi sesuai Perjanjian Kerja Bersama dan peraturan perundangan yang berlaku.
        </p>
    </div>

    <div style="margin-top: 25px; text-align: left; margin-left: 50%;">
        Bekasi, {{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran ?: now())->translatedFormat('j F Y') }}
    </div>

@php
    // ── Load TTD Digital ─────────────────────────────────────────────────────
    // TTD IR Head (kolom "Dibuat,")
    $ttdIrHead = \App\SpDigitalSignature::where('role', 'ir_head')
        ->where('is_active', true)
        ->first();

    // TTD Dept Head (kolom "Diketahui,") — cari berdasarkan user yang di-assign
    $ttdDeptHead = null;
    if ($sp->assigned_dept_head_id) {
        $ttdDeptHead = \App\SpDigitalSignature::where('user_id', $sp->assigned_dept_head_id)
            ->where('is_active', true)
            ->first();
    }

    $irHeadName    = $ttdIrHead ? ($ttdIrHead->user->name ?? 'IR & ER Dept. Head') : 'Fransiscus Xaverius WH';
    $irHeadJabatan = $ttdIrHead ? ($ttdIrHead->nama_jabatan ?: 'IR & ER Dept. Head') : 'IR & ER Dept. Head';
    $dhName        = $sp->deptHead ? $sp->deptHead->name : ($ttdDeptHead && $ttdDeptHead->user ? $ttdDeptHead->user->name : 'Dept. Head');
    $dhJabatan     = $ttdDeptHead ? ($ttdDeptHead->nama_jabatan ?: ($divisiName ?: 'Dept. Head') . ' Dept. Head') : (($divisiName ?: 'Produksi') . ' Dept. Head');
@endphp

    <table class="signature-section">
        <tr>
            <td style="text-align: center;">
                <div>Dibuat,</div>
                @if($ttdIrHead && $ttdIrHead->signature_base64)
                    <div style="height:70px; display:flex; align-items:center; justify-content:center;">
                        <img src="{{ $ttdIrHead->signature_base64 }}"
                             style="max-height:65px; max-width:180px; object-fit:contain;"
                             alt="TTD IR Head">
                    </div>
                @else
                    <div class="signature-space"></div>
                @endif
                <div><strong><u>{{ $irHeadName }}</u></strong></div>
                <div>{{ $irHeadJabatan }}</div>
            </td>
            <td style="text-align: center;">
                <div>Diketahui,</div>
                @if($ttdDeptHead && $ttdDeptHead->signature_base64)
                    <div style="height:70px; display:flex; align-items:center; justify-content:center;">
                        <img src="{{ $ttdDeptHead->signature_base64 }}"
                             style="max-height:65px; max-width:180px; object-fit:contain;"
                             alt="TTD Dept Head">
                    </div>
                @else
                    <div class="signature-space"></div>
                @endif
                <div><strong><u>{{ $dhName }}</u></strong></div>
                <div>{{ $dhJabatan }}</div>
            </td>
        </tr>
    </table>

    <div class="legal-notice">
        *Informasi elektronik dan/atau dokumen elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah sesuai dengan Undang-Undang Republik Indonesia Nomor 11 Tahun 2008 Tentang Informasi dan Transaksi Elektronik.
    </div>

    <div class="footer-code">
        <div>FRM-IER-002-003</div>
        <div>Rev.01 - 08 Mei 2023</div>
    </div>

</body>
</html>
