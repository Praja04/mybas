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

                                <strong>Foto:</strong><br><br>

                                @php
                                    // Cek apakah ada foto dari field foto langsung
                                    $directFotos = [];
                                    if (!empty($row->foto)) {
                                        $directFotos = array_filter(array_map('trim', explode(',', $row->foto)));
                                    }

                                    // Kumpulkan semua temuan dengan foto
                                    $allTemuan = [];
                                    if ($row->temuan && $row->temuan->count()) {
                                        foreach ($row->temuan as $temuan) {
                                            if (!empty($temuan->foto)) {
                                                $fotos = array_filter(array_map('trim', explode(',', $temuan->foto)));
                                                foreach ($fotos as $foto) {
                                                    $allTemuan[] = [
                                                        'foto' => $foto,
                                                        'area' => $temuan->area->nama_area ?? '-',
                                                        'deskripsi' => $temuan->deskripsi ?? '',
                                                    ];
                                                }
                                            }
                                        }
                                    }

                                    // Jika tidak ada temuan tapi ada foto langsung, gunakan foto langsung
                                    if (empty($allTemuan) && !empty($directFotos)) {
                                        foreach ($directFotos as $foto) {
                                            $allTemuan[] = [
                                                'foto' => $foto,
                                                'area' => 'Foto dari Server Utama',
                                                'deskripsi' => '',
                                            ];
                                        }
                                    }
                                @endphp

                                @if (!empty($allTemuan))
                                    @foreach ($allTemuan as $temuanItem)
                                        @php
                                            $_foto = $temuanItem['foto'];
                                            $areaName = $temuanItem['area'];
                                            $keterangan = $temuanItem['deskripsi'];

                                            // Coba path lokal dulu (untuk foto temuan)
                                            $localPath = public_path('images/5r/temuan/' . $_foto);
                                            $imageData = false;
                                            $src = '';

                                            // Cek file lokal di folder temuan
                                            if (file_exists($localPath)) {
                                                $imageData = @file_get_contents($localPath);
                                            }

                                            // Jika tidak ada di temuan, coba di folder 5r langsung
                                            if ($imageData === false) {
                                                $localPath2 = public_path('images/5r/' . $_foto);
                                                if (file_exists($localPath2)) {
                                                    $imageData = @file_get_contents($localPath2);
                                                }
                                            }

                                            // Jika tidak ada di lokal, coba ambil dari IP 172
                                            if ($imageData === false) {
                                                $remotePath = 'http://172.21.5.105/images/5r/' . $_foto;
                                                $imageData = @file_get_contents($remotePath);
                                            }

                                            // Konversi ke base64 jika berhasil
                                            if ($imageData !== false) {
                                                // Deteksi MIME type
                                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                                $mime = $finfo->buffer($imageData);

                                                // Fallback MIME type
                                                if (!$mime || strpos($mime, 'image/') !== 0) {
                                                    $mime = 'image/jpeg';
                                                }

                                                $base64 = base64_encode($imageData);
                                                $src = 'data:' . $mime . ';base64,' . $base64;
                                            }
                                        @endphp

                                        <div style="margin-bottom:10px;">

                                            {{-- BADGE AREA --}}
                                            {{-- <span
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
                                            </span> --}}

                                            <br>

                                            @if (!empty($src))
                                                {{-- FOTO FULL WIDTH --}}
                                                <img src="{{ $src }}"
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
                                            @else
                                                <div
                                                    style="
                                                        padding: 20px;
                                                        background: #fff3cd;
                                                        border: 1px solid #ffc107;
                                                        text-align: center;
                                                        font-size: 10px;
                                                        color: #856404;
                                                    ">
                                                    <em>❌ Foto tidak ditemukan</em><br>
                                                    <small>{{ $_foto }}</small>
                                                </div>
                                            @endif

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
                                    @endforeach
                                @else
                                    <em>Tidak ada foto</em>
                                @endif


                                <br><br>

                                <strong>Keterangan:</strong><br>
                                {{ $row->keterangan ?? '-' }}
                            </td>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach

        </tbody>
    </table>

</body>

</html>
