<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Akses PMS</title>
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
                                    PMS — Verifikasi Akses
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#ffffff;border:1px solid #e1e4e8;border-top:none;border-radius:0 0 6px 6px;padding:24px;">
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;">Halo <strong>{{ $name !== '' ? $name : 'Pengguna PMS' }}</strong>,</p>
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;line-height:1.6;">
                                        Berikut kode OTP untuk membuka akses area PMS (IPP &amp; PK):
                                    </p>
                                    <div style="margin:0 0 16px;padding:18px 24px;background-color:#f6f8fa;border:1px solid #e1e4e8;border-radius:6px;text-align:center;">
                                        <div style="font-size:12px;color:#57606a;letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;">Kode OTP</div>
                                        <div style="font-size:34px;line-height:1;font-weight:800;letter-spacing:8px;color:#a80000;font-family:'Courier New',Courier,monospace;">{{ $code }}</div>
                                    </div>
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;line-height:1.6;">
                                        Kode ini berlaku selama <strong>{{ $expiresMinutes }} menit</strong> dan hanya dapat digunakan untuk akun Anda.
                                    </p>
                                    @if (!empty($requestedAt) || !empty($requestedFrom))
                                        <p style="margin:0 0 16px;font-size:12px;color:#57606a;line-height:1.6;">
                                            Waktu permintaan: <strong>{{ $requestedAt ?: '-' }}</strong><br>
                                            IP: <strong>{{ $requestedFrom ?: '-' }}</strong>
                                        </p>
                                    @endif
                                    <p style="margin:0;font-size:12px;color:#a80000;line-height:1.6;">
                                        Jika Anda tidak merasa meminta kode ini, abaikan email ini dan segera hubungi IT/HR.
                                        Jangan berikan kode ini kepada siapa pun.
                                    </p>
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
