<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Kontrak Vendor - SIGRA Notification</title>
</head>

<body
    style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; background-color: #f9f9f9; padding: 20px;">
    <div
        style="max-width: 800px; margin: 0 auto; background-color: #fff; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 20px;">

        <h2 style="text-align: center; color: #a80000; margin-bottom: 10px;">
            SIGRA - Pengingat Kontrak Vendor
        </h2>

        <p style="text-align: center; font-size: 13px; color: #6c757d; margin-top: 0;">
            MyBAS mendeteksi adanya kontrak vendor yang akan atau telah melewati masa berlaku.
        </p>

        <p style="margin-top: 15px; line-height: 1.6;">
            Mohon perhatian untuk segera menindaklanjuti kontrak berikut agar tidak menerima notifikasi serupa pada
            periode berikutnya.
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background-color: #a80000; color: #fff;">
                    <th style="border: 1px solid #ddd; padding: 8px;">No</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Perusahaan</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Nama Vendor</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Jenis Pekerjaan</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Tanggal Expired</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Sisa Waktu</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kontrak_vendor as $key => $kontrak)
                    <tr style="background-color: {{ $kontrak->due_date > 0 ? '#FFF8E1' : '#FDECEA' }};">
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $key + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $kontrak->perusahaan }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $kontrak->nama_vendor }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $kontrak->jenis_pekerjaan }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            {{ @formatTanggalIndonesia($kontrak->tanggal_selesai) }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            {{ abs($kontrak->due_date) }} hari {{ $kontrak->due_date > 0 ? 'lagi' : 'lalu' }}
                        </td>
                        <td
                            style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold; color: {{ $kontrak->due_date > 0 ? '#856404' : '#721c24' }};">
                            {{ $kontrak->due_date > 0 ? 'Hampir Expired' : 'Sudah Expired' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 25px; font-size: 13px; color: #6c757d;">
            <strong>Catatan:</strong> Email ini dikirim otomatis oleh MyBAS. Anda tidak perlu membalas pesan ini.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

        <p style="text-align: center; font-size: 11px; color: #bbb;">
            &copy; {{ date('Y') }} MyBAS | Dikirim otomatis pada {{ date('d M Y H:i:s') }}
        </p>

    </div>
</body>

</html>
