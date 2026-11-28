<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Surat Peringatan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Notifikasi Surat Peringatan (SP)</h2>
    <p>Halo,</p>
    <p>Berikut adalah rincian informasi Surat Peringatan:</p>
    <ul>
        <li><strong>Nomor SP:</strong> {{ $sp->nomor_sp_generated ?: ($sp->no_sp ?: '-') }}</li>
        <li><strong>Nama Karyawan:</strong> {{ $sp->employee->nama ?? '-' }}</li>
        <li><strong>NIK:</strong> {{ $sp->employee->nik ?? '-' }}</li>
        <li><strong>Jenis Pelanggaran:</strong> {{ $sp->jenis_pelanggaran }}</li>
        <li><strong>Tanggal Pelanggaran:</strong> {{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</li>
        <li><strong>Status Terkini:</strong> {{ $sp->current_status }}</li>
        <li><strong>Alasan:</strong> {{ $sp->alasan ?? '-' }}</li>
    </ul>
    <p>Silakan login ke portal sistem mybas untuk melihat detail lebih lanjut.</p>
    <br>
    <p>Terima kasih,</p>
    <p><strong>Tim HR / IR mybas</strong></p>
</body>
</html>
