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
@endphp

    <!-- Kop Surat Resmi PT Bumi Alam Segar -->
    <table class="kop-table">
        <tr>
            <td style="width: 12%; vertical-align: middle; text-align: left;">
                <table style="border-collapse: collapse; background-color: #b30000; width: 44px; height: 44px; text-align: center;">
                    <tr>
                        <td style="color: #ffffff; font-weight: bold; font-size: 18pt; font-family: Arial, sans-serif; text-align: center; vertical-align: middle;">
                            III
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 88%; vertical-align: middle; text-align: left;">
                <div style="font-size: 15pt; font-weight: bold; color: #b30000; font-family: Arial, sans-serif; letter-spacing: 0.5px; line-height: 1;">PAS</div>
                <div style="font-size: 9.5pt; font-weight: bold; color: #333333; font-family: Arial, sans-serif; text-transform: uppercase; margin-top: 2px;">PT. BUMI ALAM SEGAR</div>
                <div style="font-size: 8.5pt; color: #333333; font-family: Arial, sans-serif; margin-top: 3px; line-height: 1.2;">
                    Jalan Raya Kaliabang Bungur, Pondok Ungu, Desa Pejuang, Bekasi, Jawa Barat, 17131
                </div>
                <div style="font-size: 8.5pt; color: #333333; font-family: Arial, sans-serif; line-height: 1.2;">
                    Telp: (021) 8888 3226 &nbsp; Fax: (021) 8888 3227
                </div>
            </td>
        </tr>
    </table>

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

    <table class="signature-section">
        <tr>
            <td style="text-align: center;">
                <div>Dibuat,</div>
                <div class="signature-space"></div>
                <div><strong><u>Fransiscus Xaverius WH</u></strong></div>
                <div>IR & ER Dept. Head</div>
            </td>
            <td style="text-align: center;">
                <div>Diketahui,</div>
                <div class="signature-space"></div>
                <div><strong><u>{{ $sp->deptHead ? $sp->deptHead->name : 'Budi Santoso Setiawan' }}</u></strong></div>
                <div>{{ $divisiName ?: 'Produksi' }} Dept. Head</div>
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
