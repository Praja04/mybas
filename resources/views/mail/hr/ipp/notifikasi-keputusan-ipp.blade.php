@php
    $isPeserta = $recipientType === 'peserta';
    $statusColor = $action === 'approved' ? '#1b5e20' : '#b71c1c';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keputusan Approval IPP {{ $periode }}</title>
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
                                    PMS — Keputusan Approval IPP
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#ffffff;border:1px solid #e1e4e8;border-top:none;border-radius:0 0 6px 6px;padding:24px;">
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;">Halo <strong>{{ $recipientNama }}</strong>,</p>
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;line-height:1.6;">
                                        @if ($isPeserta)
                                            Aktual IPP Anda periode <strong>{{ $periode }}</strong> tahapan <strong>{{ $tahapanLabel }}</strong> telah
                                            <strong style="color:{{ $statusColor }};">{{ strtolower($statusLabel) }}</strong>
                                            oleh <strong>{{ $approverNama }}</strong> ({{ $approverRole }}).
                                        @else
                                            IPP <strong>{{ $pesertaNama }}</strong> ({{ $pesertaNik }}) periode <strong>{{ $periode }}</strong> tahapan
                                            <strong>{{ $tahapanLabel }}</strong> telah
                                            <strong style="color:{{ $statusColor }};">{{ strtolower($statusLabel) }}</strong>
                                            oleh <strong>{{ $approverNama }}</strong> ({{ $approverRole }}).
                                            @if ($mustApprove)
                                                Sebagai <strong>Manager</strong>, Anda <strong>masih perlu melakukan Approve</strong> pada tahapan ini agar proses approval selesai.
                                            @endif
                                        @endif
                                    </p>
                                    @if ($mustApprove)
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#fff8e1;border-left:4px solid #FFA800;border-radius:3px;padding:12px 16px;font-size:13px;color:#8a6d1c;line-height:1.6;">
                                                    <strong>Perlu tindakan:</strong> Approve Manager
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#f6f8fa;border-left:4px solid {{ $statusColor }};border-radius:3px;padding:12px 16px;font-size:13px;color:#24292e;line-height:1.6;">
                                                    <strong>Catatan:</strong> {{ $note !== '' ? $note : '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="margin:0 0 24px;font-size:14px;color:#57606a;line-height:1.6;">
                                        @if ($isPeserta)
                                            Silakan cek detailnya melalui menu <strong>Isi Aktual IPP</strong> di PMS.
                                        @elseif ($mustApprove)
                                            Silakan lakukan approval melalui menu <strong>Approval IPP</strong> di PMS.
                                        @else
                                            Silakan cek detailnya melalui menu <strong>Approval IPP</strong> di PMS.
                                        @endif
                                    </p>
                                    <table align="center" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 auto;">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="background-color:#a80000;border-radius:5px;">
                                                    <a href="{{ $link }}" style="display:inline-block;padding:13px 24px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">{{ $linkLabel }}</a>
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
