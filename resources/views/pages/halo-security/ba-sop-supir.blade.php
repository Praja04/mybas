<?php

define('DOMPDF_FONT_HEIGHT_RATIO', 0.75);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <base href="{{ url('/') }}">
    <title>Berita Acara S.O.P Supir</title>
    <style>
        @media print {
            .break-the-page {
                page-break-after: always;
            }
        }

        body {
            font-family: sans-serif !important;
        }

        table,
        td,
        th {
            /* border: 1px solid; */
            padding: 0 !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
            line-height: 16px;
            font-size: 12px
        }

        #kolomttd td {
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
</head>

<body style="font-family: 'sans-serif'; color: #000">
    <div class="container">
        <div class="card shadow-none border-0" style="margin-top: 12px">
            <div class="card-body border-0">
                <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td
                                style="border: 1px solid; padding: 3px; text-align: left; vertical-align: middle; width: 20%;">
                                <div style="padding-top: 20px; padding-bottom: 20px; margin-right: -1px;">
                                    <img style="max-width: 190px; max-height: 110px;"
                                        src="data:image/png;base64,{{ base64_encode(file_get_contents('./assets/media/logos/logo_bas_compress.png')) }}"
                                        alt="Kop Surat">
                                </div>
                            </td>
                            <td
                                style="border: 1px solid; padding: 5px 10px 5px 5px; text-align: center; vertical-align: middle; width: 80%;">
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
                                    <span style="text-decoration: underline; font-weight:bold;">BERITA ACARA SOP KARTU
                                        HILANG</span>
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
                    <p style="font-size: 13px; margin-top: -10px; margin-top: 5px;">Berdasarkan kehilangan kartu
                        <span>{{ $item->jenis_kartu }}</span>.......................................
                    </p>
                    <p style="font-size: 13px; margin-top: 20px;">Yang mengaku :</p>
                    <p style="font-size: 13px; margin-top: -7px;">Nama<span style="margin-left: 85px;">:
                            {{ $item->nama }}</span></p>
                    <p style="font-size: 13px;">Ekspedisi<span style="margin-left: 64px;">:
                            {{ $item->ekspedisi }}</span></p>
                    <p style="font-size: 13px;">No. KTP<span style="margin-left: 70px;">: {{ $item->no_ktp }}</span>
                    </p>
                    <p style="font-size: 13px;">No. Polisi<span style="margin-left: 64px;">:
                            {{ $item->no_polisi }}</span></p>
                    <p style="font-size: 13px;">No. Handphone<span style="margin-left: 28px;">:
                            {{ $item->no_handphone }}</span></p>
                    <p style="font-size: 13px;">Alamat
                        <span style="margin-left: 75px;">:
                            <table
                                style="margin-left: 118px; margin-top: -15px; border: hidden; border-collapse: collapse; width: 270px;">
                                <tr>
                                    <td>{{ $item->alamat }}</td>
                                </tr>
                            </table>
                        </span>
                    </p>

                    {{-- ttd --}}
                    <p style="font-size: 13px; margin-top: 20px; text-align: justify;">Menerangkan bahwa pada hari ini
                        telah menghilangkan kartu {{ $item->jenis_kartu }} No: {{ $item->no_kartu }} Supir tersebut
                        diarahkan ke Pos 1 untuk bertangung jawab dengan kehilangan kartu tersebut sebesar Rp.
                        {{ $item->harga_kartu }}.</p>
                    <p style="font-size: 13px: margin-top: 7px; text-align: justify;"><span
                            style="margin-left: 15px;">Demikian</span> berita acara ini saya buat dengan
                        sebenar-benarnya dan dapat dipertanggung jawabkan kepada pimpinan PT. Bumi Alam Segar.</p>

                    {{--  --}}
                    <table
                        style="width:100%; text-align:center; margin-top:40px; font-size:13px; page-break-inside: avoid;">
                        <tr>
                            <td>Yang bersangkutan</td>
                            <td>Dibuat oleh</td>
                        </tr>

                        <!-- Jarak untuk area tanda tangan -->
                        <tr>
                            <td style="height:70px;"></td>
                            <td></td>
                        </tr>

                        <!-- Garis tanda tangan -->
                        <tr>
                            <td>( ___________________ )</td>
                            <td>( ___________________ )</td>
                        </tr>

                        <!-- Nama -->
                        <tr>
                            <td></td>
                            <td><b>Danru</b></td>
                        </tr>
                    </table>

                    <table
                        style="width:100%; text-align:center; font-size:13px; table-layout:fixed; margin-top:40px; page-break-inside: avoid;">
                        <tr>
                            <td colspan="3" style="font-weight:bold;">Diketahui Oleh</td>
                        </tr>

                        <!-- Jarak untuk area tanda tangan -->
                        <tr>
                            <td style="height:70px;"></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <!-- Garis tanda tangan -->
                        <tr>
                            <td>( ___________________ )</td>
                            <td><u>Yusman</u></td>
                            <td><u>Nancy Krisnawati</u></td>
                        </tr>

                        <!-- Jabatan -->
                        <tr>
                            <td><i>Chief Security</i></td>
                            <td><i>Koordinator Security</i></td>
                            <td><i>HR & GA PT BAS</i></td>
                        </tr>
                    </table>
                </div>
            </div>
            <p style="margin-right: 5px; font-weight: bold; line-height: 10px; font-size: 12px; text-align:right;">
                FRM/HGA/04/000/016-02
        </div>
    </div>
</body>
<!--end::Body-->

</html>
