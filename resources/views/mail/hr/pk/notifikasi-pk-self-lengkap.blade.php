<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Assessment PK Sudah Lengkap</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;background-color:#f4f4f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
        <tbody>
            <tr>
                <td align="center" style="padding:24px 16px;">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;max-width:544px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="background-color:#a80000;border-radius:6px 6px 0 0;padding:18px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                                    PMS — Penilaian Karya
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#ffffff;border:1px solid #e1e4e8;border-top:none;border-radius:0 0 6px 6px;padding:24px;">
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;">Halo <strong>{{ $recipientNama }}</strong>,</p>
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;line-height:1.6;">
                                        Self Assessment Penilaian Karya peserta <strong>{{ $pesertaNama }}</strong> ({{ $pesertaNik }}) periode
                                        <strong>{{ $periode }}</strong> group <strong>{{ $group }}</strong> telah <strong style="color:#1b5e20;">lengkap diisi</strong>.
                                        Sebagai <strong>{{ $recipientRole }}</strong>, Anda dapat melanjutkan <strong>Leader Assessment</strong> dan proses approval.
                                    </p>
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#fff8e1;border-left:4px solid #FFA800;border-radius:3px;padding:12px 16px;font-size:13px;color:#8a6d1c;line-height:1.6;">
                                                    <strong>Perlu tindakan:</strong> Leader Assessment &amp; Approval
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="margin:0 0 24px;font-size:14px;color:#57606a;line-height:1.6;">
                                        Silakan buka menu <strong>Penilaian Karya</strong> di PMS untuk menindaklanjuti.
                                    </p>
                                    <table align="center" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 auto;">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="background-color:#a80000;border-radius:5px;">
                                                    <a href="{{ $link }}" style="display:inline-block;padding:13px 24px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">Buka Penilaian Karya</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding:16px;font-size:12px;color:#8b949e;">
                                    Email otomatis dari PMS PT Bumi Alam Segar. Mohon tidak membalas email ini.
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
