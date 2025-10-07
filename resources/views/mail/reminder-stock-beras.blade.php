<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reminder Stock Beras</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 30px auto; background: #ffffff; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
        <div style="background-color: #C0151A; color: #ffffff; padding: 10px 20px; text-align: center;">
            <h1 style="margin: 0;">Reminder Stock Beras</h1>
        </div>
        <div style="padding: 20px;">
            <p style="font-size: 16px; color: #333;">Jumlah beras saat ini adalah <span style="color: red;">{{ $jumlah_stock_sesudah }}</span>. Pastikan Anda telah mengupdate nya.</p>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tr>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2;">Tanggal</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2;">Jumlah Stock</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2;">Transaksi Masuk</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2;">Transaksi Keluar</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2;">Jumlah Stock Sesudah</th>
                </tr>
                @foreach ($berasJumlahData as $data)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $data->tanggal }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $data->jumlah_stock }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $data->transaksi_masuk }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $data->transaksi_keluar }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; color: {{ $data->jumlah_stock_sesudah < 20 ? 'red' : 'black' }}">{{ $data->jumlah_stock_sesudah }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="text-align: center; font-size: 12px; color: #999999; padding: 10px 20px; border-top: 1px solid #ddd;">
            <p style="margin: 0;">&copy; PT BUMI ALAM SEGAR</p>
        </div>
    </div>
</body>
</html>
