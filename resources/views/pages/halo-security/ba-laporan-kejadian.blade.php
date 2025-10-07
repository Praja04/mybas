<?php

define("DOMPDF_FONT_HEIGHT_RATIO", 0.75);

?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <base href="{{ url('/') }}">
		<title>Berita Acara Laporan Kejadian</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
		{{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> --}}
		<style>
            /* @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap'); */
            /* @media print { */
                .break-the-page {
                    /* page-break-after: always; */
                    page-break-after: auto;
                }
            /* } */
            
            body {
                font-family: sans-serif !important;
                margin: 0; /* Remove default body margin */
                position: relative;
            }

            .container {
                margin: 20px; /* Adjust the margin as needed */
            }

            table, tr, td {
                border: 1px solid;
                padding: 0 !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
                line-height: 16px;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                margin-bottom: 20px; /* Adjust the margin as needed */
            }

            .footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                /* text-align: center; */
            }

            /* @media print { */
                .page_break { page-break-before: always; }
            /* } */
            
		</style>

		@stack('styles')
		<script>var hostUrl = "{{ asset('assets') }}/";</script>
		{{-- <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> --}}
	</head>
	<body style="font-family: 'sans-serif'; color: #000">
        <div class="container">
            <div class="card shadow-none border-0">
                <div class="card-body border-0">
                    @if ($item->faktas->count() == 1)
                        @php
                            $rowCounter = 0;
                        @endphp
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="3" colspan="4" style="text-align: center">
                                        <img style="width: 160px; height: 80px" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/bas_text.png')) }}" alt="Kop Surat">
                                    </td>
                                    <td colspan="4" style="text-align: center">
                                        <h1 style="font-size: 18px;">BERITA ACARA <br><br> LAPORAN KEJADIAN</h1>
                                    </td>
                                    <td rowspan="3" colspan="4" style="text-align: center">
                                        <img style="width: 120px; height: 75px;" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/delta guard.jpg')) }}" alt="delta guard">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: center">
                                        <h3>No. <span style="margin-left: 10px;">/LK/PT.BAS/DGP/VII/2023</span></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <h3 style="font-size: 14px; margin-left: 10px;">Rev:</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>1.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: -2px; margin-bottom: -2px;">JENIS KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="11" style="text-align: center">
                                        @if ($item->jenis_kejadian == 'kecelakaan lalu lintas')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Lalu Lintas</p>
                                        @elseif($item->jenis_kejadian == 'penemuan barang')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Penemuan Barang</p>
                                        @elseif($item->jenis_kejadian == 'kecelakaan kerja')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Kerja</p>
                                        @elseif($item->jenis_kejadian == 'pencurian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pencurian</p>
                                        @elseif($item->jenis_kejadian == 'perkelahian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Perkelahian</p>
                                        @elseif($item->jenis_kejadian == 'tindak kekerasan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindak Kekerasan</p>
                                        @elseif($item->jenis_kejadian == 'kebakaran')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kebakaran</p>
                                        @elseif($item->jenis_kejadian == 'demonstrasi')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Demonstrasi</p>
                                        @elseif($item->jenis_kejadian == 'tindakan asusila')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Asusila</p>
                                        @elseif($item->jenis_kejadian == 'pengerusakan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pengerusakan</p>
                                        @elseif($item->jenis_kejadian == 'tindakan indispliner')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Indispliner</p>
                                        @else
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">{{ $item->jenis_kejadian }}</p>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p>2.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">LAPORAN AWAL</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Dari (Nama) : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">d.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Laporan : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Nik : {{ $item->nik_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">e.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Laporan : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Perusahaan : {{ $item->perusahaan_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">f.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; margin-left: -2px; margin-right: -2px; font-size: 13px;">Bagian : {{ $item->bagian_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3" style="text-align: center; font-weight: bold;"><p>3.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">PENJELASAN KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Kejadian : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Kejadian : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Lokasi Kejadian : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>4.</p></td>
                                    <td colspan="11" style="font-weight: bold; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">FAKTA - FAKTA KEJADIAN</p></td>
                                </tr>
                                @foreach ($item->faktas as $result)
                                <tr>
                                    <td colspan="11">
                                        <p>{{ $loop->iteration }}. <span style="margin-left: 10px; text-align: justify;">{{ $result->keterangan_fakta }}</span></p>
                                    </td>
                                </tr>
                                @endforeach
                                
                                {{-- @foreach ($item->faktas as $result)
                                    @if ($rowCounter % 3 == 0 && $rowCounter > 0)
                                        </tbody>
                                        </table>
                                        <div class="page_break"></div>
                                        <table class="table table-bordered" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td rowspan="{{ $item->faktas->filter(function($result) { return strlen($result->keterangan_fakta) > 3; })->count() - 2 }}" style="text-align: center; font-weight: bold;"></td>
                                                </tr>
                                    @endif

                                    <tr>
                                        <td @if(strlen($result->keterangan_fakta) > 3) colspan="11" @else colspan="8" @endif>
                                            <p>{{ $loop->iteration }}. <span style="margin-left: 10px; text-align: justify;">{{ $result->keterangan_fakta }}</span></p>
                                        </td>
                                    </tr>

                                    @php
                                        $rowCounter++;
                                    @endphp
                                @endforeach --}}
                                <tr>
                                    <td rowspan="9" style="text-align: center; font-weight: bold;"><p>5.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">2.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">3.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->yang_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">4.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">5.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">6.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->bagaimana_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">7.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->mengapa_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">8.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                
                                {{-- <div class="page_break"></div> --}}
                            </tbody>
                        </table>
                        {{-- <div class="page_break"></div> --}}
                        {{-- <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="9" colspan="1" style="text-align: center; font-weight: bold;"><p>5.</p></td>
                                    <td colspan="10" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">2.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">3.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->yang_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">4.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">5.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">6.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->bagaimana_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">7.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->mengapa_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">8.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="{{ $item->saksis->count() + 2 }}" style="text-align: center; font-weight: bold;"><p>6.</p></td>
                                    <td colspan="10" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Nama</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">(NIK)</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Departement</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Keterangan</p>
                                    </td>
                                </tr>
                                @foreach ($item->saksis as $result)
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->nik_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->departement_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->keterangan_saksi }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table> --}}
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="{{ $item->saksis->count() + 2 }}" style="text-align: center; font-weight: bold;"><p>6.</p></td>
                                    <td colspan="14" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Nama</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">(NIK)</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Departement</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Keterangan</p>
                                    </td>
                                </tr>
                                @foreach ($item->saksis as $result)
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->nik_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->departement_saksi }}</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->keterangan_saksi }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>7.</p></td>
                                    <td colspan="14" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207); color: white;"><p style="margin-top: 10px; margin-bottom: 10px;"></p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="13">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Dokumentasi Terlampir</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>8.</p></td>
                                    <td colspan="14" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">URAIAN KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="14" style="text-align: justify;">
                                        {!! $item->uraian_kejadian !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>9.</p></td>
                                    <td colspan="14" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">TINDAKAN PENGAMANAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="14" style="text-align: justify;">
                                        {!! $item->tindakan_pengamanan !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 25px; padding-right: 19px;">10.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">HASIL DARI TINDAKAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        {!! $item->hasil_daritindakan !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 28px; padding-right: 21px;">11.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">SARAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        {!! $item->saran !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p style="padding-left: 20px; padding-right: 13px;">12.</p></td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Dibuat oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Diperiksa oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Diketahui oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Disahkan oleh :</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">
                                        @if (!empty($item->danru))
                                            {{ $item->danru }}
                                        @else
                                            Otis Sutisna
                                        @endif
                                    </td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Sumito</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Yusman</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Indra Bayu</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center">Danru</td>
                                    <td colspan="2" style="text-align: center">Chief Security</td>
                                    <td colspan="2" style="text-align: center">Koordinator Security</td>
                                    <td colspan="2" style="text-align: center">HRD & GA PT BAS</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-weight: bold;"><p>DOKUMENTASI</p></td>
                                        <td colspan="4" style="text-align: center; font-weight: bold;"><p style="padding-right: 2px; padding-left: 5px;">KETERANGAN</p></td>
                                    </tr>
                                    @foreach ($item->dokumentasis->sortBy('id') as $result)
                                    <tr>
                                        <td style="font-size: 13px; text-align:center; vertical-align: top;">
                                            <p style="margin-top: 20px;">{{ $loop->iteration }}.</p>
                                        </td>          
                                        <td colspan="6">
                                            <br>
                                            <div style="display: flex; flex-direction: column; margin-bottom: 20px; margin-top: 35px; text-align:center; gap: 5px;">
                                                @foreach (explode(',', $result->foto_kejadian) as $image)
                                                    <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./master_laporan_kejadian_gambar/" . $image)) }}" style="width: 150px; height: 150px; margin-right: 10px;">
                                                @endforeach
                                            </div>
                                        </td>
                                        <td colspan="4" style="text-align:center; vertical-align: top;">
                                            <p style="font-size: 13px; margin-left: 10px; text-align: justify;">{{ $result->keterangan_kejadian }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                            <p style="margin: 0; margin-left: 5px; font-weight: bold; line-height: 10px; font-size: 12px">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p>
                        </div>
                    @elseif($item->faktas->count() == 7 && $item->saksis->count() == 1 || $item->faktas->count() == 7 && $item->saksis->count() > 1)
                        @php
                            $rowCounter = 0;
                        @endphp
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="3" colspan="5" style="text-align: center">
                                        <img style="width: 180px; height: 80px" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/bas_text.png')) }}" alt="Kop Surat">
                                    </td>
                                    <td colspan="5" style="text-align: center">
                                        <h1 style="font-size: 18px;">BERITA ACARA <br><br> LAPORAN KEJADIAN</h1>
                                    </td>
                                    <td rowspan="3" colspan="5" style="text-align: center">
                                        <img style="width: 120px; height: 75px;" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/delta guard.jpg')) }}" alt="delta guard">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="text-align: center">
                                        <h3>No. <span style="margin-left: 10px;">/LK/PT.BAS/DGP/VII/2023</span></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <h3 style="font-size: 14px; margin-left: 10px;">Rev:</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>1.</p></td>
                                    <td colspan="14" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: -2px; margin-bottom: -2px;">JENIS KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="14" style="text-align: center">
                                        @if ($item->jenis_kejadian == 'kecelakaan lalu lintas')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Lalu Lintas</p>
                                        @elseif($item->jenis_kejadian == 'penemuan barang')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Penemuan Barang</p>
                                        @elseif($item->jenis_kejadian == 'kecelakaan kerja')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Kerja</p>
                                        @elseif($item->jenis_kejadian == 'pencurian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pencurian</p>
                                        @elseif($item->jenis_kejadian == 'perkelahian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Perkelahian</p>
                                        @elseif($item->jenis_kejadian == 'tindak kekerasan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindak Kekerasan</p>
                                        @elseif($item->jenis_kejadian == 'kebakaran')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kebakaran</p>
                                        @elseif($item->jenis_kejadian == 'demonstrasi')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Demonstrasi</p>
                                        @elseif($item->jenis_kejadian == 'tindakan asusila')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Asusila</p>
                                        @elseif($item->jenis_kejadian == 'pengerusakan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pengerusakan</p>
                                        @elseif($item->jenis_kejadian == 'tindakan indispliner')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Indispliner</p>
                                        @else
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">{{ $item->jenis_kejadian }}</p>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p>2.</p></td>
                                    <td colspan="14" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">LAPORAN AWAL</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Dari (Nama) : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">d.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Laporan : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Nik : {{ $item->nik_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">e.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Laporan : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Perusahaan : {{ $item->perusahaan_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">f.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; margin-left: -2px; margin-right: -2px; font-size: 13px;">Bagian : {{ $item->bagian_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3" style="text-align: center; font-weight: bold;"><p>3.</p></td>
                                    <td colspan="14" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">PENJELASAN KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Kejadian : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="6">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Kejadian : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="13">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Lokasi Kejadian : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="5" style="text-align: center; font-weight: bold;"><p>4.</p></td>
                                    <td colspan="14" style="font-weight: bold; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">FAKTA - FAKTA KEJADIAN</p></td>
                                </tr>
                                @foreach ($item->faktas as $result)
                                    @if ($rowCounter % 4 == 0 && $rowCounter > 0)
                                        </tbody>
                                        </table>
                                        <div class="page_break"></div>
                                        <table class="table table-bordered" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    {{-- <td rowspan="{{ $item->faktas->filter(function($result) { return strlen($result->keterangan_fakta) > 3; })->count() - 2 }}" style="text-align: center; font-weight: bold;"></td> --}}
                                                    <td rowspan="4" style="text-align: center; font-weight: bold;"></td>
                                                </tr>
                                    @endif

                                    <tr>
                                        <td @if(strlen($result->keterangan_fakta) > 4) colspan="14" @else colspan="10" @endif>
                                            <p style="text-align: justify;">{{ $loop->iteration }}. <span style="margin-left: 10px;">{{ $result->keterangan_fakta }}</span></p>
                                        </td>
                                    </tr>

                                    @php
                                        $rowCounter++;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td rowspan="9" style="text-align: center; font-weight: bold;"><p>5.</p></td>
                                    <td colspan="14" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">2.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">3.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: justify;"> : {{ $item->yang_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">4.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: justify;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">5.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: justify;"> : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">6.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: justify;"> : {{ $item->bagaimana_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">7.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: justify;"> : {{ $item->mengapa_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">8.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                
                                {{-- <div class="page_break"></div> --}}
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="{{ $item->saksis->count() + 2 }}" style="text-align: center; font-weight: bold;"><p>6.</p></td>
                                    <td colspan="15" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Nama</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">(NIK)</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Departement</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Keterangan</p>
                                    </td>
                                </tr>
                                @foreach ($item->saksis as $result)
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->nik_saksi }}</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->departement_saksi }}</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->keterangan_saksi }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>7.</p></td>
                                    <td colspan="16" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207); color: white;"><p style="margin-top: 10px; margin-bottom: 10px;"></p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="15">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Dokumentasi Terlampir</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>8.</p></td>
                                    <td colspan="16" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">URAIAN KEJADIAN</p></td>
                                </tr>
                                @php
                                    // Split uraian kejadian menjadi array kalimat
                                    $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $item->uraian_kejadian);
                                    // Hitung jumlah kalimat per halaman
                                    $sentences_per_page = ceil(count($sentences) / 2); // Dibagi 2 karena akan dibagi ke dalam dua halaman
                                @endphp
                                <tr>
                                    <td colspan="16" style="text-align: justify;">
                                        @foreach ($sentences as $index => $sentence)
                                            {!! $sentence !!}
                                            @if ($index + 1 == $sentences_per_page)
                                                {{-- Tambahkan page break di sini --}}
                                                {{-- <div class="page_break"></div> --}}
                                            @endif
                                            @if ($index + 1 == $sentences_per_page)
                                                {{-- Jika sudah mencapai akhir halaman kedua, hentikan perulangan --}}
                                                @break
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <!-- Tabel untuk halaman kedua -->
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <!-- Baris untuk halaman kedua -->
                                {{-- <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>8.</p></td>
                                    <td colspan="16" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">URAIAN KEJADIAN (Halaman 2)</p></td>
                                </tr> --}}
                                <tr>
                                    <td style="text-align: center; font-weight: bold;"></td>
                                    <td colspan="16" style="text-align: justify;">
                                        @php
                                            $remaining_sentences = array_slice($sentences, $sentences_per_page);
                                        @endphp
                                        @foreach ($remaining_sentences as $index => $sentence)
                                            {!! $sentence !!}
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>9.</p></td>
                                    <td colspan="16" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">TINDAKAN PENGAMANAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="16" style="text-align: justify;">
                                        {!! $item->tindakan_pengamanan !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>10.</p></td>
                                    <td colspan="15" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">HASIL DARI TINDAKAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="15" style="text-align: justify;">
                                        {!! $item->hasil_daritindakan !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>11.</p></td>
                                    <td colspan="15" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">SARAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="15" style="text-align: justify;">
                                        {!! $item->saran !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p>12.</p></td>
                                    <td colspan="3" style="font-weight: bold; text-align: center">Dibuat oleh :</td>
                                    <td colspan="4" style="font-weight: bold; text-align: center">Diperiksa oleh :</td>
                                    <td colspan="4" style="font-weight: bold; text-align: center">Diketahui oleh :</td>
                                    <td colspan="4" style="font-weight: bold; text-align: center">Disahkan oleh :</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><br><br><br><br><br></td>
                                    <td colspan="4"><br><br><br><br><br></td>
                                    <td colspan="4"><br><br><br><br><br></td>
                                    <td colspan="4"><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-weight: bold; text-align: center;">
                                        @if (!empty($item->danru))
                                            {{ $item->danru }}
                                        @else
                                            Otis Sutisna
                                        @endif
                                    </td>
                                    <td colspan="4" style="font-weight: bold; text-align: center;">Sumito</td>
                                    <td colspan="4" style="font-weight: bold; text-align: center;">Yusman</td>
                                    <td colspan="4" style="font-weight: bold; text-align: center;">Indra Bayu</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: center">Danru</td>
                                    <td colspan="4" style="text-align: center">Chief Security</td>
                                    <td colspan="4" style="text-align: center">Koordinator Security</td>
                                    <td colspan="4" style="text-align: center">HRD & GA PT BAS</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-weight: bold;"><p>DOKUMENTASI</p></td>
                                        <td colspan="4" style="text-align: center; font-weight: bold;"><p style="padding-right: 2px; padding-left: 5px;">KETERANGAN</p></td>
                                    </tr>
                                    @foreach ($item->dokumentasis->sortBy('id') as $result)
                                    <tr>
                                        <td style="font-size: 13px; text-align:center; vertical-align: top;">
                                            <p style="margin-top: 20px;">{{ $loop->iteration }}.</p>
                                        </td>          
                                        <td colspan="6">
                                            <br>
                                            <div style="display: flex; flex-direction: column; margin-bottom: 20px; margin-top: 35px; text-align:center; gap: 5px;">
                                                @foreach (explode(',', $result->foto_kejadian) as $image)
                                                    <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./master_laporan_kejadian_gambar/" . $image)) }}" style="width: 150px; height: 150px; margin-right: 10px;">
                                                @endforeach
                                            </div>
                                        </td>
                                        <td colspan="4" style="text-align:center; vertical-align: top;">
                                            <p style="font-size: 13px; margin-left: 10px; text-align: justify;">{{ $result->keterangan_kejadian }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                            <p style="margin: 0; margin-left: 5px; font-weight: bold; line-height: 10px; font-size: 12px">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p>
                        </div>
                    @else
                        @php
                            $rowCounter = 0;
                        @endphp
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="3" colspan="4" style="text-align: center">
                                        <img style="width: 160px; height: 80px" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/bas_text.png')) }}" alt="Kop Surat">
                                    </td>
                                    <td colspan="4" style="text-align: center">
                                        <h1 style="font-size: 18px;">BERITA ACARA <br><br> LAPORAN KEJADIAN</h1>
                                    </td>
                                    <td rowspan="3" colspan="4" style="text-align: center">
                                        <img style="width: 120px; height: 75px;" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/delta guard.jpg')) }}" alt="delta guard">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align: center">
                                        <h3>No. <span style="margin-left: 10px;">/LK/PT.BAS/DGP/VII/2023</span></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <h3 style="font-size: 14px; margin-left: 10px;">Rev:</h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>1.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: -2px; margin-bottom: -2px;">JENIS KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="11" style="text-align: center">
                                        @if ($item->jenis_kejadian == 'kecelakaan lalu lintas')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Lalu Lintas</p>
                                        @elseif($item->jenis_kejadian == 'penemuan barang')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Penemuan Barang</p>
                                        @elseif($item->jenis_kejadian == 'kecelakaan kerja')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kecelakaan Kerja</p>
                                        @elseif($item->jenis_kejadian == 'pencurian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pencurian</p>
                                        @elseif($item->jenis_kejadian == 'perkelahian')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Perkelahian</p>
                                        @elseif($item->jenis_kejadian == 'tindak kekerasan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindak Kekerasan</p>
                                        @elseif($item->jenis_kejadian == 'kebakaran')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Kebakaran</p>
                                        @elseif($item->jenis_kejadian == 'demonstrasi')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Demonstrasi</p>
                                        @elseif($item->jenis_kejadian == 'tindakan asusila')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Asusila</p>
                                        @elseif($item->jenis_kejadian == 'pengerusakan')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Pengerusakan</p>
                                        @elseif($item->jenis_kejadian == 'tindakan indispliner')
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">Tindakan Indispliner</p>
                                        @else
                                            <p style="margin-top: -2px; margin-bottom: -2px; font-size: 13px;">{{ $item->jenis_kejadian }}</p>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p>2.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">LAPORAN AWAL</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Dari (Nama) : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">d.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Laporan : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Nik : {{ $item->nik_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">e.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Laporan : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Perusahaan : {{ $item->perusahaan_korban }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">f.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; margin-left: -2px; margin-right: -2px; font-size: 13px;">Bagian : {{ $item->bagian_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="3" style="text-align: center; font-weight: bold;"><p>3.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">PENJELASAN KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">a.</p>
                                    </td>
                                    <td colspan="4">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Tanggal Kejadian : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">b.</p>
                                    </td>
                                    <td colspan="5">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Waktu Kejadian : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px; text-align: center;">c.</p>
                                    </td>
                                    <td colspan="10">
                                        <p style="margin-top: 2px; margin-bottom: 2px; font-size: 13px;">Lokasi Kejadian : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p>4.</p></td>
                                    <td colspan="11" style="font-weight: bold; background-color: rgb(52, 52, 207);"><p style="margin: 2px;">FAKTA - FAKTA KEJADIAN</p></td>
                                </tr>
                                
                                @foreach ($item->faktas as $result)
                                    @if ($rowCounter % 3 == 0 && $rowCounter > 0)
                                        </tbody>
                                        </table>
                                        <div class="page_break"></div>
                                        <table class="table table-bordered" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td rowspan="{{ $item->faktas->filter(function($result) { return strlen($result->keterangan_fakta) > 3; })->count() - 2 }}" style="text-align: center; font-weight: bold;"></td>
                                                </tr>
                                    @endif

                                    <tr>
                                        <td @if(strlen($result->keterangan_fakta) > 3) colspan="11" @else colspan="8" @endif>
                                            <p style="text-align: justify;">{{ $loop->iteration }}. <span style="margin-left: 10px;">{{ $result->keterangan_fakta }}</span></p>
                                        </td>
                                    </tr>

                                    @php
                                        $rowCounter++;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td rowspan="9" style="text-align: center; font-weight: bold;"><p>5.</p></td>
                                    <td colspan="11" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">2.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">3.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->yang_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">4.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">5.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">6.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->bagaimana_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">7.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->mengapa_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">8.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="{{ $item->saksis->count() + 2 }}" style="text-align: center; font-weight: bold;"><p>6.</p></td>
                                    <td colspan="11" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Nama</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">(NIK)</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Departement</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Keterangan</p>
                                    </td>
                                </tr>
                                @foreach ($item->saksis as $result)
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->nik_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->departement_saksi }}</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->keterangan_saksi }}</p>
                                    </td>
                                </tr>
                                @endforeach
                                {{-- <div class="page_break"></div> --}}
                            </tbody>
                        </table>
                        {{-- <div class="page_break"></div> --}}
                        {{-- <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="9" colspan="1" style="text-align: center; font-weight: bold;"><p>5.</p></td>
                                    <td colspan="10" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">2.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">3.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->yang_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">4.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">5.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_korban }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">6.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->bagaimana_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">7.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->mengapa_terjadi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">8.</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="{{ $item->saksis->count() + 2 }}" style="text-align: center; font-weight: bold;"><p>6.</p></td>
                                    <td colspan="10" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Nama</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">(NIK)</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Departement</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Keterangan</p>
                                    </td>
                                </tr>
                                @foreach ($item->saksis as $result)
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                    </td>
                                    <td colspan="3">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->nik_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->departement_saksi }}</p>
                                    </td>
                                    <td colspan="2">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">{{ $result->keterangan_saksi }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table> --}}
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p>7.</p></td>
                                    <td colspan="8" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207); color: white;"><p style="margin-top: 10px; margin-bottom: 10px;"></p></td>
                                </tr>
                                <tr>
                                    <td>
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                    </td>
                                    <td colspan="7">
                                        <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Dokumentasi Terlampir</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 28px; padding-right: 28px;">8.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">URAIAN KEJADIAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8" style="text-align: justify;">
                                        {!! $item->uraian_kejadian !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 20px; padding-right: 16px;">9.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">TINDAKAN PENGAMANAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8" style="text-align: justify;">
                                        {!! $item->tindakan_pengamanan !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break"></div>
                        <table class="table table-bordered" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 25px; padding-right: 19px;">10.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">HASIL DARI TINDAKAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        {!! $item->hasil_daritindakan !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 28px; padding-right: 21px;">11.</p></td>
                                    <td colspan="8" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">SARAN</p></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        {!! $item->saran !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="4" style="text-align: center; font-weight: bold;"><p style="padding-left: 20px; padding-right: 13px;">12.</p></td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Dibuat oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Diperiksa oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Diketahui oleh :</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center">Disahkan oleh :</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                    <td colspan="2"><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">
                                        @if (!empty($item->danru))
                                            {{ $item->danru }}
                                        @else
                                            Otis Sutisna
                                        @endif
                                    </td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Sumito</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Yusman</td>
                                    <td colspan="2" style="font-weight: bold; text-align: center;">Indra Bayu</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center">Danru</td>
                                    <td colspan="2" style="text-align: center">Chief Security</td>
                                    <td colspan="2" style="text-align: center">Koordinator Security</td>
                                    <td colspan="2" style="text-align: center">HRD & GA PT BAS</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-weight: bold;"><p>DOKUMENTASI</p></td>
                                        <td colspan="4" style="text-align: center; font-weight: bold;"><p style="padding-right: 2px; padding-left: 5px;">KETERANGAN</p></td>
                                    </tr>
                                    @foreach ($item->dokumentasis->sortBy('id') as $result)
                                    <tr>
                                        <td style="font-size: 13px; text-align:center; vertical-align: top;">
                                            <p style="margin-top: 20px;">{{ $loop->iteration }}.</p>
                                        </td>          
                                        <td colspan="6">
                                            <br>
                                            <div style="display: flex; flex-direction: column; margin-bottom: 20px; margin-top: 35px; text-align:center; gap: 5px;">
                                                @foreach (explode(',', $result->foto_kejadian) as $image)
                                                    <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./master_laporan_kejadian_gambar/" . $image)) }}" style="width: 150px; height: 150px; margin-right: 10px;">
                                                @endforeach
                                            </div>
                                        </td>
                                        <td colspan="4" style="text-align:center; vertical-align: top;">
                                            <p style="font-size: 13px; margin-left: 10px; text-align: justify;">{{ $result->keterangan_kejadian }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                            <p style="margin: 0; margin-left: 5px; font-weight: bold; line-height: 10px; font-size: 12px">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p>
                        </div>
                    @endif

                    {{-- Hasil Lama --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="4" style="text-align: center; font-weight: bold;"><p style="padding-right: 1px;">2.</p></td>
                                <td colspan="4" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">LAPORAN AWAL</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">a.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dari (Nama) : Sdr. {{ $item->nama_korban }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">d.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tanggal Laporan : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">b.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Nik : {{ $item->nik_korban }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">e.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Laporan : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">c.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Perusahaan : {{ $item->perusahaan_korban }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">f.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagian : {{ $item->bagian_korban }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="3" style="text-align: center; font-weight: bold;"><p style="padding-right: 23px; padding-left: 26px;">3.</p></td>
                                <td colspan="4" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PENJELASAN KEJADIAN</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">a.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tanggal Kejadian : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">b.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian : {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">c.</p>
                                </td>
                                <td colspan="3">
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Lokasi Kejadian : {{ $item->lokasi_kejadian }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-right: 33px; padding-left: 35px;">4.</p></td>
                                <td colspan="2" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">FAKTA - FAKTA KEJADIAN</p></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    @foreach ($item->faktas as $result)
                                    <p>{{ $loop->iteration }}. <span style="margin-left: 10px;">{{ $result->keterangan_fakta }}</span></p>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="6" style="text-align: center; font-weight: bold;"><p style="padding-right: 32px; padding-left: 35px;">5.</p></td>
                                <td colspan="3" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">PERISTIWA YANG DILAPORKAN</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">1.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Waktu Kejadian</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sekira pukul {{ date('H.i', strtotime($item->created_at)) }} wib</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">2.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Tempat Kejadian</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->lokasi_kejadian }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">3.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Apa yang terjadi</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->yang_terjadi }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">4.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Siapa Terlapor</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_terlapor }}, Umur : {{ $item->umur_terlapor }}, Tempat Tanggal Lahir : {{ $item->ttl_terlapor }}, Pekerjaan : {{ $item->pekerjaan_terlapor }}, Alamat : {{ $item->alamat_terlapor }}, Kelurahan : {{ $item->kelurahan_terlapor }}, Kecamatan : {{ $item->kecamatan_terlapor }}, Provinsi : {{ $item->provinsi_terlapor }}, Status : {{ $item->status_terlapor }}, Agama : {{ $item->agama_terlapor }}, Kebangsaan : {{ $item->kebangsaan_terlapor }}, No. KTP : {{ $item->no_ktp_terlapor }}, No. SIM (C) : {{ $item->no_simc_terlapor }}, No. HP : {{ $item->no_hp_terlapor }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="5" style="text-align: center; font-weight: bold;"><p style="padding-right: 30px; padding-left: 35px;"></p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">5.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Korbannya</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Sdr. {{ $item->nama_korban }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">6.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Bagaimana Terjadi</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->bagaimana_terjadi }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">7.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Mengapa Terjadi</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : {{ $item->mengapa_terjadi }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">8.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Dilaporkan Pada</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;"> : Pada hari {{ formatHariIndonesia($item->created_at) }} {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="5" style="text-align: center; font-weight: bold; border-color: black; background-color: rgb(52, 52, 207);"><p style="margin-top: 10px; margin-bottom: 10px;">SAKSI - SAKSI</p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; text-align: center; font-size: 13px; padding-right: 8px; padding-left: 6px;">No.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Nama</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">(NIK)</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Departement</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">Keterangan</p>
                                </td>
                            </tr>
                            @foreach ($item->saksis as $result)
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; text-align: center; margin-bottom: 5px; font-size: 13px; padding-right: 8px; padding-left: 6px;">{{ $loop->iteration }}.</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nama_saksi }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->nik_saksi }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->departement_saksi }}</p>
                                </td>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px;">{{ $result->keterangan_saksi }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="">6.</p></td>
                                <td colspan="4" style="font-weight: bold; border-color: black; background-color: rgb(52, 52, 207); color: white;"><p style="margin-top: 10px; margin-bottom: 10px;"></p></td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">1.</p>
                                </td>
                                <td colspan="3">
                                    <p style="margin-top: 5px; margin-bottom: 5px; font-size: 13px; text-align: center;">Dokumentasi Terlampir</p>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 28px; padding-right: 28px;">7.</p></td>
                                <td colspan="4" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">URAIAN KEJADIAN</p></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {!! $item->uraian_kejadian !!}
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 20px; padding-right: 16px;">8.</p></td>
                                <td colspan="4" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">TINDAKAN PENGAMANAN</p></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {!! $item->tindakan_pengamanan !!}
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 25px; padding-right: 19px;">9.</p></td>
                                <td colspan="4" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">HASIL DARI TINDAKAN</p></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {!! $item->hasil_daritindakan !!}
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="2" style="text-align: center; font-weight: bold;"><p style="padding-left: 28px; padding-right: 21px;">10.</p></td>
                                <td colspan="4" style="font-weight: bold;"><p style="margin-top: 10px; margin-bottom: 10px;">SARAN</p></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    {!! $item->saran !!}
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td rowspan="4" style="text-align: center; font-weight: bold;"><p style="padding-left: 20px; padding-right: 13px;">11.</p></td>
                                <td style="font-weight: bold; text-align: center">Dibuat oleh :</td>
                                <td style="font-weight: bold; text-align: center">Diperiksa oleh :</td>
                                <td style="font-weight: bold; text-align: center">Diketahui oleh :</td>
                                <td style="font-weight: bold; text-align: center">Disahkan oleh :</td>
                            </tr>
                            <tr>
                                <td><br><br><br><br><br></td>
                                <td><br><br><br><br><br></td>
                                <td><br><br><br><br><br></td>
                                <td><br><br><br><br><br></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; text-align: center;">Suherman Prasetia</td>
                                <td style="font-weight: bold; text-align: center;">Sumito</td>
                                <td style="font-weight: bold; text-align: center;">Yusman</td>
                                <td style="font-weight: bold; text-align: center;">Nancy Krisnawati</td>
                            </tr>
                            <tr>
                                <td style="text-align: center">Danru</td>
                                <td style="text-align: center">Chief Security</td>
                                <td style="text-align: center">Koordinator Security</td>
                                <td style="text-align: center">Chief Supervisor HRD GA</td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                    <p style="margin: 0; margin-left: 5px; font-weight: bold; line-height: 10px; font-size: 12px">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p> --}}
                    {{-- <br><br><br><br><br><br> --}}
                    {{-- <br><br><br><br><br><br> --}}
                    

                    {{-- <div class="footer-container">
                        <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                        <p class="footer-text">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p>
                    </div> --}}
                    {{-- <div class="footer">
                        <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div>
                        <p style="margin: 0; margin-left: 5px; font-weight: bold; line-height: 10px; font-size: 12px">FRM-GAP-002-017 <br />Rev.01 - 23 Maret 2023</p>
                    </div> --}}
                </div>
            </div>
        </div>
	</body>
	<!--end::Body-->
</html>