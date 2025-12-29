<!DOCTYPE html>
<html lang="en">

    <head>
        <base href="{{ url('/') }}">
        <title></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <meta name="description" content="PT Bumi Alam Segar Applications Base" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/bas_logo.png" />

        <!-- Layout config Js -->
        {{-- <script src="{{ asset('assets/velzon/js/layout.js') }}"></script>
        <link href="{{ asset('assets/velzon/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/velzon/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/velzon/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/velzon/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

        <!--datatable css-->
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/responsive.bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/buttons.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/select.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/datatables/css/fixedColumns.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}"> --}}
        <style>
            .pas-background-color {
                background-color: #a80000 !important;
            }

            .pas-color {
                color: #a80000 !important;
            }

            table p {
                margin-bottom: 0 !important
            }

            .modal-backdrop {
                opacity: 0.99 !important;
            }

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 10px;
            }

            h1 {
                font-size: 18px;
                margin-bottom: 5px;
            }

            h3 {
                font-size: 14px;
                margin-bottom: 5px;
            }

            h6 {
                font-size: 11px;
                margin-bottom: 5px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th,
            table td {
                border: 1px solid #000;
                padding: 4px;
                vertical-align: top;
            }

            .text-center {
                text-align: center;
            }

            .pas-background-color {
                background-color: #a80000;
                color: #fff;
            }

            img {
                max-width: 250px;
                height: auto;
            }

            .page-break {
                page-break-after: always;
            }
        </style>
    </head>

    <body>

        <div style="text-align:center; margin-bottom:15px;">
            <h1 style="margin:0;">PENILAIAN 5R</h1>
            <h3 style="margin:0;">PT BUMI ALAM SEGAR</h3>
            <p style="margin:5px 0; font-size:11px;">
                {{ $info['tahun'] }} - {{ $info['periode'] }} -
                {{ $info['department'] }} - {{ $info['group'] }}
            </p>
        </div>

        @php
            $colors = ['#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51', '#1d3557'];
            $textColors = ['#fff', '#fff', '#000', '#000', '#000', '#ffffff'];
            $jenisList = ['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN', 'DIGITALISASI'];
        @endphp

        <table style="width:100%; border-collapse:collapse; margin-bottom:25px;">
            <tbody>

                {{-- HEADER --}}
                <tr style="background:#a80000; color:#fff;">
                    <th style="border:1px solid #000; width:60%; padding:5px;">PERTANYAAN</th>
                    <th style="border:1px solid #000; width:40%; padding:5px;">NILAI & FOTO</th>
                </tr>

                @foreach ($jenisList as $idx => $jenis)
                    {{-- GROUP JENIS --}}
                    <tr>
                        <td colspan="2"
                            style="
                            border:1px solid #000;
                            background:{{ $colors[$idx] }};
                            color:{{ $textColors[$idx] }};
                            font-weight:bold;
                            text-align:center;
                            padding:6px;
                        ">
                            {{ $jenis }}
                        </td>
                    </tr>

                    @if (!isset($data[$jenis]) || $data[$jenis]->isEmpty())
                        <tr>
                            <td colspan="2" style="border:1px solid #000; padding:6px; text-align:center;">
                                <em>Tidak ada data</em>
                            </td>
                        </tr>
                    @else
                        @foreach ($data[$jenis] as $row)
                            <tr>
                                {{-- PERTANYAAN --}}
                                <td style="border:1px solid #000; padding:6px;">
                                    <strong>ITEM PERIKSA</strong><br>
                                    {!! str_replace('||--||', '&', $row->pertanyaan->item_periksa) !!}

                                    <br><br>

                                    <strong>KETERANGAN</strong><br>
                                    {!! str_replace('||--||', '&', $row->pertanyaan->keterangan) !!}
                                </td>

                                {{-- NILAI + FOTO --}}
                                <td style="border:1px solid #000; padding:6px;">
                                    <strong>Nilai:</strong>
                                    {{ $row->nilai ?? '-' }}

                                    <br><br>

                                    <strong>Foto:</strong><br>

                                    @if ($row->temuan && $row->temuan->count())
                                        @foreach ($row->temuan as $temuan)
                                            @php
                                                $areaName = $temuan->area->nama_area ?? '-';
                                                $keterangan = $temuan->deskripsi ?? '';
                                                $fotos = array_filter(array_map('trim', explode(',', $temuan->foto)));
                                            @endphp

                                            @foreach ($fotos as $_foto)
                                                @php
                                                    $path = public_path('images/5r/temuan/' . $_foto);
                                                @endphp

                                                @if (file_exists($path))
                                                    <div style="margin-bottom:10px;">

                                                        {{-- BADGE AREA --}}
                                                        <span
                                                            style="
                                                                display:inline-block;
                                                                background:#1d3557;
                                                                color:#fff;
                                                                font-size:9px;
                                                                padding:3px 8px;
                                                                border-radius:10px;
                                                                margin-bottom:4px;
                                                            ">
                                                            {{ $areaName }}
                                                        </span>

                                                        <br>

                                                        {{-- FOTO FULL WIDTH --}}
                                                        <img src="file://{{ $path }}"
                                                            style="
                                                                width: 100%;
                                                                max-height: 220px;
                                                                object-fit: contain;
                                                                border: 1px solid #ccc;
                                                                padding: 4px;
                                                                background: #fff;
                                                                display: block;
                                                            "
                                                            alt="Foto temuan {{ $areaName }}">

                                                        {{-- KETERANGAN TEMUAN (di bawah foto) --}}
                                                        @if (!empty($keterangan))
                                                            <div
                                                                style="
                                                                    margin-top: 6px;
                                                                    font-size: 11px;
                                                                    color: #444;
                                                                    font-style: italic;
                                                                    background: #f8f9fa;
                                                                    padding: 6px 8px;
                                                                    border-left: 3px solid #1d3557;
                                                                    line-height: 1.4;
                                                                ">
                                                                <strong>Deskripsi:</strong> {{ $keterangan }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @else
                                        <em>Tidak ada foto</em>
                                    @endif


                                    <br><br>

                                    <strong>Keterangan:</strong><br>
                                    {{ $row->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach

            </tbody>
        </table>

    </body>


    <!--end::Body-->

</html>
