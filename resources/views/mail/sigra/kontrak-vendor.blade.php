<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>(FYI) Sigra Notification Kontrak Vendor</title>
</head>

<body>
    <h3 style="text-align: center">Sigra Notification Kontrak Vendor <span
            style="font-size: 5px; color: #eee">{{ date('YmdHis') }}</span></h3>

    <p style="margin-top: 10px; margin-bottom: 10px; background-color: #eee; padding: 5px">Diinformasikan
        sertifikat-sertifikat berikut akan atau sudah expired. Harap untuk ditindak lanjuti agar tidak menerima email
        notifikasi ini berikutnya. <span style="font-size: 5px; color: #eee">{{ date('YmdHis') }}</span></p>

    <div style="width: 98%">
        <table style="width: 100%; border-collapse: collapse; margin-left: 15px; margin-light: 15px">
            <thead>
                <tr style="background-color: #eee">
                    <th style="border: 1px solid #333;padding: 8px;">No</th>
                    <th style="border: 1px solid #333;padding: 8px;">PT</th>
                    <th style="border: 1px solid #333;padding: 8px;">NAMA VENDOR</th>
                    <th style="border: 1px solid #333;padding: 8px;">JENIS PEKERJAAN</th>
                    <th style="border: 1px solid #333;padding: 8px;">TANGGAL EXPIRED</th>
                    <th style="border: 1px solid #333;padding: 8px;">DUE DATE</th>
                    <th style="border: 1px solid #333;padding: 8px;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kontrak_vendor as $key => $kontrak)
                    <tr style="background-color: {{ $kontrak->due_date > 0 ? '#FFC857' : '#C5283D' }}">
                        <td style="border: 1px solid #333;padding: 8px;">{{ $key + 1 }}</td>
                        <td style="border: 1px solid #333;padding: 8px;">{{ $kontrak->perusahaan }}</td>
                        <td style="border: 1px solid #333;padding: 8px;">{{ $kontrak->nama_vendor }}</td>
                        <td style="border: 1px solid #333;padding: 8px;">{{ $kontrak->jenis_pekerjaan }}</td>
                        <td style="border: 1px solid #333;padding: 8px;">
                            {{ @formatTanggalIndonesia($kontrak->tanggal_selesai) }}</td>
                        <td style="border: 1px solid #333;padding: 8px;">{{ $kontrak->due_date }} hari</td>
                        <td style="border: 1px solid #333;padding: 8px;">
                            {{ $kontrak->due_date > 0 ? 'Hampir Expired' : 'Expired' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7"><span style="font-size: 5px; color: #eee">{{ date('YmdHis') }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <p style="margin-top: 30px; margin-bottom: 20px">*Note : <i>Notifikasi ini di genereate oleh system. Dan tidak perlu
            dibalas.</i> <span style="font-size: 5px; color: #eee">{{ date('YmdHis') }}</span></p>
</body>

</html>
