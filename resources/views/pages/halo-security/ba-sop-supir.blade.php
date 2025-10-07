<?php

define("DOMPDF_FONT_HEIGHT_RATIO", 0.75);

?>

<!DOCTYPE html>
<html lang="en">
	<head>
        <base href="{{ url('/') }}">
		<title>Berita Acara S.O.P Supir</title>
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
                                    <div style="padding-top: 20px; padding-bottom: 20px; margin-right: -1px;">
                                        <img style="max-width: 190px; max-height: 110px;" src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/Logo_hd_bas.png')) }}" alt="Kop Surat">
                                    </div>
                                </td>
                                <td style="border: 1px solid; padding: 5px 10px 5px 5px; text-align: center; vertical-align: middle; width: 80%;">
                                    <div style="padding-top: 20px; padding-bottom: 20px;">
                                        <h1 style="font-size: 18px; margin: 0;">FORM BERITA ACARA KARTU HILANG</h1>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="border: 1px solid; padding: 10px; margin-top:10px;">
                    <div style="text-align: center; padding: 5px 0;">
                        <table style="width: 258px; border-collapse: collapse; margin: 0 auto; border: 0 !important;">
                            <tbody>
                                <tr style="height: 22.2px; text-align: center;">
                                    <td style="width: 258px; height: 22.2px;" colspan="2">
                                        <span style="text-decoration: underline; font-weight:bold;">BERITA ACARA SOP KARTU HILANG</span>
                                    </td>
                                </tr>
                                <tr style="height: 22px;">
                                    <td style="width: 101.738px; height: 16px; text-align: center;">No.</td>
                                    <td style="width: 154.262px; height: 16px;">&nbsp;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-left: 5px;">
                        <p style="font-size: 13px; margin-top: 30px; text-align: justify;">
                            Pada hari : <u style="margin-right: 10px;">{{ formatHariIndonesia($item->created_at) }}</u> 
                            Tanggal : <u style="margin-right: 10px;">{{ date('d', strtotime($item->created_at)) }}</u> 
                            Bulan : <u style="margin-right: 10px;">{{ formatBulanIndonesia($item->created_at) }}</u> 
                            Tahun : <u style="margin-right: 10px;">{{ date('Y', strtotime($item->created_at)) }}</u> 
                            Jam : <u style="margin-right: 10px;">{{ date('H:i:s', strtotime($item->created_at)) }} WIB</u> 
                            Shift : <u style="margin-right: 10px;">{{ $item->shift }}</u> 
                            Nama : <u style="margin-right: 10px;">{{ $item->nama_pembuat }}</u> 
                            Jabatan : <u>{{ $item->jabatan_pembuat }}</u>
                        </p>
                        <p style="font-size: 13px; margin-top: -10px; margin-top: 5px;">Berdasarkan kehilangan kartu <span>{{ $item->jenis_kartu }}</span>.......................................</p>
                        <p style="font-size: 13px; margin-top: 20px;">Yang mengaku :</p>
                        <p style="font-size: 13px; margin-top: -7px;">Nama<span style="margin-left: 85px;">: {{ $item->nama }}</span></p>
                        <p style="font-size: 13px;">Ekspedisi<span style="margin-left: 64px;">: {{ $item->ekspedisi }}</span></p>
                        <p style="font-size: 13px;">No. KTP<span style="margin-left: 70px;">: {{ $item->no_ktp }}</span></p>
                        <p style="font-size: 13px;">No. Polisi<span style="margin-left: 64px;">: {{ $item->no_polisi }}</span></p>
                        <p style="font-size: 13px;">No. Handphone<span style="margin-left: 28px;">: {{ $item->no_handphone }}</span></p>
                        <p style="font-size: 13px;">Alamat
                            <span style="margin-left: 75px;">: 
                                <table style="margin-left: 118px; margin-top: -15px; border: hidden; border-collapse: collapse; width: 270px;">
                                    <tr>
                                        <td>{{ $item->alamat }}</td>
                                    </tr>
                                </table>
                            </span>
                        </p>

                        {{-- ttd --}}
                        <p style="font-size: 13px; margin-top: 20px; text-align: justify;">Menerangkan bahwa pada hari ini telah menghilangkan kartu {{ $item->jenis_kartu }} No: {{ $item->no_kartu }} Supir tersebut diarahkan ke Pos 1 untuk bertangung jawab dengan kehilangan kartu tersebut sebesar Rp. {{ $item->harga_kartu }}.</p>
                        <p style="font-size: 13px: margin-top: 7px; text-align: justify;"><span style="margin-left: 15px;">Demikian</span> berita acara ini saya buat dengan sebenar-benarnya dan dapat dipertanggung jawabkan kepada pimpinan PT. Bumi Alam Segar.</p>
                        <p style="font-size: 13px; margin-top: 50px;"><span style="margin-left: 40px;">Yang bersangkutan</span><span style="margin-left: 390px;">Dibuat oleh</span></p>
                        <p style="font-size: 13px; margin-top: 80px;"><span style="margin-left: 38px;">________________</span><span style="margin-left: 370px;">_______________</span></p>
                        <p style="font-size: 13px; margin-left: 531px; margin-top: -9px; font-weight: bold">Danru/Wadanru</p>
                        <p style="font-size: 13px; margin-left: 290px;">Diketahui Oleh</p>
                        <p style="font-size: 13px; margin-top: 80px; font-weight: bold;"><span><u style="margin-left: 70px;">Sumito</u></span><span><u style="margin-left: 195px;">Yusman</u></span><span><u style="margin-left: 168px;">Indra Bayu</u></span></p>
                        <p style="font-size: 13px; margin-top: -5px; margin-bottom: 50px;"><span><i style="margin-left: 55px;">Chief Security</i></span><span><i style="margin-left: 145px;">Koordinator Security</i></span><span><i style="margin-left: 128px;">HRD & GA PT BAS</i></span></p>
                    </div>
                </div>
                <p style="margin-right: 5px; font-weight: bold; line-height: 10px; font-size: 12px; text-align:right;">FRM/HGA/04/000/016-02
            </div>
        </div>
	</body>
	<!--end::Body-->
</html>