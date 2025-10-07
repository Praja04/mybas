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
            <th colspan="11" style="text-align: center">PT. PRAKARSA ALAM SEGAR</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center">HALO SECURITY</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center">BERITA ACARA S.O.P KARYAWAN</th>
        </tr>
        <tr>
            <th scope="col" class="text-center" style="text-align: center;">No</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Karyawan</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK (Nomor Induk Karyawan)</th>
            <th scope="col" class="text-center" style="text-align: center;">Jabatan Karyawan</th>
            <th scope="col" class="text-center" style="text-align: center;">Jenis Kelamin</th>
            <th scope="col" class="text-center" style="text-align: center;">Shift</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Pembuat Dokumen</th>
            <th scope="col" class="text-center" style="text-align: center;">Jabatan Pembuat Dokumen</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Area</th>
            <th scope="col" class="text-center" style="text-align: center;">Barang</th>
            <th scope="col" class="text-center" style="text-align: center;">Tanggal Dokumen S.O.P Karyawan</th>
        </tr>
        </thead>
        <tbody>
        @foreach($basopkaryawan as $key => $item)
        <tr>
            <td style="text-align: center;">{{ ($key+1) }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nik }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jabatan }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jenis_kelamin }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->shift }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_pembuat }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jabatan_pembuat }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_area }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->barang }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->created_at }}</td>
        @endforeach
        </tbody>
    </table>
</body>
</html>