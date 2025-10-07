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
            <th colspan="32" style="text-align: center">PT. PRAKARSA ALAM SEGAR</th>
        </tr>
        <tr>
            <th colspan="32" style="text-align: center">HALO SECURITY</th>
        </tr>
        <tr>
            <th colspan="32" style="text-align: center">BERITA ACARA INTROGASI</th>
        </tr>
        <tr>
            <th scope="col" class="text-center" style="text-align: center;">No</th>
            <th scope="col" class="text-center" style="text-align: center;">ID Berita Acara Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Jenis Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Umur Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Pekerjaan Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Bagian Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Pelapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Detail Barang Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Tempat Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK (Nomor Induk Karyawan) Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">Bagian Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Umur Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Tempat Tanggal Lahir Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Pekerjaan Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK (Nomor Induk Karyawan) Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Bagian Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Alamat Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Agama Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Suku Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Status Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Shift</th>
            <th scope="col" class="text-center" style="text-align: center;">Pendidikan Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK KTP Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">No HP Pelaku</th>
            <th scope="col" class="text-center" style="text-align: center;">Tempat Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Keterangan Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Pertanyaan Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Jawaban Introgasi</th>
            <th scope="col" class="text-center" style="text-align: center;">Tanggal Introgasi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($baintrogasi as $key => $item)
        @for ($i = 0; $i < collect(count($item->baiitems))->max(); $i++)
        <tr>
            <td style="text-align: center;">{{ ($key+1) }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bai_id }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jenis_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_introgasi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->umur_introgasi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->pekerjaan_introgasi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bagian_introgasi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_pelapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->detail_barang_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->tempat_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nik_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bagian_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->umur_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->ttl_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->pekerjaan_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nik_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bagian_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->alamat_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->agama_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->suku_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if ($item->status_pelaku == 'sudah kawin')
                    Sudah Kawin
                @elseif($item->status_pelaku == 'belum kawin')
                    Belum Kawin
                @else
                    Janda / Duda
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->shift }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->pendidikan_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nik_ktp_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_hp_pelaku }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->tempat_introgasi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->keterangan_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->baiitems[$i]))
                   {{ $item->baiitems[$i]->bai_id }} - {{ $item->baiitems[$i]->pertanyaan_introgasi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->baiitems[$i]))
                    {{ $item->baiitems[$i]->bai_id }} - {{ $item->baiitems[$i]->jawaban_introgasi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->created_at }}</td>
        </tr>
        @endfor
        @endforeach
        </tbody>
    </table>
</body>
</html>