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
		<script>var hostUrl = "{{ asset('assets') }}/";</script>
		{{-- <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> --}}
	</head>
	<body style="font-family: 'sans-serif'; color: #000">
        <div class="container">
            <img src="data:image/jpg;base64,{{ base64_encode(file_get_contents("./onepage_ttd/". $item->dokumen_ttd)) }}" style="width: 100%; height: 100%;">
        </div>
	</body>
	<!--end::Body-->
</html>