<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap'); */
        @media print {
            .break-the-page {page-break-after: always;}
        }
        body {
            font-family: sans-serif !important;
        }
    
        table, td, th {
            border: 1px solid;
            padding: 0 !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
            line-height: 16px;
            font-size: 12px
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
        }
    
        .keterangan_nama {
            display: flex;
            justify-content: space-between;
        }
    </style>
    @stack('styles')
</head>
<body>
    <table>
        <thead>
        <tr>
            <th colspan="12" style="text-align: center">PT. PRAKARSA ALAM SEGAR</th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center">HALO SECURITY</th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center">BERITA ACARA S.O.P SUPIR</th>
        </tr>
        <tr>
            <th scope="col" class="text-center" style="text-align: center;">No</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Supir</th>
            <th scope="col" class="text-center" style="text-align: center;">Ekspedisi</th>
            <th scope="col" class="text-center" style="text-align: center;">No.KTP</th>
            <th scope="col" class="text-center" style="text-align: center;">No.Polisi</th>
            <th scope="col" class="text-center" style="text-align: center;">No.Handphone</th>
            <th scope="col" class="text-center" style="text-align: center;">No.Kartu</th>
            <th scope="col" class="text-center" style="text-align: center;">Alamat</th>
            <th scope="col" class="text-center" style="text-align: center;">Shift</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Pembuat Dokumen</th>
            <th scope="col" class="text-center" style="text-align: center;">Jabatan Pembuat Dokumen</th>
            <th scope="col" class="text-center" style="text-align: center;">Tanggal Dokumen S.O.P Supir</th>
        </tr>
        </thead>
        <tbody>
        @foreach($basopsupir as $key => $item)
        <tr>
            <td style="text-align: center;">{{ ($key+1) }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->ekspedisi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_ktp }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_polisi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_handphone }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_kartu }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->alamat }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->shift }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_pembuat }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jabatan_pembuat }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->created_at }}</td>
        @endforeach
        </tbody>
    </table>
</body>
</html>