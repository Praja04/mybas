<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reminder Pengambilan Dokumen</title>
</head>

<body style="padding: 0; margin: 0; ffont-family: Arial, Helvetica, sans-serif; background-color: #F9F9F9;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%"
        style="table-layout:fixed;background-color:#F9F9F9;">
        <tbody>
            <tr>
                <td align="center" valign="top" style="padding-right:10px;padding-left:10px;">

                    <!-- HEADER -->
                    <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%">
                        <tbody>
                            <tr>
                                <td align="center" valign="top" style="padding-top: 40px; padding-bottom: 10px;">
                                    <p style="font-size:20px; font-weight:600; color:#333; margin:0;">MyBAS Online
                                        Notification</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- BODY -->
                    <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style="background-color:#FFFFFF;border:1px solid #E5E5E5;">
                                        <tbody>
                                            <!-- TOP BORDER -->
                                            <tr>
                                                <td height="3"
                                                    style="background-color:rgb(201,0,27); font-size:1px; line-height:3px;">
                                                    &nbsp;</td>
                                            </tr>

                                            <!-- TITLE -->
                                            <tr>
                                                <td align="center" style="padding:20px;">
                                                    <h2 style="font-size:24px; font-weight:600; margin:0; color:#000;">
                                                        Reminder Pengambilan E-Document</h2>
                                                    <p style="font-size:14px; color:#777; margin-top:5px;">E-Document
                                                        Reminder Notification</p>
                                                </td>
                                            </tr>

                                            <!-- MESSAGE -->
                                            <tr>
                                                <td align="left" style="padding:20px; background-color:#FFF7CC;">
                                                    <p style="margin:0; font-size:14px; line-height:1.6; color:#333;">
                                                        <strong>Yth. {{ $data->nama_penerima }},</strong><br><br>
                                                        Mohon untuk segera mengambil <strong>dokumen/barang</strong>
                                                        Anda di <strong>POS Security atau Resepsionis</strong>.<br><br>
                                                        Kami <strong>mengingatkan kembali</strong> bahwa untuk mencegah
                                                        hal-hal yang tidak diinginkan seperti <strong>kerusakan atau
                                                            kehilangan</strong>, diharapkan agar dokumen atau barang
                                                        tersebut <strong>segera diambil</strong>.<br><br>
                                                        Apabila terjadi kerusakan atau kehilangan akibat tidak diambil
                                                        sesuai pemberitahuan, maka <strong>tanggung jawab sepenuhnya
                                                            berada di pihak penerima</strong>.<br><br>
                                                        Terima kasih atas perhatian dan kerja samanya.
                                                    </p>
                                                </td>
                                            </tr>

                                            <!-- TABLE DETAIL -->
                                            <tr>
                                                <td align="center" valign="top" style="padding:20px;">
                                                    <table border="1" cellpadding="6" cellspacing="0" width="100%"
                                                        style="border-collapse:collapse; border:1px solid #ddd; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#333;">
                                                        <thead style="background-color:#f8f8f8;">
                                                            <tr style="text-align:left;">
                                                                <th>Departemen Tujuan</th>
                                                                <th>Nama Penerima</th>
                                                                <th>PT Pengirim</th>
                                                                <th>Tanggal Kedatangan</th>
                                                                <th>Jenis</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr style="text-align:left;">
                                                                <td>{{ $data->dept_penerima }}</td>
                                                                <td>{{ $data->nama_penerima }}</td>
                                                                <td>{{ $data->nama_pt_pengirim }}</td>
                                                                <td>{{ formatTanggalIndonesia2($data->tanggal_kedatangan) }}
                                                                </td>
                                                                <td>{{ $data->jenis }}</td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="5"
                                                                    style="background-color:#f8f8f8; padding:8px; text-align:left;">
                                                                    Keterangan:</th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" style="padding:8px;">
                                                                    {{ $data->keterangan }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </td>
                                            </tr>

                                            <!-- SIGNATURE -->
                                            <tr>
                                                <td align="center" style="padding:30px 10px 20px 10px;">
                                                    <i style="color:#999; font-size:13px;">PT. Bumi Alam Segar Team</i>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- FOOTER -->
                    <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="padding:10px;">
                                                    <p
                                                        style="color:#777; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:12px; line-height:20px; margin:0;">
                                                        © PT. Bumi Alam Segar {{ date('Y') }} | Departemen HRGA
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding-bottom:20px;">
                                                    <p
                                                        style="color:#777; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:12px; margin:0;">
                                                        <a href="#"
                                                            style="color:#777;text-decoration:underline;">BAS APP</a> |
                                                        <a href="#"
                                                            style="color:#777;text-decoration:underline;">ITE - 4003</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
