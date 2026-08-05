<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi SP Karyawan - PT Bumi Alam Segar</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 8px;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-subtitle {
            font-size: 10pt;
            color: #555;
            margin-top: 3px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #1e3c72;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #102a56;
            text-align: center;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #cccccc;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 3px;
            color: #fff;
            text-align: center;
        }
        .badge-aktif { background-color: #28a745; }
        .badge-expired { background-color: #6c757d; }
        .badge-sp3 { background-color: #dc3545; }
        .badge-ditolak { background-color: #e74c3c; }
        .badge-cancel { background-color: #7f8c8d; }
        .badge-proses { background-color: #ffc107; color: #000; }
        .footer {
            margin-top: 15px;
            font-size: 8pt;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">PT. BUMI ALAM SEGAR</div>
        <div class="header-subtitle">LAPORAN REKAPITULASI & RIWAYAT SURAT PERINGATAN (SP) KARYAWAN</div>
    </div>

    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Filter Klasifikasi</strong></td>
            <td width="35%">: {{ $kategoriLabel }}</td>
            <td width="15%"><strong>Dicetak Pada</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Total Data</strong></td>
            <td>: {{ count($sps) }} Dokumen SP</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ auth()->user()->name ?? 'Administrator' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Nomor SP</th>
                <th width="8%">NIK</th>
                <th width="14%">Nama Karyawan</th>
                <th width="12%">Dept / Bagian</th>
                <th width="6%">Group</th>
                <th width="9%">Jenis SP</th>
                <th width="9%">Tgl Kejadian</th>
                <th width="9%">Berlaku Sampai</th>
                <th width="10%">Klasifikasi Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sps as $index => $sp)
            @php
                $emp = $sp->employee;
                $divisiName = null;
                $bagianName = null;

                if ($emp) {
                    if (!empty($emp->kode_divisi)) {
                        $div = \DB::table('pkw_divisi')->where('id', $emp->kode_divisi)->orWhere('kode_divisi', $emp->kode_divisi)->first();
                        $divisiName = $div ? ($div->nama_divisi ?? $div->kode_divisi) : (\DB::table('departments')->where('id', $emp->kode_divisi)->value('name') ?: $emp->kode_divisi);
                    }
                    if (!empty($emp->kode_bagian)) {
                        $bag = \DB::table('pkw_bagian')->where('id', $emp->kode_bagian)->orWhere('kode_bagian', $emp->kode_bagian)->first();
                        $bagianName = $bag ? ($bag->nama_bagian ?? $bag->kode_bagian) : $emp->kode_bagian;
                    }
                }
                $deptBagian = $divisiName && $bagianName ? "{$divisiName} - {$bagianName}" : ($divisiName ?: ($bagianName ?: '-'));
                $groupVal = $emp ? ($emp->kode_group ?? $emp->group ?? '-') : '-';

                $katClass = 'badge-proses';
                if ($sp->kategori_sp === 'AKTIF') $katClass = 'badge-aktif';
                elseif ($sp->kategori_sp === 'EXPIRED') $katClass = 'badge-expired';
                elseif ($sp->kategori_sp === 'SP3') $katClass = 'badge-sp3';
                elseif ($sp->kategori_sp === 'DITOLAK') $katClass = 'badge-ditolak';
                elseif ($sp->kategori_sp === 'CANCEL') $katClass = 'badge-cancel';
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $sp->nomor_sp_generated ?: ($sp->no_sp ?: 'DRAFT') }}</strong></td>
                <td>{{ $emp->nik ?? '-' }}</td>
                <td><strong>{{ $emp->nama ?? '-' }}</strong></td>
                <td>{{ $deptBagian }}</td>
                <td style="text-align: center;">{{ $groupVal ?: '-' }}</td>
                <td>{{ $sp->jenis_pelanggaran }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $sp->masa_berlaku_sampai ? \Carbon\Carbon::parse($sp->masa_berlaku_sampai)->format('d/m/Y') : '-' }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $katClass }}">{{ $sp->kategori_sp ?: $sp->current_status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 15px; color: #888;">Tidak ada data SP yang sesuai dengan filter yang dipilih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem SP Online PT Bumi Alam Segar
    </div>

</body>
</html>
