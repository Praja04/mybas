<?php

define("DOMPDF_FONT_HEIGHT_RATIO", 0.75);

?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <base href="{{ url('/') }}">
		<title>Berita Acara Introgasi</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
		{{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> --}}
		<style>
            /* @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap'); */
            @media print {
                .break-the-page {page-break-after: always;}
            }
            body {
                font-family: sans-serif !important;
            }

            table, td, th {
                /* border: 1px solid; */
                padding: 0 !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
                line-height: 16px;
                font-size: 12px
            }

            #kolomttd  td {
                border: solid rgb(0, 0, 0) 1px !important; 
                text-align: center; 
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
		<script>var hostUrl = "{{ asset('assets') }}/";</script>
		{{-- <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> --}}
	</head>
	<body style="font-family: 'sans-serif'; color: #000">
        <div class="container">
            <div class="card shadow-none border-0" style="margin-top: 12px">
                <div class="card-body border-0">
                    <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="border: 1px solid; padding: 3px; text-align: left; vertical-align: middle; width: 20%;">
                                    <div style="padding-top: 20px; padding-bottom: 10px; margin-right: -1px;">
                                        <img style="max-width: 190px; max-height: 110px;" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/bas_text.png')) }}" alt="Kop Surat">
                                    </div>
                                </td>
                                <td style="border: 1px solid; padding: 5px 10px 5px 5px; text-align: center; vertical-align: middle; width: 80%;">
                                    <div style="padding-top: 20px; padding-bottom: 10px;">
                                        <h1 style="font-size: 18px; margin: 0;">FORM BERITA ACARA INTROGASI</h1>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="text-align: center; padding: 10px 0;">
                        <table style="width: 258px; border-collapse: collapse; margin: 0 auto; border: 0 !important;">
                            <tbody>
                                <tr style="height: 18px.2px; text-align: center;">
                                    <td style="width: 258px; height: 18.2px;" colspan="2">
                                        <span style="text-decoration: underline; font-weight:bold;">BERITA ACARA INTROGASI</span>
                                    </td>
                                </tr>
                                <tr style="height: 18px;">
                                    <td style="width: 101.738px; height: 18px; text-align: left;">No.</td>
                                    <td style="width: 154.262px; height: 18px;">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                        <p style="font-size: 13px; margin-top: 30px; text-align: center;">
                            Pada hari : <u style="margin-right: 10px;">{{ formatHariIndonesia($item->created_at) }}</u> 
                            Tanggal : <u style="margin-right: 10px;">{{ date('d', strtotime($item->created_at)) }}</u> 
                            Bulan : <u style="margin-right: 10px;">{{ formatBulanIndonesia($item->created_at) }}</u> 
                            Tahun : <u style="margin-right: 10px;">{{ date('Y', strtotime($item->created_at)) }}</u> 
                            Jam : <u style="margin-right: 10px;">{{ date('H:i:s', strtotime($item->created_at)) }} WIB</u>  
                        </p>                    </div>
                    <div style="margin-left: 5px;">
                        <p style="font-size: 13px;">Nama<span style="margin-left: 85px;">: {{ $item->nama_pelaku }}</span></p>
                        <p style="font-size: 13px;">No KTP<span style="margin-left: 74px;">: {{ $item->nik_ktp_pelaku }}</span></p>
                        <p style="font-size: 13px;">NIK<span style="margin-left: 98px;">: {{ $item->nik_pelaku }}</span></p>
                        <p style="font-size: 13px;">Pekerjaan<span style="margin-left: 61px;">: {{ $item->pekerjaan_pelaku }}</span></p>
                        <p style="font-size: 13px;">Jabatan/Bagian<span style="margin-left: 30px;">: {{ $item->bagian_pelaku }}</span></p>
                        <p style="font-size: 13px;">No.Handphone<span style="margin-left: 33px;">: {{ $item->no_hp_pelaku }}</span></p>
                        <p style="font-size: 13px;">Perkara/Kasus<span style="margin-left: 34px;">: 
                            @if ($item->jenis_kejadian == 'kecelakaan lalu lintas')
                                        KECELAKAAN LALU LINTAS
                                    @elseif($item->jenis_kejadian == 'penemuan barang')
                                        PENEMUAN BARANG
                                    @elseif($item->jenis_kejadian == 'kecelakaan kerja')
                                        KECELAKAAN KERJA
                                    @elseif($item->jenis_kejadian == 'pencurian')
                                        PENCURIAN
                                    @elseif($item->jenis_kejadian == 'perkelahian')
                                        PERKELAHIAN
                                    @elseif($item->jenis_kejadian == 'tindak kekerasan')
                                        TINDAK KEKERASAN
                                    @elseif($item->jenis_kejadian == 'kebakaran')
                                        KEBAKARAN
                                    @elseif($item->jenis_kejadian == 'demonstrasi')
                                        DEMONSTRASI
                                    @elseif($item->jenis_kejadian == 'tindakan asusila')
                                        TINDAKAN ASUSILA
                                    @elseif($item->jenis_kejadian == 'pengerusakan')
                                        PENGERUSAKAN
                                    @elseif($item->jenis_kejadian == 'tindakan indispliner')
                                        TINDAKAN INDISPLINER
                                    @endif
                        </span></p>
                        <p style="font-size: 13px; margin-top: 20px; text-align: justify;"><span style="margin-left: 20px;">Demikian berita acara ini saya buat dengan sebenar-benarnya dan dapat dipertanggung jawabkan kepada pimpinan PT. Bumi Alam Segar</span></p>
                    </div>
                    <table id="kolomttd" class="table table-bordered" style="width: 100%; margin-top: 20px;">
                        <tbody>
                            <tr>
                                <td style="text-align: center">Dokumentasi Kejadian</td>
                                <td style="text-align: center">Dokumentasi Proses BAI</td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./baioneimage-halosecurity/". $item->bai_oneimage)) }}" style="width: 200px; height: 120px; margin-left: 70px; margin-top: 10px;"><br><br>
                                </td>
                                <td>
                                    <img style="width: 200px; height: 120px; margin-left: 70px;" src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./webcam-halosecurity/". $item->image)) }}" alt="Kop Surat">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-left: 5px;">
                        <p style="font-size: 13px; margin-top: 50px;"><span style="margin-left: 40px;">Yang bersangkutan</span><span style="margin-left: 390px;">Dibuat oleh</span></p>
                        <p style="font-size: 13px; margin-top: 80px;"><span style="margin-left: 38px;">________________</span><span style="margin-left: 370px;">_______________</span></p>
                        <p style="font-size: 13px; margin-left: 531px; margin-top: -9px; font-weight: bold">Danru/Wadanru</p>
                        <p style="font-size: 13px; margin-left: 290px;">Diketahui Oleh</p>
                        <p style="font-size: 13px; margin-top: 80px; font-weight: bold;"><span><u style="margin-left: 70px;">Sumito</u></span><span><u style="margin-left: 195px;">Yusman</u></span><span><u style="margin-left: 168px;">Indra Bayu</u></span></p>
                        <p style="font-size: 13px; margin-top: -5px; margin-bottom: 50px;"><span><i style="margin-left: 55px;">Chief Security</i></span><span><i style="margin-left: 145px;">Koordinator Security</i></span><span><i style="margin-left: 128px;">HRD & GA PT BAS</i></span></p>
                    </div>
                </div>
            </div>
        </div>
	</body>
	<!--end::Body-->
</html>