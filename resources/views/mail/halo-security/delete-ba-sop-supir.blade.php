<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>(Halo Security) Delete Berita Acara S.O.P Supir</title>
    <style>
        table, th, td {
          border:1px solid #6A6A6A;
        }
    </style>
</head>
<body>
    <p style="text-align: center;"><img style="width: 500px; height: 150px;" src="https://www.forumhrdindonesia.com/wp-content/uploads/2021/02/Perkasa-Alam-Segar-PAS.png" alt="Kop Surat"></p>
    <h2 style="text-align: center; color: #6A6A6A;"><span style="color: red; font-weight: bold;">Delete</span> - (Halo Security) Berita Acara S.O.P Supir</h2>
    <p style="text-align: center; color: #6B6A6B;">Ada data berita acara sop supir yang dihapus, silahkan hubungi ke pihak security POS 1 atau POS 2</p>
    <table style="width:100%; margin-top: 10px;">
        <thead>
            <tr>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Informasi</th>
                <th scope="col" style="font-weight: bold; color: white; background-color: #AF2120;">Link</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">Data berita acara s.o.p supir yang dihapus atas nama supir :</td>
                <td style="font-weight: bold; text-align: center; color: red;">{{ $supir->nama }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center; color: #6B6A6B;">Untuk melihat data berita acara s.o.p supir yang dihapus silahkan masuk melalui link berikut ini:</td>
                <td style="font-weight: bold; text-align: center; color: black;"><a href="{{ route('listsupir.trash') }}" style="text-decoration: none; color:red;">Menu Recycling BA S.O.P Supir</a></td>
            </tr>
        </tbody>
    </table>
</body>
</html>