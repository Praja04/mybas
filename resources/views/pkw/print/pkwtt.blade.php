<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PRINT PKWTT</title>
    <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/logo-pas.jpg" />
    <style>
        html, body {
            /* Reset the document's margin values */
            margin: 0;
            /* Reset the document's padding values */
            padding: 0;
            /* Use the platform's native font as the default */
            font-family: "Times New Roman";
            /* Define a reasonable base font size */
            font-size: 12pt;

            /* Styles for better appearance on screens only -- are reset to defaults in print styles later */

            /* Use a non-white background color to make the content areas stick out from the full page box */
            background-color: #eee;
        }
        /* Styles that are shared by all elements */
        * {
            /* Include the content box as well as padding and border for precise definitions */
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            /* Styles for better appearance on screens only -- are reset to defaults in print styles later */

            /* Divide single pages with some space and center all pages horizontally */
            margin: 1cm auto;
            /* Define a white paper background that sticks out from the darker overall background */
            background: #fff;
            /* Show a drop shadow beneath each page */
            box-shadow: 0 4px 5px rgba(75, 75, 75, 0.2);
            /* Override outline from user agent stylesheets */
            outline: 0;
        }
        /* Defines a class for manual page breaks via inserted .page-break element */
        div.page-break {
            page-break-after: always;
        }
        /* Simulates the behavior of manual page breaks from `print` mode in `screen` mode */
        @media screen {
            /* Renders the border and shadow at the bottom of the upper virtual page */
            div.page-break::before {
                content: "";
                display: block;
                /* Give a sufficient height to this element so that its drop shadow is properly rendered */
                height: 0.8cm;
                /* Offset the negative extra margin at the left of the non-pseudo element */
                margin-left: 0.5cm;
                /* Offset the negative extra margin at the right of the non-pseudo element */
                margin-right: 0.5cm;
                /* Make the bottom area appear as a part of the page margins of the upper virtual page */
                background-color: #fff;
                /* Show a drop shadow beneath the upper virtual page */
                box-shadow: 0 6px 5px rgba(75, 75, 75, 0.2);
            }
            /* Renders the empty space as a divider between the two virtual pages that are actually two parts of the same page */
            div.page-break {
                display: block;
                /* Assign the intended height plus the height of the pseudo element */
                height: 1.8cm;
                /* Apply a negative margin at the left to offset the page margins of the page plus some negative extra margin to paint over the border and shadow of the page */
                margin-left: -2.5cm;
                /* Apply a negative margin at the right to offset the page margins of the page plus some negative extra margin to paint over the border and shadow of the page */
                margin-right: -2.5cm;
                /* Create the bottom page margin on the upper virtual page (minus the height of the pseudo element) */
                margin-top: 1.2cm;
                /* Create the top page margin on the lower virtual page */
                margin-bottom: 2cm;
                /* Let this page appear as empty space between the virtual pages */
                background: #eee;
            }
        }
        /* For top-level headings only */
        h1 {
            /* Force page breaks after */
            page-break-before: always;
        }
        /* For all headings */
        h1, h2, h3, h4, h5, h6 {
            /* Avoid page breaks immediately */
            page-break-after: avoid;
        }
        /* For all paragraph tags */
        p {
            /* Reset the margin so that the text starts and ends at the expected marks */
            margin: 0;
        }
        /* For adjacent paragraph tags */
        p + p {
            /* Restore the spacing between the paragraphs */
            margin-top: 0.5cm;
        }
        /* For links in the document */
        a {
            /* Prevent colorization or decoration */
            text-decoration: none;
            color: black;
        }
        /* For tables in the document */
        table {
            /* Avoid page breaks inside */
            page-break-inside: avoid;
        }
        /* Use CSS Paged Media to switch from continuous documents to sheet-like documents with separate pages */
        @page {
            /* You can only change the size, margins, orphans, widows and page breaks here */

            /* Require that at least this many lines of a paragraph must be left at the bottom of a page */
            orphans: 4;
            /* Require that at least this many lines of a paragraph must be left at the top of a new page */
            widows: 2;
        }
        /* When the document is actually printed */
        @media print {
            html, body {
                /* Reset the document's background color */
                background-color: #fff;
            }
            .page {
                /* Reset all page styles that have been for better screen appearance only */
                /* Break cascading by using the !important rule */
                /* These resets are absolute must-haves for the print styles and the specificity may be higher elsewhere */
                width: initial !important;
                min-height: initial !important;
                margin: 0 !important;
                padding: 0 !important;
                border: initial !important;
                border-radius: initial !important;
                background: initial !important;
                box-shadow: initial !important;

                /* Force page breaks after each .page element of the document */
                page-break-after: always;
            }
        }
        .page {
            /* Styles for better appearance on screens only -- are reset to defaults in print styles later */

            /* Reflect the paper width in the screen rendering (must match size from @page rule) */
            width: 21cm;
            /* Reflect the paper height in the screen rendering (must match size from @page rule) */
            min-height: 29.7cm;

            /* Reflect the actual page margin/padding on paper in the screen rendering (must match margin from @page rule) */
            padding-left: 2cm;
            padding-top: 0.5cm;
            padding-right: 2cm;
            padding-bottom: 2cm;
            letter-spacing: .1px;
            position: relative;
        }
        /* Use CSS Paged Media to switch from continuous documents to sheet-like documents with separate pages */
        @page {
            /* You can only change the size, margins, orphans, widows and page breaks here */

            /* Paper size and page orientation */
            size: A4 portrait;

            /* Margin per single side of the page */
            margin-left: 2cm;
            margin-top: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        .page-header {
            margin: auto
        }

        .page-table {
            margin-left: 50px;
        }
        .page-table tr td {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        .page-title {
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .page-title h2 {
            font-size: 16px;
            text-align: center;
            text-decoration: underline;
            /* border: 1px solid #000; */
            margin-top: 0;
        }
        .page-title h3 {
            font-size: 14px;
            text-align: center;
            font-weight: normal
        }
        .page-text, .page-table {
            font-size: 14px;
        }
        .page-list {
            font-size: 14px !important
        }
        .page-table {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .page-detail {
            margin: 0;
            position: absolute;
            bottom: 2cm;
            right: 2cm;
            font-size: 12px;
            display: none;
        }

        .mb-1 {
            margin-bottom: 1em
        }
        .mt-2 {
            margin-top: 2em
        }

    </style>
</head>
<body>
    @foreach ($employees as $employee)
    <div class="page" contenteditable="true">
        <div class="page-header">
            <img src="{{ asset('assets/media/logos/logo-pkw.png') }}" alt="Page Header">
        </div>

        <div class="page-title">
            <h2>PERJANJIAN KERJA WAKTU TIDAK TERTENTU</h2>
            <h3>No. {{ $employee->pkw->no_perjanjian }} /PKWTT/HRD/PAS/{{ formatBulanRomawi($employee->tanggal_masuk) }}/@php echo date('Y'); @endphp </h3>
        </div>

        <div class="page-body">
            <p class="page-text">Pada hari ini, {{ formatHariIndonesia($employee->tanggal_masuk) }} , {{ formatTanggalIndonesia2($employee->tanggal_masuk) }} , telah ditandatangani Perjanjian Kerja Waktu Tidak Tertentu (selanjutnya disebut dengan Perjanjian Kerja) oleh dan antara : </p>
            <p>
                <table class="page-table">
                    <tr>
                        <td>1.</td>
                        <td width="150">Nama</td>
                        <td>:</td>
                        <td><strong>PAULUS WIKATMO</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td><strong>HRD DEPARTMENT HEAD</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Perusahaan</td>
                        <td>:</td>
                        <td><strong>PT. PRAKARSA ALAM SEGAR</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Alamat</td>
                        <td>:</td>
                        <td><strong>JL. RAYA KALIABANG BUNGUR, PONDOK UNGU,</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>DESA PEJUANG, BEKASI, 17131</strong></td>
                    </tr>
                </table>
            </p>
            <p class="page-text">
                Dalam Perjanjian Kerja ini bertindak untuk dan atas nama PT. Prakarsa Alam Segar  yang selanjutnya dalam hal ini disebut <strong>PIHAK PERTAMA</strong>.
            </p>
            <p>
                <table class="page-table">
                    <tr>
                        <td>2.</td>
                        <td width="150">Nama</td>
                        <td>:</td>
                        <td><strong>{{ $employee->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>NIK</td>
                        <td>:</td>
                        <td><strong>{{ $employee->nik }}</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Tempat, Tgl lahir</td>
                        <td>:</td>
                        <td>{{ $employee->tempat_lahir }}, {{ formatTanggalIndonesia($employee->tanggal_lahir) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>No. KTP</td>
                        <td>:</td>
                        <td>{{ $employee->nik_ktp }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jenis kelamin</td>
                        <td>:</td>
                        <td>{{ $employee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $employee->alamat_sekarang.', Kel. '.ucfirst(strtolower($employee->sekarang_desa->name)).', Kec. '.ucfirst(strtolower($employee->sekarang_kecamatan->name)).', Kota. '.ucfirst(strtolower($employee->sekarang_kota->name)).', Provinsi '.ucfirst(strtolower($employee->sekarang_provinsi->name)) }}</td>
                    </tr>
                </table>
            </p>
            <p class="page-text">
                Dalam Perjanjian Kerja ini bertindak untuk dan atas nama diri sendiri, yang selanjutnya dalam hal ini disebut <strong>PIHAK KEDUA</strong>. 
            </p>
            <p class="page-text">
                PIHAK PERTAMA dan PIHAK KEDUA (selanjutnya disebut dengan Para Pihak) sepakat untuk mengadakan Perjanjian Kerjayang isi dan syarat-syaratnya dituangkan sebagai berikut: 
            </p>
        </div>

        <span class="page-detail"> Page 1 of 5</span>
    </div>

    <div class="page" contenteditable="true">
        <div class="page-header">
            <img src="{{ asset('assets/media/logos/logo-pkw.png') }}" alt="Page Header">
        </div>

        <div class="page-title">
            <h2>PASAL 1</h2>
            <h3><strong>MASA PERCOBAAN</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">Perjanjian Kerja ini mulai berlaku pada saat PIHAK KEDUA melaksanakan tugas-tugas yang di berikan oleh PIHAK PERTAMA  pada tanggal 02 Januari 2021.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA bersedia menjalani masa percobaan selama masa 3 (tiga) bulan, terhitung  ditandatanganinya  perjanjian kerja ini.</li>
                    <li class="mb-1 page-list">Selama menjalani masa percobaan PIHAK KEDUA akan dievaluasi oleh PIHAK PERTAMA.</li>
                    <li class="mb-1 page-list">Selama menjalani masa percobaan Para Pihak dapat memutuskan hubungan kerja tanpa syarat.</li>
                    <li class="mb-1 page-list">Apabila PIHAK KEDUA dinyatakan tidak lulus oleh PIHAK PERTAMA, maka PIHAK PERTAMA tidak berkewajiban untuk membayar/memberikan kompensasi apapun kecuali upah.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA akan diangkat sebagai karyawan tetap oleh PIHAK PERTAMA setelah dinyatakan lulus masa percobaan yang dituangkan dalam Surat Keputusan.</li>
                </ol>
            </p>
        </div>

        <div class="page-title">
            <h2>PASAL 2</h2>
            <h3><strong>TUGAS DAN PENEMPATAN</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">PIHAK PERTAMA mempekerjakan PIHAK KEDUA sebagai Pekerja dengan tugas dan kewajiban seperti yang dijelaskan oleh atasannya.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA bersedia melaksanakan tugas pekerjaan yang diberikan oleh PIHAK PERTAMA dengan sebaik-baiknya.</li>
                    <li class="mb-1 page-list">Golongan,  jabatan dan penempatan PIHAK KEDUA adalah sebagai berikut :</li>
                    <table class="mb-1">
                        <tr>
                            <td width="150" style="padding-bottom: 10px">a) Golongan</td>
                            <td style="padding-bottom: 10px">:</td>
                            <td style="padding-bottom: 10px">{{ angkaRomawi($employee->level) }} ({{ ucwords(angkaTerbilang($employee->level)) }})</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px">b) Jabatan</td>
                            <td style="padding-bottom: 10px">:</td>
                            <td style="padding-bottom: 10px">{{ $employee->jabatan->nama_jabatan }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px">c) Dept/Section</td>
                            <td style="padding-bottom: 10px">:</td>
                            <td style="padding-bottom: 10px">{{ $employee->divisi->nama_divisi }}/{{ $employee->bagian->nama_bagian }}</td>
                        </tr>
                    </table>
                    <li class="mb-1 page-list">PIHAK KEDUA bersedia ditugaskan untuk bekerja secara Non-Shift ataupun Shift dan ditempatkan di bagian yang ditentukan oleh PIHAK PERTAMA.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA bersedia melaksanakan pekerjaan lembur atas perintah PIHAK PERTAMA.</li>
                </ol>
            </p>
        </div>

        <span class="page-detail"> Page 2 of 5</span>
    </div>

    <div class="page" contenteditable="true">
        <div class="page-header">
            <img src="{{ asset('assets/media/logos/logo-pkw.png') }}" alt="Page Header">
        </div>

        <div class="page-title">
            <h2>PASAL 3</h2>
            <h3><strong>HARI KERJA DAN WAKTU KERJA</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">Hari kerja di perusahaan ditentukan 6 (enam) hari kerja dalam seminggu.</li>
                    <li class="mb-1 page-list">Sedangkan waktunya adalah 7 (tujuh) jam sehari yang pengaturannya berdasarkan Peraturan Perusahaan yang berlaku. </li>
                </ol>
            </p>
        </div>

        <div class="page-title">
            <h2>PASAL 4</h2>
            <h3><strong>PENGUPAHAN</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">Selama terikat hubungan kerja, PIHAK KEDUA memperoleh upah setiap bulan sebesar @if($employee->level <= 1) <strong>4.998.845;  ( {{ ucwords(angkaTerbilang(4998845)) }} Rupiah )</strong> @else @endif termasuk biaya transportasi dan makan diberikan secara natura.</li>
                    <li class="mb-1 page-list">Pembayaran Upah kepada PIHAK KEDUA dibayarkan setiap tanggal terakhir pada bulan berikutnya dengan cara ditransfer melalui Bank yang ditunjuk oleh PT Prakarsa Alam Segar.</li>
                    <li class="mb-1 page-list">Apabila tidak masuk bekerja selain dari sakit dengan surat keterangan dari dokter atau cuti, perusahaan berhak melakukan pemotongan upah kepada PIHAK KEDUA yang besarnya :n/30x upah pokok ( n=jumlah hari tidak masuk kerja, kecuali sakit dengan rekomendasi dokter atau cuti).</li>
                    <li class="mb-1 page-list">Apabila tanggal pembayaran upah sebagaimana disebutkan di dalam ayat di atas jatuh pada hari libur, maka pembayaran upah diberikan pada hari kerja terakhir sebelum tanggal tersebut.</li>
                </ol>
            </p>
        </div>

        <div class="page-title">
            <h2>PASAL 5</h2>
            <h3><strong>PROGRAM BPJS</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">PIHAK PERTAMA mengikutsertakan PIHAK KEDUA dalam Program Badan Penyelenggara Jaminan Sosial dengan  rincian Jaminan Kecelakaan Kerja (JKK), Jaminan kematian (JK), Jaminan Hari Tua (JHT), Jaminan Pensiun  (JP), dan  Jaminan Kesehatan.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA menanggung 2% dari upah tetap untuk premi Jaminan Hari Tua PT. BPJS dan 1% dari upah tetap untuk premi jaminan Pensiun yang dipotong dari upah setiap bulan. </li>
                    <li class="mb-1 page-list">PIHAK KEDUA menanggung 1% dari upah tetap untuk premi  BPJS Kesehatan.</li>
                </ol>
            </p>
        </div>

        <span class="page-detail"> Page 3 of 5</span>
    </div>

    <div class="page" contenteditable="true">
        <div class="page-header">
            <img src="{{ asset('assets/media/logos/logo-pkw.png') }}" alt="Page Header">
        </div>

        <div class="page-title">
            <h2>PASAL 6</h2>
            <h3><strong>TATA TERTIB PERUSAHAAN</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">PIHAK KEDUA wajib melengkapi data administrasi dan apabila ada perubahan data wajib memberitahukan kepada bagian personalia paling lambat 2 minggu setelah adanya perubahan data.</li>
                    <li class="mb-1 page-list">PIHAK PERTAMA berhak menempatkan/ memutasikan dan memberikan pekerjaan kepada PIHAK KEDUA yang disesuaikan  dengan kebutuhan Perusahaan.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA wajib untuk mematuhi tata tertib, peraturan perusahaan, prosedur/sistem, instruksi kerja dan Undang-Undang Ketenagakerjaan serta termasuk peraturan-peraturan tambahan lainnya.</li>
                    <li class="mb-1 page-list">Apabila PIHAK KEDUA melakukan pelanggaran yang ada di PKB  maka perusahaan dapat memberikan sanksi sesuai PKB dan atau memutuskan hubungan kerja ( dinyatakan Tidak Lulus ).</li>
                    <li class="mb-1 page-list">PIHAK KEDUA berkewajiban untuk menjaga hal-hal yang menjadi rahasia perusahaan baik selama bekerja maupun setelah mengundurkan diri.</li>
                    <li class="mb-1 page-list">PIHAK KEDUA selama terikat hubungan kerja tidak boleh terikat atau melakukan kerja dengan pihak lain.</li>
                    <li class="mb-1 page-list">Apabila PIHAK KEDUA berhenti bekerja dan tidak mengembalikan inventaris perusahaan yang digunakannya, maka perusahaan dapat melakukan pemotongan upah secara sepihak dan sesuai dengan harga baru dari barang tersebut.</li>
                </ol>
            </p>
        </div>

        <div class="page-title">
            <h2>PASAL 7</h2>
            <h3><strong>BERAKHIRNYA HUBUNGAN KERJA KARENA MENGUNDURKAN DIRI</strong></h3>
        </div>

        <p class="page-text" style="margin-left: 35px">Apabila PIHAK KEDUA mengundurkan diri, maka harus mengajukan Surat Pengunduran diri minimal 30 hari sebelum efektifnya masa pengunduran diri.</p>

        <div class="page-title">
            <h2>PASAL 8</h2>
            <h3><strong>PENUTUP</strong></h3>
        </div>

        <div class="page-body">
            <p class="page-text">
                <ol>
                    <li class="mb-1 page-list">Perjanjan Kerja Waktu Tidak Tertentu ini dibuat rangkap 2 (dua) dengan 3 (tiga) halaman, bermaterai cukup dan keduanya mempunyai kekuatan hukum yang sama.</li>
                    <li class="mb-1 page-list">Perjanjan Kerja Waktu Tidak Tertentu ini dibuat dan ditandatangani oleh Para Pihak dalam keadaan sehat jasmani dan rohani tanpa adanya paksaan dari pihak lain untuk dilaksanakan dengan penuh tanggung jawab.</li>
                    <li class="mb-1 page-list">Perjanjan Kerja ini mulai berlaku sejak ditandatangani oleh Para Pihak.</li>
                </ol>
            </p>
        </div>

        <span class="page-detail"> Page 4 of 5</span>
    </div>

    <div class="page" contenteditable="true">
        <div class="page-header">
            <img src="{{ asset('assets/media/logos/logo-pkw.png') }}" alt="Page Header">
        </div>

        <div class="page-body">
            <p class="page-text">
                <table style="margin-left: 10px; margin-top: 0" class="page-table">
                    <tr><td></td></tr>
                    <tr>
                        <td>Bekasi, {{ formatTanggalIndonesia2(date('Y-m-d')) }}</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 150px">PIHAK PERTAMA</td>
                        <td width="250"></td>
                        <td style="padding-bottom: 150px">PIHAK KEDUA</td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td><strong>Paulus Wikatmo</strong></td>
                        <td></td>
                        <td><strong>{{ $employee->nama }}</strong></td>
                    </tr>
                </table>
            </p>
            <p class="page-text" style="padding-left: 20px; margin-top: 150px"><span>cc: Kantor Dinas Tenaga Kerja</span> <br/> <span style="padding-left: 20px;">Bekasi.</span></p>
        </div>

        <span class="page-detail"> Page 5 of 5</span>
    </div>

    @endforeach
    <script type="text/javascript">
    // window.print();
    </script>
</body>
</html>