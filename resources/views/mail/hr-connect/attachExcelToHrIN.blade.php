<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRConnect Notification - Karyawan Masuk</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f3f9;
            margin: 0;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background-color: #4F81BD;
            /* Warna Biru (Selaras dengan Header Excel) */
            color: #ffffff;
            padding: 20px 30px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
            font-size: 15px;
        }

        .content p {
            margin-bottom: 15px;
        }

        .highlight {
            font-weight: bold;
            color: #4F81BD;
            /* Mengikuti warna Header */
        }

        .footer {
            background-color: #f8f9fa;
            color: #878a99;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            border-top: 1px solid #e9ebec;
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="header">
            <h2>HRConnect System</h2>
        </div>

        <div class="content">
            <p>Halo Tim HR,</p>
            <p>Bersama email ini, kami lampirkan dokumen Excel yang berisi daftar <span class="highlight">Karyawan Masuk
                    (Ploting Admin)</span> yang telah berhasil diproses ke dalam sistem pada hari ini.</p>
            <p>Silakan unduh dan periksa file Excel yang terlampir pada email ini untuk melihat detail informasi seperti
                NIK, Nama Karyawan, Departemen, Bagian, serta kelengkapan ploting Grup & Admin.</p>
            <br>
            <p>Terima kasih,<br><strong>Sistem HRConnect</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} HRConnect. All rights reserved.</p>
            <p><i>Email ini dibuat secara otomatis oleh sistem. Mohon untuk tidak membalas email ini (No-Reply).</i></p>
        </div>
    </div>

</body>

</html>
