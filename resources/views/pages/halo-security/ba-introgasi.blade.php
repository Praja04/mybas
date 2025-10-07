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
                margin: 0; /* Remove default body margin */
            }

            .container {
                margin: 20px; /* Adjust the margin as needed */
            }

            .bold-text {
                font-weight: bold;
            }

            .bold-text2 {
                font-weight: bold;
            }
            
            .bold-text3 {
                font-weight: bold;
            }

            .bold-text4 {
                font-weight: bold;
            }

            /* table, td, th {
                border: 1px solid;
                padding: 0 !important;
                padding-left: 5px !important;
                padding-right: 5px !important;
                line-height: 16px;
                font-size: 12px
            } */

            /* table {
                width: 100%;
                border-collapse: collapse;
            } */

            .keterangan_nama {
                display: flex;
                justify-content: space-between;
            }

            .page_break { page-break-before: always; }

            .text-line.center {
                text-align: center;
            }

            .text-line{
                background-color: black;
                height: 1px;
                vertical-align: middle;
                line-height: 1px;
                margin-top: 10px;
            }

            .text-line span {
                padding: 10px;
                background: #fff;
                font-size: 13px;
            }

            .text-line2.center {
                text-align: center;
            }

            .text-line2{
                background-color: black;
                height: 1px;
                vertical-align: middle;
                line-height: 1px;
                margin-top: 30px;
                margin-bottom: 20px;
            }

            .text-line2 span {
                padding: 10px;
                background: #fff;
                font-size: 13px;
            }

            .text-line3.center {
                text-align: center;
            }

            .text-line3{
                background-color: black;
                height: 1px;
                vertical-align: middle;
                line-height: 1px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .text-line3 span {
                padding: 10px;
                background: #fff;
                font-size: 13px;
            }

            .text-line4{
                background-color: black;
                height: 1px;
                /* vertical-align: middle; */
                line-height: 1px;
            }

            .text-line4 span {
                /* padding: 10px;
                background: #fff; */
                font-size: 13px;
            }

            .text-line5 {
                background-color: black;
                height: 1px;
                vertical-align: middle;
                line-height: 1px;
            }

            .text-line5 span {
                padding-right: 6px;
                background: #fff;
                font-size: 13px;
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
                                            <h1 style="font-size: 18px; margin: 0;">FORM BERITA ACARA INTROGASI</h1>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin: 20px 0">
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
                        </div>
                        <p style="text-align: center; margin-top: -10px; font-size: 13px;">( TERLAPOR )</p>
                        <div style="margin-left: 5px;">
                        <p class="text-line center"><span>Pada hari ini {{ formatHariIndonesia($item->created_at) }} tanggal : {{ explode('-', formatTanggalIndonesia2($item->created_at))[0] }} sekira pukul {{ date('H:i', strtotime($item->created_at)) }} wib. oleh Saya :</span></p>
                        <p class="text-line2 center"><span>{{ $item->nama_introgasi }}</span></p>
                        <?php
                            $identitas1 = "Umur: $item->umur_introgasi Tahun, Pekerjaan: $item->pekerjaan_introgasi, Bagian $item->bagian_introgasi Berdasarkan laporan kejadian dari Chief Security PT. BAS Bpk/Ibu $item->nama_pelapor bahwa telah diketahui terjadi <span class='bold-text'>$item->detail_barang_kejadian</span> di $item->tempat_kejadian Atas nama $item->nama_korban Nik $item->nik_korban Bagian $item->bagian_korban yang dilakukan oleh salah satu oknum Karyawan / Vendor dan atas perintah lisan pimpinan untuk melakukan introgasi terhadap seorang Karyawan / Vendor yang belum saya kenal, Ia mengaku bernama:";
                            
                            $panjang_kalimat = strlen($identitas1);
                            
                            $last_line_break_position = strrpos($identitas1, "\n");
                            
                            $length_last_line = $panjang_kalimat - $last_line_break_position - 1;
                            
                            $remaining_spaces = 80 - ($length_last_line % 80) + 1000;
                            
                            // if ($remaining_spaces > 80) {
                            //     $remaining_spaces = 83;
                            // }
                            
                            $spaces = str_repeat(". . ", $remaining_spaces);
                            
                            $identitas1 .= ' <span style="white-space: nowrap; word-spacing: -4px">' . $spaces . '</span>';
                        ?>
                        <p style="text-align: justify; font-size: 13px; margin-bottom: 30px; padding-right: 3px; overflow: hidden;">{!! $identitas1 !!}</p>
                        {{-- <p style="text-align: justify; font-size: 13px;">Umur: {{ $item->umur_introgasi }} Tahun, Pekerjaan: {{ $item->pekerjaan_introgasi }}, Bagian {{ $item->bagian_introgasi }} Berdasarkan laporan kejadian dari Chief Security PT. PAS Bpk/Ibu {{ $item->nama_pelapor }} bahwa telah diketahui terjadi <span style="font-weight: bold;">{{ $item->detail_barang_kejadian }}</span> di {{ $item->tempat_kejadian }} Atas nama {{ $item->nama_korban }} Nik {{ $item->nik_korban }} Bagian {{ $item->bagian_korban }} yang dilakukan oleh salah satu oknum Karyawan / Vendor dan atas perintah lisan pimpinan untuk melakukan introgasi terhadap seorang Karyawan / Vendor yang belum saya kenal, Ia mengaku bernama:</p> --}}
                        <p class="text-line3 center"><span>{{ $item->nama_pelaku }}</span></p>
                        <?php
                            $identitas2 = "Umur: $item->umur_pelaku Tahun, Tempat Tanggal Lahir : $item->ttl_pelaku, Pekerjaan : $item->pekerjaan_pelaku, Nik : $item->nik_pelaku Jabatan : Bagian $item->bagian_pelaku, Alamat $item->alamat_pelaku, Agama : $item->agama_pelaku, Suku : $item->suku_pelaku, Status : $item->status_pelaku, Pendidikan $item->pendidikan_pelaku Lulus, NIK KTP : $item->nik_ktp_pelaku, No HP : $item->no_hp_pelaku.";
                            
                            $panjang_kalimat = strlen($identitas2);
                            
                            $last_line_break_position = strrpos($identitas2, "\n");
                            
                            $length_last_line = $panjang_kalimat - $last_line_break_position - 1;
                            
                            $remaining_spaces = 80 - ($length_last_line % 80) + 1000;
                            
                            // if ($remaining_spaces > 80) {
                            //     $remaining_spaces = 84;
                            // }
                            
                            $spaces = str_repeat(". . ", $remaining_spaces);
                            
                            $identitas2 .= ' <span style="white-space: nowrap; word-spacing: -4px">' . $spaces . '</span>';
                        ?>
                        <p style="text-align: justify; font-size: 13px; margin-top: 30px; padding-right: 3px; overflow: hidden;">{!! $identitas2 !!}</p>
                        {{-- <p style="text-align: justify; font-size: 13px;">Umur: {{ $item->umur_pelaku }} Tahun, Tempat Tanggal Lahir : {{ $item->ttl_pelaku }}, Pekerjaan : {{ $item->pekerjaan_pelaku }}, Nik : {{ $item->nik_pelaku }} Jabatan : Bagian {{ $item->bagian_pelaku }}, Alamat {{ $item->alamat_pelaku }}, Agama : {{ $item->agama_pelaku }}, Suku : {{ $item->suku_pelaku }}, Status : {{ $item->status_pelaku }}, Pendidikan {{ $item->pendidikan_pelaku }} Lulus, NIK KTP : {{ $item->nik_ktp_pelaku }}, No HP : {{ $item->no_hp_pelaku }}.</p> --}}
                        <?php
                            $identitas3 = "Ia <span class='bold-text2'>(Sdr $item->nama_pelaku)</span> dimintai keterangan di $item->tempat_introgasi sebagai terlapor untuk didengar keterangannya dalam perkara diduga adanya <span class='bold-text3'>$item->detail_barang_kejadian</span> di $item->tempat_kejadian milik karyawan atas Nama Sdr $item->nama_korban Bagian $item->bagian_korban PT. Bumi Alam Segar yang diduga oleh karyawan atas nama <span class='bold-text4'>$item->nama_pelaku</span> Dept $item->bagian_pelaku PT. Bumi Alam Segar pada saat $item->keterangan_kejadian";
                            
                            $panjang_kalimat = strlen($identitas3);
                            
                            $last_line_break_position = strrpos($identitas3, "\n");
                            
                            $length_last_line = $panjang_kalimat - $last_line_break_position - 1;
                            
                            $remaining_spaces = 80 + 1000 - ($length_last_line % 80);
                            
                            // if ($remaining_spaces > 80) {
                            //     $remaining_spaces = 84;
                            // }
                            
                            $spaces = str_repeat(". . ", $remaining_spaces);
                            
                            $identitas3 .= ' <span style="white-space: nowrap; word-spacing: -4px">' . $spaces . '</span>';
                        ?>
                        <p style="text-align: justify; font-size: 13px; padding-right: 3px; overflow: hidden">{!! $identitas3 !!}</p>
                        {{-- <p style="text-align: justify; margin-top: 20px; margin-bottom: 20px; font-size: 13px;">Ia <span style="font-weight: bold;">(Sdr {{ $item->nama_pelaku }})</span> dimintai keterangan di {{ $item->tempat_introgasi }} sebagai terlapor untuk didengar keterangannya dalam perkara diduga adanya <span style="font-weight: bold;">{{ $item->detail_barang_kejadian }}</span> di {{ $item->tempat_kejadian }} milik karyawan atas Nama Sdr {{ $item->nama_korban }} Bagian {{ $item->bagian_korban }} PT. Prakarsa Alam Segar yang diduga oleh karyawan atas nama <span style="font-weight: bold;">{{ $item->nama_pelaku }}</span> Dept {{ $item->bagian_pelaku }} PT. Prakarsa Alam Segar pada saat {{ $item->keterangan_kejadian }}</p> --}}
                        <p style="font-size: 13px; margin-top: 20px; margin-bottom: 20px;"><span style="margin-left: 40px;">Pertanyaan :</span><span style="margin-left: 450px;">Jawaban :</span></p>
                        @foreach ($item->baiitems as $result)
                            @php
                                $pertanyaan = "Ditanyakan kepada Sdr. <span class='bold-text2'>$item->nama_pelaku</span> " .  $result->pertanyaan_introgasi;
                                
                                $panjang_kalimat = strlen($pertanyaan);
                                
                                $last_line_break_position = strrpos($pertanyaan, "\n");
                                
                                $length_last_line = $panjang_kalimat - $last_line_break_position - 1;
                                
                                $remaining_spaces = 80 + 1000 - ($length_last_line % 80);
                                
                                // if ($remaining_spaces > 80) {
                                //     $remaining_spaces = 100;
                                // }
                                
                                $spaces = str_repeat(". . ", $remaining_spaces);
                                
                                $pertanyaan .= ' <span style="white-space: nowrap; word-spacing: -4px">' . $spaces . '</span>';
                            @endphp
                            <p style="font-size: 13px;">{{ $loop->iteration }}.</p>
                            <p style="margin-top: -30px; text-align: justify; margin-left: 20px; font-size: 13px; padding-right: 1px; overflow: hidden">{!! $pertanyaan !!}</p>
                            @php
                                $jawaban = $result->jawaban_introgasi;
                                
                                $panjang_kalimat = strlen($jawaban);
                                
                                $last_line_break_position = strrpos($jawaban, "\n");
                                
                                $length_last_line = $panjang_kalimat - $last_line_break_position - 1;
                                
                                $remaining_spaces = 80 + 1000 - ($length_last_line % 80);
                                
                                // if ($remaining_spaces > 80) {
                                //     $remaining_spaces = 100;
                                // }
                                
                                $spaces = str_repeat(". . ", $remaining_spaces);
                                
                                $jawaban .= ' <span style="white-space: nowrap; word-spacing: -4px">' . $spaces . '</span>';
                            @endphp
                            <p style="margin-left: 25px; margin-top: -10px;">-</p>
                            <p style="margin-top: -30px; text-align: justify; margin-left: 50px; font-size: 13px; padding-right: 1px; overflow: hidden">{!! $jawaban !!}</p>
                            @if ($loop->iteration == 2 || $loop->iteration == 8 || $loop->iteration == 14)
                                <div class="page_break"></div>
                            @endif
                        @endforeach
                        <table style="font-size: 12px;">
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <p style="margin-left: 480px;">Yang dimintai keterangan</p>
                                        <p style="margin-left: 480px;"><br><br><br><br></p>
                                        <p style="margin-left: 445px; margin-bottom: 40px; font-weight: bold; text-align: center;"><u>{{ $item->nama_pelaku }}</u></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;" colspan="3">--------------- Demikian berita acara introgasi ini dibuat berdasarkan tugas dan tanggung jawab yang diterima saat ini, kemudian ditutup dam ditanda tangani pada, hari, tanggal, bulan dan tahun tersebut di atas. --------------------------------------</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <p style="margin-top: 20px; margin-left: 500px;">Yang Memeriksa</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;" colspan="3">
                                        <p style="margin-left: 500px;">{{ $item->nama_introgasi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;" colspan="3">
                                        <p style="margin-left: 505px; font-style: italic; margin-top: -10px;">{{ $item->bagian_introgasi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; text-align: center" colspan="3">
                                        <u>Diketahui Oleh</u>
                                    </td>
                                </tr>
                                <tr>
                                    <td><br><br><br><br><br></td>
                                    <td><br><br><br><br><br></td>
                                    <td><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; text-align: center;"><u>Sumito</u></td>
                                    <td style="font-weight: bold; text-align: center;"><u>Yusman</u></td>
                                    <td style="font-weight: bold; text-align: center;"><u>Indra Bayu</u></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center">Chief Security</td>
                                    <td style="text-align: center">Koordinator Security</td>
                                    <td style="text-align: center">HRD & GA PT BAS</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="page_break">
                            <table class="table table-bordered" style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                <tbody>
                                    <tr>
                                        <td colspan="3" style="text-align: center; font-weight: bold; border: 1px solid; padding: 0 !important; padding-left: 5px !important; padding-right: 5px !important; line-height: 16px;">
                                            <p style="margin-top: 10px; margin-bottom: 10px;">DOKUMENTASI</p>
                                        </td>
                                    </tr>
                                    @foreach ($item->dokumentasibais->sortBy('id') as $result)
                                        <tr>
                                            <td style="font-size: 13px; text-align: center; border: 1px solid; padding: 0 !important; padding-left: 5px !important; padding-right: 5px !important; line-height: 16px;">
                                                <p>{{ $loop->iteration }}.</p>
                                            </td>
                                            <td style="font-size: 13px; border: 1px solid; padding: 0 !important; padding-left: 5px !important; padding-right: 5px !important; line-height: 16px;">
                                                <div style="display: flex; flex-direction: column; margin-bottom: 20px; margin-top: 35px; text-align: center; gap: 5px;">
                                                    @foreach (explode(',', $result->foto_introgasi) as $image)
                                                        <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./master_introgasi_gambar/" . $image)) }}" style="width: 200px; height: 150px; padding-right: 50px; padding-left: 50px;"><br><br>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td style="border: 1px solid; padding: 0 !important; padding-left: 5px !important; padding-right: 5px !important; line-height: 16px;">
                                                <p style="font-size: 13px; margin-top: -100px; margin-left: 5px;">Keterangan :<br>
                                                    <span>Dokumentasi {{ $result->keterangan_introgasi }}</span>
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p style="margin-right: 5px; font-weight: bold; line-height: 10px; font-size: 12px; text-align:right;">FRM/HGA/04/000/016-02</p>

                    {{-- Hasil Lama --}}
                    {{-- <p style="font-size: 13px; text-align: justify;">--------------- Demikian berita acara introgasi ini dibuat berdasarkan tugas dan tanggung jawab yang diterima saat ini, kemudian ditutup dam ditanda tangani pada, hari, tanggal, bulan dan tahun tersebut di atas. --------------------------------------</p>
                    <p style="font-size: 13px; margin-top: 20px; margin-left: 525px; text-align: center;">Yang memeriksa</p>
                    <p style="margin-left: 500px;"><br><br></p>
                    <p style="font-size: 13px; margin-left: 515px; font-weight: bold; text-align: center;">{{ $item->nama_introgasi }}</p>
                    <p style="font-size: 13px; margin-left: 515px; margin-top: -10px; font-weight: bold; text-align: center;"><i>{{ $item->bagian_introgasi }}</i></p>
                    <p style="font-size: 13px; margin-left: 290px; font-weight: bold;"><u>Diketahui Oleh</u></p>
                    <p style="font-size: 13px; margin-top: 80px; font-weight: bold;"><span><u style="margin-left: 70px;">Sumito</u></span><span><u style="margin-left: 195px;">Yusman</u></span><span><u style="margin-left: 168px;">Nancy Krisnawati</u></span></p>
                    <p style="font-size: 13px; margin-top: -5px; font-weight: bold;"><span><i style="margin-left: 50px;">Chief Security</i></span><span><i style="margin-left: 135px;">Koordinator Security</i></span><span><i style="margin-left: 105px;">Chief Supervisor HRD GA</i></span></p> --}}
                    
                    {{-- <table class="table table-bordered" style="width: 100%;">
                        <tr>
                            <td style="text-align: center;" colspan="3">--------------- Demikian berita acara introgasi ini dibuat berdasarkan tugas dan tanggung jawab yang diterima saat ini, kemudian ditutup dam ditanda tangani pada, hari, tanggal, bulan dan tahun tersebut di atas. --------------------------------------</td>
                        </tr>
                        <tr>
                            <td style="text-align: center" colspan="3">Yang Memeriksa</td>
                        </tr>
                        <tr>
                            <td colspan="3"><br><br><br><br><br></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; text-align: center;" colspan="3">{{ $item->nama_introgasi }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;" colspan="3">{{ $item->bagian_introgasi }}</td>
                        </tr>
                    </table> --}}

                    {{-- <div class="page_break">
                        <table style="font-size: 12px;">
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <p style="margin-left: 480px;">Yang dimintai keterangan</p>
                                        <p style="margin-left: 480px;"><br><br><br><br></p>
                                        <p style="margin-left: 445px; margin-bottom: 40px; font-weight: bold; text-align: center;"><u>{{ $item->nama_pelaku }}</u></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: center;" colspan="3">--------------- Demikian berita acara introgasi ini dibuat berdasarkan tugas dan tanggung jawab yang diterima saat ini, kemudian ditutup dam ditanda tangani pada, hari, tanggal, bulan dan tahun tersebut di atas. --------------------------------------</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <p style="margin-top: 20px; margin-left: 500px;">Yang Memeriksa</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;" colspan="3">
                                        <p style="margin-left: 490px;">{{ $item->nama_introgasi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;" colspan="3">
                                        <p style="margin-left: 485px; font-style: italic; margin-top: -10px;">{{ $item->bagian_introgasi }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; text-align: center" colspan="3">
                                        <u>Diketahui Oleh</u>
                                    </td>
                                </tr>
                                <tr>
                                    <td><br><br><br><br><br></td>
                                    <td><br><br><br><br><br></td>
                                    <td><br><br><br><br><br></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold; text-align: center;"><u>Sumito</u></td>
                                    <td style="font-weight: bold; text-align: center;"><u>Yusman</u></td>
                                    <td style="font-weight: bold; text-align: center;"><u>Nancy Krisnawati</u></td>
                                </tr>
                                <tr>
                                    <td style="text-align: center">Chief Security</td>
                                    <td style="text-align: center">Koordinator Security</td>
                                    <td style="text-align: center">Chief Supervisor GA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> --}}

                    {{-- <div style="margin-left: 5px; margin-top: 10px; margin-bottom: 10px; border-bottom: 1px solid #000; width: 98.5%"></div> --}}
                </div>
            </div>
        </div>
	</body>
</html>