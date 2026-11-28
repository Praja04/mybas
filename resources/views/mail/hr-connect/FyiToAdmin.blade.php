<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Karyawan Baru - HR Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border-top: 5px solid #3699ff;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #2c3e50;
        }

        .content {
            margin-bottom: 25px;
        }

        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3699ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }

        .highlight-text {
            font-size: 1.2rem;
            font-weight: bold;
            color: #3699ff;
            display: block;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
            padding-top: 15px;
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3699ff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Pemberitahuan Karyawan Baru Masuk</h2>
        </div>

        <div class="content">
            <p>Halo Tim Admin Department,</p>
            <p>Menginformasikan bahwa terdapat data karyawan baru masuk dari tim GA yang memerlukan proses pembagian penempatan (plot) lebih lanjut pada sistem HR Connect.</p>

            <div class="highlight-box">
                <span class="highlight-text">Jumlah Karyawan Baru: {{ $hitung_karyawan_baru }} Orang</span>
            </div>

            <p>Silakan klik tombol di bawah ini untuk melihat detail data karyawan dan melakukan proses plot karyawan baru (IN/NO-IN).</p>

            <div style="text-align: center;">
                <a href="{{ $link }}" class="btn">Buka Aplikasi HR Connect</a>
            </div>

            <p style="margin-top: 30px;">Terima kasih atas kerja samanya.</p>
            <p><strong>Sistem GA - HR Connect</strong></p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem (BAS / HR Connect). Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>

</html>
