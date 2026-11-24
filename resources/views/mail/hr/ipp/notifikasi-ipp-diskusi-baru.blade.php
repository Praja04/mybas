<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diskusi IPP {{ $periode }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;">
    @php
        $gmailTag = function ($email, $nama) use ($gmailSubject) {
            $label = $nama !== '' ? $nama : ($email !== '' ? $email : '-');
            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                return '<strong>' . e($label) . '</strong>';
            }
            return '<a href="https://mail.google.com/mail/?view=cm&fs=1&to=' . urlencode($email) . '&su=' . urlencode($gmailSubject) . '" target="_blank" rel="noopener" style="color:#0366d6;text-decoration:underline;">' . e($label) . '</a>';
        };
    @endphp
    <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;background-color:#f4f4f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
        <tbody>
            <tr>
                <td align="center" style="padding:24px 16px;">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;max-width:544px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="background-color:#a80000;border-radius:6px 6px 0 0;padding:18px 24px;color:#ffffff;font-size:18px;font-weight:bold;">
                                    PMS — Diskusi IPP
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color:#ffffff;border:1px solid #e1e4e8;border-top:none;border-radius:0 0 6px 6px;padding:24px;">
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;">Halo <strong>{{ $recipientNama }}</strong> ({{ $recipientRole }}),</p>
                                    <p style="margin:0 0 16px;font-size:14px;color:#24292e;line-height:1.6;">
                                        Peserta <strong>{{ $pesertaNama }}</strong> ({{ $pesertaNik }}) mengirim pertanyaan diskusi pada
                                        periode <strong>{{ $periode }}</strong>, tahapan <strong>{{ $tahapanLabel }}</strong>{{ $kpiText }}.
                                    </p>
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#f6f8fa;border:1px solid #e1e4e8;border-radius:4px;padding:12px 16px;font-size:13px;color:#24292e;line-height:1.6;">
                                                    <strong>Pertanyaan:</strong><br>
                                                    {!! nl2br(e($pertanyaan)) !!}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#ffffff;border:1px solid #e1e4e8;border-radius:4px;padding:12px 16px;font-size:13px;color:#57606a;line-height:1.9;">
                                                    <strong style="color:#24292e;">Tag:</strong><br>
                                                    Peserta: {!! $gmailTag($tagPesertaEmail, $tagPeserta) !!}<br>
                                                    Associate Manager: {!! $gmailTag($tagAmEmail, $tagAm) !!}<br>
                                                    Manager: {!! $gmailTag($tagManagerEmail, $tagManager) !!}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 0 20px;">
                                        <tbody>
                                            <tr>
                                                <td style="background-color:#fff8e1;border-left:4px solid #FFA800;border-radius:3px;padding:12px 16px;font-size:13px;color:#8a6d1c;line-height:1.6;">
                                                    <strong>Perlu tindakan:</strong> Jawab Diskusi
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p style="margin:0 0 24px;font-size:14px;color:#57606a;line-height:1.6;">
                                        Silakan buka menu <strong>Approval IPP</strong> di PMS untuk melihat detail dan menjawab pertanyaan peserta.
                                    </p>
                                    <table align="center" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;margin:0 auto;">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="background-color:#a80000;border-radius:5px;">
                                                    <a href="{{ $link }}" style="display:inline-block;padding:13px 24px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">Jawab Diskusi</a>
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
