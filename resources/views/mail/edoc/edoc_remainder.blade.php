<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EMAIL</title>
</head>
<body style="padding: 0;margin:0; font-family: Arial, Helvetica, sans-serif">
    <div style="margin: 10px; padding: 20px; background-color: rgb(255, 212, 20)">
        <p>Halloo <br />
        Mohon dapat mengambil Doc/Log Anda di Pos 2 <br />
        Kami <strong>mengingatkan kembali</strong> <br />
        Untuk mencegah kejadian yang tidak diinginkan seperti kerusakan atau kehilangan, maka diharapkan barang-barang atau dokumen tersebut untuk <strong>segera diambil</strong>. <br />
        Apabila terjadi kerusakan/ kehilangan barang karena tidak diambil sesuai dengan pemberitahuan yang ada, maka <strong>bukan menjadi tanggung jawab GA</strong>. <br />
        Terima kasih atas kerjasama Bapak/ Ibu.</p>
    </div>
    <table style="margin: 10px">
        <tbody>
            <tr>
                <th style="text-align: left">Department Tujuan Penerima</th>
                <td>:</td>
                <td>{{ $data->dept_penerima }}</td>
            </tr>
            <tr>
                <th style="text-align: left">Nama Tujuan Penerima</th>
                <td>:</td>
                <td>{{ $data->nama_penerima }}</td>
            </tr>
            <tr>
                <th style="text-align: left">PT Pengirim</th>
                <td>:</td>
                <td>{{ $data->nama_pt_pengirim }}</td>
            </tr>
            <tr>
                <th style="text-align: left">Tanggal Kedatangan</th>
                <td>:</td>
                <td>{{ formatTanggalIndonesia2($data->tanggal_kedatangan) }}</td>
            </tr>
            <tr>
                <th style="text-align: left">Jenis</th>
                <td>:</td>
                <td>{{ $data->jenis }}</td>
            </tr>
            <tr>
                <th style="text-align: left">Keterangan</th>
                <td>:</td>
                <td>{{ $data->keterangan }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
