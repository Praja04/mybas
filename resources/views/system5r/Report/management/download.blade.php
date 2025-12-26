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
            $colors = ['#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51'];
            $textColors = ['#fff', '#fff', '#000', '#000', '#000'];
            $jawaban = $jawabanGroup ? $jawabanGroup->jawaban : collect();
        @endphp

        @foreach ($pertanyaan as $group)
            <table style="width:100%; border-collapse:collapse; margin-bottom:25px;">
                <tbody>

                    {{-- HEADER TABLE --}}
                    <tr style="background:#a80000; color:#fff;">
                        <th style="border:1px solid #000; width:60%; padding:5px;">PERTANYAAN</th>
                        <th style="border:1px solid #000; width:40%; padding:5px;">NILAI & FOTO</th>
                    </tr>

                    @foreach (['RINGKAS', 'RAPI', 'RESIK', 'RAWAT', 'RAJIN'] as $idx => $jenis)
                        @php
                            $__pertanyaan = $group->pertanyaan->where('jenis', $jenis);
                        @endphp

                        {{-- ROW GROUP (AMAN PAGE BREAK) --}}
                        <tr>
                            <td colspan="2"
                                style="
                            border:1px solid #000;
                            background:{{ $colors[$idx] }};
                            color:{{ $textColors[$idx] }};
                            font-weight:bold;
                            text-align:center;
                            padding:6px;
                            page-break-inside:avoid;
                        ">
                                {{ $jenis }}
                            </td>
                        </tr>

                        @foreach ($__pertanyaan as $_pertanyaan)
                            @php
                                $__jawaban = $jawaban->where('id_pertanyaan', $_pertanyaan->id_pertanyaan)->first();
                            @endphp

                            <tr>
                                <td style="border:1px solid #000; padding:6px;">
                                    <strong>ITEM PERIKSA</strong><br>
                                    {!! str_replace('||--||', '&', $_pertanyaan->item_periksa) !!}

                                    <br><br>

                                    <strong>KETERANGAN</strong><br>
                                    {!! str_replace('||--||', '&', $_pertanyaan->keterangan) !!}
                                </td>

                                <td style="border:1px solid #000; padding:6px;">
                                    <strong>Nilai:</strong>
                                    {{ $__jawaban->nilai ?? '-' }}

                                    <br><br>

                                    <strong>Foto:</strong><br>

                                    @if ($__jawaban && !empty($__jawaban->foto))
                                        @foreach (explode(',', $__jawaban->foto) as $_foto)
                                            @php
                                                $path = public_path('images/5r/temuan/' . trim($_foto));
                                            @endphp

                                            @if (file_exists($path))
                                                <img src="file://{{ $path }}"
                                                    style="max-width:220px; margin-bottom:5px;">
                                            @else
                                                <em style="color:red;">Foto tidak ditemukan</em>
                                            @endif
                                        @endforeach
                                    @else
                                        <em>Tidak ada foto</em>
                                    @endif

                                    <br><br>

                                    <strong>Keterangan:</strong><br>
                                    {{ $__jawaban->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach

                </tbody>
            </table>

            @if (!$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach


    </body>

    <!--end::Body-->

</html>
