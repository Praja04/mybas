<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Notifikasi Karyawan Keluar (Checkout)</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f4f6f9;
            padding: 20px;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Header dengan warna Kuning/Orange (Warning) */
        .header {
            background: #E5A93A;
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            padding: 30px;
        }

        /* Styling Table HTML untuk Email */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        .table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        .highlight {
            color: #d9534f;
            font-weight: bold;
        }

        /* Warna merah untuk alasan keluar */
        .attachment-note {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-left: 4px solid #6c757d;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #0ab39c;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eaeaea;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>Notifikasi Checkout Karyawan</h2>
        </div>

        <div class="content">
            <p>Halo <strong>Tim General Affair (GA)</strong>,</p>
            <p>Sistem HRConnect menginformasikan bahwa departemen terkait baru saja memproses <em>Checkout</em>
                (Karyawan Keluar). Berikut adalah rincian data karyawan yang membutuhkan tindak lanjut pencabutan
                fasilitas (loker/kunci):</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Alasan Keluar</th>
                        <th>Tgl Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list_karyawan as $karyawan)
                        <tr>
                            <td><strong>{{ $karyawan['nik'] }}</strong></td>
                            <td>{{ $karyawan['nama'] }}</td>
                            <td class="highlight">{{ $karyawan['alasan_keluar'] ?? 'Checkout Admin' }}</td>
                            <td>
                                {{ !empty($karyawan['tanggal_keluar']) ? \Carbon\Carbon::parse($karyawan['tanggal_keluar'])->format('d-m-Y') : now()->format('d-m-Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="attachment-note">
                <strong>Catatan:</strong> Data lengkap karyawan yang keluar ini juga telah dilampirkan dalam format
                <strong>Excel (.xlsx)</strong> pada email ini untuk mempermudah rekapitulasi tim GA.
            </div>

            <p>Silakan klik tombol di bawah ini untuk masuk ke halaman sistem dan menyelesaikan proses serah terima
                barang/loker.</p>

            <div class="btn-container">
                <a href="{{ $link }}" class="btn">Masuk ke Sistem GA</a>
            </div>
        </div>

        <div class="footer">
            <p>Email ini dihasilkan secara otomatis oleh sistem <strong>HRConnect</strong>.<br>Mohon untuk tidak
                membalas email ini.</p>
        </div>
    </div>

</body>

</html>
