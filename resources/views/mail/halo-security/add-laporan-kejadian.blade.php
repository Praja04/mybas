<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>(Halo Security) Terdapat Data Baru - Berita Acara Laporan Kejadian</title>
    <style>
        body {
            padding: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #6A6A6A;
        }
    </style>
</head>

<body>
    <p style="text-align: center;">
        <img style="width: 200px" src="{{ $message->embed(public_path('assets/media/logos/logo_bas_compress.png')) }}"
            alt="Kop Surat">
    </p>

    <h2 style="text-align: center; color: #6A6A6A;">
        <span style="color: red; font-weight: bold;">Data Baru</span> - Berita Acara Laporan Kejadian
    </h2>

    <p style="text-align: center; color: #6A6A6A;">
        Telah ditambahkan data baru pada <strong>BA Laporan Kejadian</strong>.<br>
        Silakan akses link di bawah ini untuk melihat atau mencetak laporan terkait.
    </p>

    <table style="width:100%">

        <thead>
            <tr>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Informasi</th>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Link</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">
                    Melihat detail data BA Laporan Kejadian terbaru
                </td>
                <td style="font-weight: bold; text-align: center; color: black;">
                    <a href="{{ route('ba-list-laporankejadian') }}" style="text-decoration: none; color:red;">
                        Buka Menu BA Laporan Kejadian
                    </a>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">
                    Download PDF BA Laporan Kejadian
                </td>
                <td style="font-weight: bold; text-align: center; color: black;">
                    <a href="{{ route('printpdf.laporankejadian', $lk_id) }}" style="text-decoration: none; color:red;">
                        Download PDF
                    </a>
                </td>
            </tr>
        </tbody>

    </table>

    <span style="font-size:1px;color:transparent;">{{ now() }}</span>

</body>

</html>
