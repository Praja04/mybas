<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Mail\HR\Pms\PmsAccessOtpMail;
use App\Models\HR\HrPmsAccessOtp;
use App\Support\PmsAccessOtpService;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PmsAccessOtpController extends Controller
{
    public function show($requestId, Request $request)
    {
        if (!PmsAccessOtpService::enabled()) {
            return PmsAccessOtpService::applyNoStore(redirect()->to(url('/hr')));
        }

        $user = Auth::user();
        $otp = HrPmsAccessOtp::where('request_id', $requestId)
            ->where('user_id', $user->id)
            ->first();

        if (!$otp) {
            abort(404);
        }

        if (PmsAccessOtpService::hasValidGrant()) {
            $target = $otp->intended_uri ?: '/hr';

            return PmsAccessOtpService::applyNoStore(redirect()->to(url($target)));
        }

        // Halaman lama bisa tertinggal saat middleware sudah membuat request baru.
        // Arahkan ke request aktif yang kodenya masih berlaku agar kode dari email
        // tetap bisa diverifikasi.
        if ($otp->consumed_at === null
            && !PmsAccessOtpService::hasActiveCode($otp)) {
            $activeSent = PmsAccessOtpService::activeSentRequestForUser($user);
            if ($activeSent && $activeSent->request_id !== $otp->request_id) {
                return PmsAccessOtpService::applyNoStore(
                    redirect()->route('hr.pms-access-otp.show', [
                        'requestId' => $activeSent->request_id,
                    ])
                );
            }
        }

        $emailInfo = PmsAccessOtpService::resolveEmail($user);
        $requestExpired = PmsAccessOtpService::requestExpired($otp);
        $consumed = $otp->consumed_at !== null;
        $codeSent = !$requestExpired && !$consumed
            && PmsAccessOtpService::hasActiveCode($otp);

        $lastSentAt = PmsAccessOtpService::latestSentAtForUser($user);
        $resendAvailableAt = $lastSentAt > 0
            ? $lastSentAt + PmsAccessOtpService::resendCooldownSeconds()
            : 0;

        $view = view('hr.pms-access-otp.challenge', [
            'requestId' => $otp->request_id,
            'maskedEmail' => PmsAccessOtpService::maskEmail($emailInfo['email']),
            'hasEmail' => $emailInfo['email'] !== null,
            'codeSent' => $codeSent,
            'codeExpiresAt' => $codeSent && $otp->expires_at ? $otp->expires_at->timestamp : 0,
            'resendAvailableAt' => $resendAvailableAt,
            'requestExpiresAt' => $otp->request_expires_at ? $otp->request_expires_at->timestamp : 0,
            'requestExpired' => $requestExpired,
            'consumed' => $consumed,
            'backUrl' => $otp->intended_uri ? url($otp->intended_uri) : url('/'),
            'serverTime' => now()->timestamp,
            'codeTtlMinutes' => PmsAccessOtpService::codeTtlMinutes(),
            'ttlMinutes' => PmsAccessOtpService::ttlMinutes(),
            'resendCooldownSeconds' => PmsAccessOtpService::resendCooldownSeconds(),
            'errorMessage' => session('pms_otp_error'),
            'statusMessage' => session('pms_otp_status'),
        ]);

        return PmsAccessOtpService::applyNoStore($view);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'nullable|string|max:36',
            'return_url' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $emailInfo = PmsAccessOtpService::resolveEmail($user);

        if ($emailInfo['email'] === null) {
            return $this->errorResponse(
                $request,
                'Email Anda belum terdaftar atau tidak valid. Hubungi HR/IT untuk memperbaiki data email.',
                422,
                ['request_id' => $validated['request_id'] ?? null]
            );
        }

        $requestId = trim((string) ($validated['request_id'] ?? ''));

        $outcome = DB::transaction(function () use ($request, $user, $requestId, $validated) {
            // Mutex per user. Transaksi ini singkat; email dikirim setelah commit.
            PmsAccessOtpService::lockUserForOtp($user);

            $otp = null;

            if ($requestId !== '') {
                $otp = HrPmsAccessOtp::where('request_id', $requestId)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();
            }

            if ($otp === null) {
                $otp = PmsAccessOtpService::activeRequestForUser($user, true);
            }

            if ($otp === null) {
                if ($requestId !== '') {
                    return [
                        'status' => 404,
                        'message' => 'Permintaan OTP tidak ditemukan.',
                    ];
                }

                $intendedUri = PmsAccessOtpService::normalizeIntendedUri($validated['return_url'] ?? null, $request);
                $otp = PmsAccessOtpService::createRequest($request, $user, $intendedUri);
            }

            if ($otp->consumed_at !== null) {
                return [
                    'status' => 409,
                    'message' => 'Permintaan OTP sudah digunakan. Silakan buka kembali halaman PMS.',
                    'request_id' => $otp->request_id,
                ];
            }

            if (PmsAccessOtpService::requestExpired($otp)) {
                $activeSent = PmsAccessOtpService::activeSentRequestForUser($user, true);
                if ($activeSent) {
                    return ['status' => 200, 'already_sent' => true, 'otp' => $activeSent];
                }

                return [
                    'status' => 410,
                    'message' => 'Sesi verifikasi OTP sudah kedaluwarsa. Silakan buka kembali halaman PMS.',
                    'request_id' => $otp->request_id,
                ];
            }

            $nowTimestamp = now()->timestamp;
            $latestSentAt = PmsAccessOtpService::latestSentAtForUser($user);
            $cooldownUntil = $latestSentAt > 0
                ? $latestSentAt + PmsAccessOtpService::resendCooldownSeconds()
                : 0;

            if ($cooldownUntil > $nowTimestamp) {
                $activeSent = PmsAccessOtpService::activeSentRequestForUser($user, true);
                if ($activeSent) {
                    return ['status' => 200, 'already_sent' => true, 'otp' => $activeSent];
                }

                $retryAfter = max(1, $cooldownUntil - $nowTimestamp);

                return [
                    'status' => 429,
                    'message' => 'Mohon tunggu ' . $retryAfter . ' detik sebelum mengirim ulang kode OTP.',
                    'retry_after' => $retryAfter,
                    'request_id' => $otp->request_id,
                ];
            }

            $limiter = app(RateLimiter::class);
            $limiterKey = 'pms-access-otp-send:' . $user->id;
            $maxSends = PmsAccessOtpService::maxSendsPerWindow();

            if ($limiter->tooManyAttempts($limiterKey, $maxSends)) {
                $activeSent = PmsAccessOtpService::activeSentRequestForUser($user, true);
                if ($activeSent) {
                    return ['status' => 200, 'already_sent' => true, 'otp' => $activeSent];
                }

                $retryAfter = max(1, (int) $limiter->availableIn($limiterKey));

                return [
                    'status' => 429,
                    'message' => 'Terlalu banyak permintaan OTP. Coba lagi dalam ' . $retryAfter . ' detik.',
                    'retry_after' => $retryAfter,
                    'request_id' => $otp->request_id,
                ];
            }

            // Hit dicatat di dalam critical section supaya request paralel
            // tidak bisa sama-sama lolos pengecekan.
            $limiter->hit($limiterKey, PmsAccessOtpService::sendWindowMinutes() * 60);

            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $codeExpiresAt = now()->addMinutes(PmsAccessOtpService::codeTtlMinutes());
            $sentAt = now();

            $otp->forceFill([
                'code_hash' => Hash::make($code),
                'expires_at' => $codeExpiresAt,
                'attempts' => 0,
                'last_sent_at' => $sentAt,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ])->save();

            // increment atomik; jangan memakai nilai send_count yang sudah basi.
            $otp->increment('send_count');

            return [
                'status' => 200,
                'otp' => $otp,
                'code' => $code,
                'code_expires_at' => $codeExpiresAt->timestamp,
                'sent_at' => $sentAt->timestamp,
            ];
        });

        if (($outcome['status'] ?? 200) !== 200) {
            return $this->errorResponse(
                $request,
                (string) ($outcome['message'] ?? 'Gagal mengirim kode OTP.'),
                (int) $outcome['status'],
                [
                    'request_id' => $outcome['request_id'] ?? null,
                    'retry_after' => $outcome['retry_after'] ?? null,
                ]
            );
        }

        /** @var HrPmsAccessOtp $otp */
        $otp = $outcome['otp'];

        if (!empty($outcome['already_sent'])) {
            return $this->sendSuccessResponse($request, $otp, $emailInfo, [
                'already_sent' => true,
                'message' => 'Kode OTP sudah dikirim. Silakan cek email Anda.',
            ]);
        }

        $code = (string) $outcome['code'];
        $codeExpiresAt = (int) $outcome['code_expires_at'];

        try {
            Mail::mailer((string) config('pms.access_otp.mailer', 'smtp'))
                ->to($emailInfo['email'], $emailInfo['name'] !== '' ? $emailInfo['name'] : (string) $user->name)
                ->send(new PmsAccessOtpMail(
                    $code,
                    PmsAccessOtpService::codeTtlMinutes(),
                    $emailInfo['name'] !== '' ? $emailInfo['name'] : (string) $user->name,
                    now()->format('d/m/Y H:i:s'),
                    (string) $request->ip()
                ));
        } catch (\Throwable $e) {
            Log::error('PMS access OTP email failed', [
                'user_id' => $user->id,
                'request_id' => $otp->request_id,
                'error' => $e->getMessage(),
            ]);

            PmsAccessOtpService::audit($request, 'otp-fail', [
                'request_id' => $otp->request_id,
                'reason' => 'mail_failed_or_unknown',
                'code_preserved' => true,
            ], 202);

            // Jangan hapus code_hash. SMTP bisa saja sudah menerima email
            // tetapi koneksi timeout sebelum aplikasi menerima konfirmasi.
            return $this->sendSuccessResponse($request, $otp, $emailInfo, [
                'delivery_unknown' => true,
                'message' => 'Kemungkinan email sudah terkirim. Cek inbox sebelum mengirim ulang.',
            ]);
        }

        PmsAccessOtpService::audit($request, 'otp-send', [
            'request_id' => $otp->request_id,
            'email_source' => $emailInfo['source'],
            'expires_at' => $codeExpiresAt,
        ]);

        return $this->sendSuccessResponse($request, $otp, $emailInfo);
    }

    private function sendSuccessResponse(Request $request, HrPmsAccessOtp $otp, array $emailInfo, array $extra = [])
    {
        $message = (string) ($extra['message'] ?? 'Kode OTP telah dikirim ke email terdaftar.');
        $lastSentAt = $otp->last_sent_at ? (int) $otp->last_sent_at->timestamp : now()->timestamp;
        $resendAvailableAt = max(
            now()->timestamp,
            $lastSentAt + PmsAccessOtpService::resendCooldownSeconds()
        );

        $payload = array_merge([
            'success' => true,
            'message' => $message,
            'request_id' => $otp->request_id,
            'masked_email' => PmsAccessOtpService::maskEmail($emailInfo['email']),
            'expires_at' => $otp->expires_at ? (int) $otp->expires_at->timestamp : 0,
            'resend_available_at' => $resendAvailableAt,
            'server_time' => now()->timestamp,
            'code_ttl_minutes' => PmsAccessOtpService::codeTtlMinutes(),
            'already_sent' => false,
            'delivery_unknown' => false,
        ], $extra);

        if ($request->expectsJson() || $request->ajax()) {
            return PmsAccessOtpService::applyNoStore(response()->json($payload));
        }

        return PmsAccessOtpService::applyNoStore(
            redirect()->route('hr.pms-access-otp.show', ['requestId' => $otp->request_id])
                ->with('pms_otp_status', $message)
        );
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|string|max:36',
            'code' => 'required|regex:/^[0-9]{6}$/',
        ]);

        $user = Auth::user();
        $result = DB::transaction(function () use ($validated, $user) {
            $otp = HrPmsAccessOtp::where('request_id', $validated['request_id'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$otp) {
                return ['status' => 404, 'message' => 'Permintaan OTP tidak ditemukan.'];
            }
            if ($otp->consumed_at !== null) {
                return ['status' => 409, 'message' => 'Permintaan OTP sudah digunakan. Silakan buka kembali halaman PMS.'];
            }
            if (PmsAccessOtpService::requestExpired($otp)) {
                return ['status' => 410, 'message' => 'Sesi verifikasi OTP sudah kedaluwarsa. Silakan buka kembali halaman PMS.'];
            }
            if (empty($otp->code_hash) || PmsAccessOtpService::codeExpired($otp)) {
                return ['status' => 422, 'message' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.'];
            }

            $maxAttempts = PmsAccessOtpService::maxAttempts();
            if ((int) $otp->attempts >= $maxAttempts) {
                return [
                    'status' => 429,
                    'message' => 'Terlalu banyak percobaan. Silakan kirim ulang kode OTP.',
                    'reason' => 'max_attempts',
                    'otp' => $otp,
                ];
            }

            $otp->attempts = (int) $otp->attempts + 1;

            if (!Hash::check((string) $validated['code'], (string) $otp->code_hash)) {
                $reachedMax = (int) $otp->attempts >= $maxAttempts;

                if ($reachedMax) {
                    $otp->code_hash = null;
                    $otp->expires_at = null;
                }

                $otp->save();

                return [
                    'status' => $reachedMax ? 429 : 422,
                    'message' => $reachedMax
                        ? 'Terlalu banyak percobaan. Silakan kirim ulang kode OTP.'
                        : 'Kode OTP tidak sesuai. Sisa percobaan: ' . max(0, $maxAttempts - (int) $otp->attempts) . '.',
                    'reason' => 'wrong_code',
                    'remaining' => max(0, $maxAttempts - (int) $otp->attempts),
                    'otp' => $otp,
                ];
            }

            $otp->consumed_at = now();
            $otp->code_hash = null;
            $otp->expires_at = null;
            $otp->save();

            return ['status' => 200, 'otp' => $otp];
        });

        if ($result['status'] !== 200) {
            PmsAccessOtpService::audit($request, 'otp-fail', [
                'request_id' => $validated['request_id'],
                'reason' => $result['reason'] ?? 'invalid',
                'remaining' => $result['remaining'] ?? null,
            ], (int) $result['status']);

            return $this->errorResponse(
                $request,
                $result['message'],
                (int) $result['status'],
                [
                    'request_id' => $validated['request_id'],
                    'remaining' => $result['remaining'] ?? null,
                ]
            );
        }

        /** @var HrPmsAccessOtp $otp */
        $otp = $result['otp'];
        $grant = PmsAccessOtpService::putGrant((int) $user->id, $otp->request_id);
        $redirectUrl = $otp->intended_uri ? url($otp->intended_uri) : url('/hr');

        PmsAccessOtpService::audit($request, 'otp-verify', [
            'request_id' => $otp->request_id,
            'grant_expires_at' => $grant['expires_at'],
        ]);

        $payload = [
            'success' => true,
            'message' => 'Verifikasi OTP berhasil.',
            'expires_at' => $grant['expires_at'],
            'server_time' => now()->timestamp,
            'ttl_minutes' => PmsAccessOtpService::ttlMinutes(),
            'redirect_url' => $redirectUrl,
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return PmsAccessOtpService::applyNoStore(response()->json($payload));
        }

        return PmsAccessOtpService::applyNoStore(redirect()->to($redirectUrl));
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        $grant = PmsAccessOtpService::grant();
        $emailInfo = PmsAccessOtpService::resolveEmail($user);

        $requestId = trim((string) $request->query('request_id', ''));
        $otp = null;

        if ($requestId !== '') {
            $otp = HrPmsAccessOtp::where('request_id', $requestId)
                ->where('user_id', $user->id)
                ->first();
        }

        if (!$otp) {
            $otp = PmsAccessOtpService::activeRequestForUser($user);
        }

        // Jika halaman memakai request lama, gunakan request aktif yang
        // kodenya masih berlaku agar frontend bisa melakukan rekonsiliasi.
        $activeSent = PmsAccessOtpService::activeSentRequestForUser($user);
        if ($activeSent && (!$otp || !PmsAccessOtpService::hasActiveCode($otp))) {
            $otp = $activeSent;
        }

        $requestExpired = $otp ? PmsAccessOtpService::requestExpired($otp) : false;
        $consumed = $otp ? $otp->consumed_at !== null : false;
        $codeSent = $otp && !$requestExpired && !$consumed
            && PmsAccessOtpService::hasActiveCode($otp);
        $latestSentAt = PmsAccessOtpService::latestSentAtForUser($user);

        return PmsAccessOtpService::applyNoStore(response()->json([
            'enabled' => PmsAccessOtpService::enabled(),
            'has_grant' => PmsAccessOtpService::enabled() && $grant !== null,
            'expires_at' => $grant !== null ? (int) $grant['expires_at'] : null,
            'server_time' => now()->timestamp,
            'ttl_minutes' => PmsAccessOtpService::ttlMinutes(),
            'code_ttl_minutes' => PmsAccessOtpService::codeTtlMinutes(),
            'has_email' => $emailInfo['email'] !== null,
            'masked_email' => PmsAccessOtpService::maskEmail($emailInfo['email']),
            'request_id' => $otp ? $otp->request_id : null,
            'code_sent' => $codeSent,
            'code_expires_at' => $codeSent ? (int) $otp->expires_at->timestamp : 0,
            'last_sent_at' => $latestSentAt,
            'resend_available_at' => $latestSentAt > 0
                ? $latestSentAt + PmsAccessOtpService::resendCooldownSeconds()
                : 0,
            'request_expired' => $requestExpired,
            'consumed' => $consumed,
        ]));
    }

    private function errorResponse(Request $request, string $message, int $status, array $extra = [])
    {
        $payload = array_merge([
            'success' => false,
            'message' => $message,
            'server_time' => now()->timestamp,
        ], $extra);

        if ($request->expectsJson() || $request->ajax()) {
            return PmsAccessOtpService::applyNoStore(response()->json($payload, $status));
        }

        $requestId = isset($extra['request_id']) ? trim((string) $extra['request_id']) : '';

        if ($requestId !== '') {
            return PmsAccessOtpService::applyNoStore(
                redirect()->route('hr.pms-access-otp.show', ['requestId' => $requestId])
                    ->with('pms_otp_error', $message)
            );
        }

        return PmsAccessOtpService::applyNoStore(redirect()->to(url('/'))->with('pms_otp_error', $message));
    }
}
