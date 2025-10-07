<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Report Beras</title>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 5rem;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        @page {
            size: A4;
            margin: 1cm;
        }

        .center-text {
            text-align: center;
        }

        .logo {
            max-width: 100px;
            height: auto;
            float: left;
        }

        .header-text {
            float: left;
            margin-left: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table border="1">
        <tbody>
            <tr>
                <td colspan="10">
                    <div style="display: flex; align-items: center;">
                        <img src="https://i.ibb.co/YRCC1Zg/bas-logo.jpg" alt="Logo"
                            style="max-width: 100px; height: auto;">
                        <div style="margin-left: 10px;">
                            <p style="font-weight: bold; margin: 0;">FORM CHECK SHEET PENGAMBILAN BERAS</p>
                        </div>
                    </div>
                </td>
            </tr>

            <tr class="center-text">
                <td rowspan="3">No</td>
                <td rowspan="3">Tanggal</td>
                <td colspan="9">Beras</td>
            </tr>
            <tr class="center-text">
                <td colspan="3">Pengambilan</td>
                <td colspan="3">Pemakaian</td>
                <td colspan="3">Stock yang ada</td>
            </tr>
            <tr class="center-text">
                <td>S1</td>
                <td>S2</td>
                <td>S3</td>
                <td>S1</td>
                <td>S2</td>
                <td>S3</td>
                <td colspan="3"></td>
            </tr>
            @for ($day = 1; $day <= 32; $day++)
                @php
                    $formattedDay = sprintf('%04d-%02d-%02d', now()->year, now()->month, $day);
                    $dayData = $stock->where('tanggal', $formattedDay)->first();

                    $pengambilanS1 = $dayData ? $dayData->pengambilanBeras->where('Shift', '1')->first() : null;
                    $pengambilanS2 = $dayData ? $dayData->pengambilanBeras->where('Shift', '2')->first() : null;
                    $pengambilanS3 = $dayData ? $dayData->pengambilanBeras->where('Shift', '3')->first() : null;

                    $pemakaianS1 = $dayData ? $dayData->penggunaanBeras->where('Shift', '1')->first() : null;
                    $pemakaianS2 = $dayData ? $dayData->penggunaanBeras->where('Shift', '2')->first() : null;
                    $pemakaianS3 = $dayData ? $dayData->penggunaanBeras->where('Shift', '3')->first() : null;
                @endphp

                <tr class="center-text">
                    <td>{{ $day }}</td>
                    <td>
                        {{ $dayData ? \Carbon\Carbon::parse($dayData->tanggal)->format('Y-m-d') : '' }}
                    </td>
                    <td>{{ $pengambilanS1 ? $pengambilanS1->jumlah_pengambilan : '' }}</td>
                    <td>{{ $pengambilanS2 ? $pengambilanS2->jumlah_pengambilan : '' }}</td>
                    <td>{{ $pengambilanS3 ? $pengambilanS3->jumlah_pengambilan : '' }}</td>
                    <td>{{ $pemakaianS1 ? $pemakaianS1->jumlah_pemakaian : '' }}</td>
                    <td>{{ $pemakaianS2 ? $pemakaianS2->jumlah_pemakaian : '' }}</td>
                    <td>{{ $pemakaianS3 ? $pemakaianS3->jumlah_pemakaian : '' }}</td>
                    <td colspan="3">{{ $dayData ? $dayData->jumlah_stock : '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>

</html>
