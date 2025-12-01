<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>(Halo Security) Terdapat Data Dihapus - Berita Acara Introgasi</title>
    <style>
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
        <span style="color: red; font-weight: bold;">Data Dihapus</span> - Berita Acara Introgasi
    </h2>

    <p style="text-align: center; color: #6B6A6B;">
        Terdapat data Berita Acara Introgasi yang dihapus, silahkan hubungi ke pihak security POS 1.
    </p>

    <table style="width:100%; margin-top: 10px;">

        <thead>
            <tr>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Informasi</th>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Link</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">Data berita acara introgasi yang
                    dihapus atas nama korban :</td>
                <td style="font-weight: bold; text-align: center; color: red;">{{ $item->nama_korban }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">Untuk melihat data berita acara
                    introgasi yang dihapus silahkan masuk melalui link berikut ini:</td>
                <td style="font-weight: bold; text-align: center; color: black;"><a href="{{ route('listbai.trash') }}"
                        style="text-decoration: none; color:red;">Menu Recycling BA Introgasi</a></td>
            </tr>
        </tbody>

    </table>

    <span style="font-size:1px;color:transparent;">{{ now() }}</span>

</body>

</html>
