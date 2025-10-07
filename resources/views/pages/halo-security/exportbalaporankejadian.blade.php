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
            <th colspan="35" style="text-align: center">PT. PRAKARSA ALAM SEGAR</th>
        </tr>
        <tr>
            <th colspan="35" style="text-align: center">HALO SECURITY</th>
        </tr>
        <tr>
            <th colspan="35" style="text-align: center">BERITA ACARA LAPORAN KEJADIAN</th>
        </tr>
        <tr>
            <th scope="col" class="text-center" style="text-align: center;">No</th>
            <th scope="col" class="text-center" style="text-align: center;">ID Berita Acara Laporan Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Jenis Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK (Nomor Induk Karyawan) Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">Perusahaan Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">Bagian Korban</th>
            <th scope="col" class="text-center" style="text-align: center;">Lokasi Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Fakta Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Yang Terjadi</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Umur Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Tempat Tanggal Lahir Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Pekerjaan Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Alamat Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Kelurahan Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Kecamatan Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Provinsi Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Status Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Agama Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Kebangsaan Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">No KTP Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">No SIM Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">No HP Terlapor</th>
            <th scope="col" class="text-center" style="text-align: center;">Bagaimana Terjadi</th>
            <th scope="col" class="text-center" style="text-align: center;">Mengapa Terjadi</th>
            <th scope="col" class="text-center" style="text-align: center;">Nama Saksi</th>
            <th scope="col" class="text-center" style="text-align: center;">NIK Saksi</th>
            <th scope="col" class="text-center" style="text-align: center;">Departement Saksi</th>
            <th scope="col" class="text-center" style="text-align: center;">Keterangan Saksi</th>
            <th scope="col" class="text-center" style="text-align: center;">Uraian Kejadian</th>
            <th scope="col" class="text-center" style="text-align: center;">Tindakan Pengamanan</th>
            <th scope="col" class="text-center" style="text-align: center;">Hasil Dari Tindakan</th>
            <th scope="col" class="text-center" style="text-align: center;">Saran</th>
            <th scope="col" class="text-center" style="text-align: center;">Tanggal Laporan Kejadian</th>
        </tr>
        </thead>
        <tbody>
        @foreach($balaporankejadian as $key => $item)
        @for ($i = 0; $i < collect([count($item->faktas), count($item->saksis)])->max(); $i++)
        <tr>
            <td style="text-align: center;">{{ ($key+1) }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->lk_id }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->jenis_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nik_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->perusahaan_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bagian_korban }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->lokasi_kejadian }}</td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->faktas[$i]))
                   {{ $item->faktas[$i]->lk_id }} - {{ $item->faktas[$i]->keterangan_fakta }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->yang_terjadi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->nama_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->umur_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->ttl_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->pekerjaan_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->alamat_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->kelurahan_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->kecamatan_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->provinsi_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->status_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->agama_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->kebangsaan_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_ktp_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_simc_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->no_hp_terlapor }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->bagaimana_terjadi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->mengapa_terjadi }}</td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->saksis[$i]))
                   {{ $item->saksis[$i]->lk_id }} - {{ $item->saksis[$i]->nama_saksi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->saksis[$i]))
                   {{ $item->saksis[$i]->lk_id }} - {{ $item->saksis[$i]->nik_saksi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->saksis[$i]))
                   {{ $item->saksis[$i]->lk_id }} - {{ $item->saksis[$i]->departement_saksi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">
                @if(isset($item->saksis[$i]))
                   {{ $item->saksis[$i]->lk_id }} - {{ $item->saksis[$i]->keterangan_saksi }}
                @endif
            </td>
            <td scope="row" class="text-center" style="text-align: center;">{!! $item->uraian_kejadian !!}</td>
            <td scope="row" class="text-center" style="text-align: center;">{!! $item->tindakan_pengamanan !!}</td>
            <td scope="row" class="text-center" style="text-align: center;">{!! $item->hasil_daritindakan !!}</td>
            <td scope="row" class="text-center" style="text-align: center;">{!! $item->saran !!}</td>
            <td scope="row" class="text-center" style="text-align: center;">{{ $item->created_at }}</td>
        </tr>
        @endfor
        @endforeach
        </tbody>
    </table>
</body>
</html>