<?php

namespace App\Support;

use App\Models\HR\HrPmsAccessOtp;
use App\Models\HR\HrPmsActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PmsAccessOtpService
{
    public const SESSION_KEY = 'pms_access_otp';

    public const PURPOSE = 'pms_access';

    public static function enabled(): bool
    {
        return (bool) config('pms.access_otp.enabled', true);
    }

    public static function ttlMinutes(): int
    {
        return max(1, (int) config('pms.access_otp.ttl_minutes', 20));
    }

    public static function codeTtlMinutes(): int
    {
        return max(1, (int) config('pms.access_otp.code_ttl_minutes', 5));
    }

    public static function requestTtlMinutes(): int
    {
        return max(1, (int) config('pms.access_otp.request_ttl_minutes', 30));
    }

    public static function maxAttempts(): int
    {
        return max(1, (int) config('pms.access_otp.max_attempts', 5));
    }

    public static function resendCooldownSeconds(): int
    {
        return max(1, (int) config('pms.access_otp.resend_cooldown_seconds', 60));
    }

    public static function maxSendsPerWindow(): int
    {
        return max(1, (int) config('pms.access_otp.max_sends_per_window', 3));
    }

    public static function sendWindowMinutes(): int
    {
        return max(1, (int) config('pms.access_otp.send_window_minutes', 15));
    }

    /**
     * Grant akses PMS yang masih valid untuk user login.
     */
    public static function grant(): ?array
    {
        return self::grantForUserId(Auth::id());
    }

    public static function grantForUserId($userId): ?array
    {
        $grant = Session::get(self::SESSION_KEY);
        if (!is_array($grant)) {
            return null;
        }

        if (!$userId || (int) ($grant['user_id'] ?? 0) !== (int) $userId) {
            self::clearGrant();
            return null;
        }

        if ((int) ($grant['expires_at'] ?? 0) <= now()->timestamp) {
            self::clearGrant();
            return null;
        }

        return $grant;
    }

    public static function hasValidGrant(): bool
    {
        return self::grant() !== null;
    }

    public static function hasValidGrantForUserId($userId): bool
    {
        return self::grantForUserId($userId) !== null;
    }

    /**
     * Dipakai juga oleh channel websocket PMS.
     */
    public static function accessAllowed($userId = null): bool
    {
        if (!self::enabled()) {
            return true;
        }

        $userId = $userId !== null ? $userId : Auth::id();

        return self::hasValidGrantForUserId($userId);
    }

    public static function putGrant(int $userId, ?string $requestId = null): array
    {
        $expiresAt = now()->addMinutes(self::ttlMinutes());

        $grant = [
            'user_id' => (int) $userId,
            'request_id' => $requestId,
            'verified_at' => now()->timestamp,
            'expires_at' => $expiresAt->timestamp,
        ];

        Session::put(self::SESSION_KEY, $grant);

        return $grant;
    }

    public static function clearGrant(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Email OTP: hanya dari email akun login (users.email).
     *
     * @return array{email: ?string, name: string, nik: string, source: ?string}
     */
    public static function resolveEmail($user): array
    {
        $name = trim((string) ($user->name ?? ''));
        $nik = strtoupper(trim((string) ($user->nik ?? '')));
        $email = trim((string) ($user->email ?? ''));

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $email = '';
        }

        return [
            'email' => $email !== '' ? $email : null,
            'name' => $name,
            'nik' => $nik,
            'source' => $email !== '' ? 'users' : null,
        ];
    }

    public static function maskEmail(?string $email): string
    {
        $email = trim((string) $email);
        if ($email === '' || strpos($email, '@') === false) {
            return '';
        }

        $parts = explode('@', $email, 2);
        $local = trim((string) ($parts[0] ?? ''));
        $domain = trim((string) ($parts[1] ?? ''));

        if ($domain === '') {
            return '';
        }

        $visible = $local !== '' ? substr($local, 0, 1) : '';

        return $visible . '***@' . $domain;
    }

    /**
     * Simpan path+query lokal saja. Tidak boleh open redirect.
     */
    public static function normalizeIntendedUri($uri, ?Request $request = null): ?string
    {
        $uri = trim((string) $uri);
        if ($uri === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $uri) === 1) {
            $parts = parse_url($uri);
            if ($parts === false) {
                return null;
            }

            $path = (string) ($parts['path'] ?? '');
            $query = (string) ($parts['query'] ?? '');
            $uri = $path . ($query !== '' ? '?' . $query : '');
        }

        // Buang karakter kontrol agar tidak bisa dipakai untuk header injection.
        $uri = preg_replace('/[\x00-\x1F\x7F]/', '', $uri);
        if ($uri === null) {
            return null;
        }

        $request = $request ?: request();
        $basePath = rtrim((string) $request->getBasePath(), '/');

        if ($basePath !== '' && strpos($uri, $basePath . '/') === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . ltrim($uri, '/');
        }

        if (strpos($uri, '//') === 0) {
            return null;
        }
        if (strpos($uri, '..') !== false) {
            return null;
        }
        if (preg_match('#^/hr(/|$)#', $uri) !== 1) {
            return null;
        }
        if (strpos($uri, '/hr/pms-access-otp') === 0) {
            return null;
        }

        return substr($uri, 0, 1000);
    }

    public static function createRequest(Request $request, $user, ?string $intendedUri): HrPmsAccessOtp
    {
        $requestId = (string) Str::uuid();
        $emailInfo = self::resolveEmail($user);

        $otp = HrPmsAccessOtp::create([
            'request_id' => $requestId,
            'user_id' => (int) $user->id,
            'nik' => $emailInfo['nik'] !== '' ? $emailInfo['nik'] : null,
            'intended_uri' => $intendedUri,
            'purpose' => self::PURPOSE,
            'request_expires_at' => now()->addMinutes(self::requestTtlMinutes()),
            'attempts' => 0,
            'send_count' => 0,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        self::audit($request, 'otp-request', [
            'request_id' => $requestId,
            'intended_uri' => $intendedUri,
            'purpose' => self::PURPOSE,
        ]);

        return $otp;
    }

    public static function requestExpired(HrPmsAccessOtp $otp): bool
    {
        return !$otp->request_expires_at || $otp->request_expires_at->lte(now());
    }

    public static function codeExpired(HrPmsAccessOtp $otp): bool
    {
        return !$otp->expires_at || $otp->expires_at->lte(now());
    }

    public static function hasActiveCode(HrPmsAccessOtp $otp): bool
    {
        return !empty($otp->code_hash)
            && $otp->expires_at !== null
            && $otp->expires_at->gt(now());
    }

    /**
     * Request OTP yang masih boleh dipakai untuk user ini.
     * Satu user hanya boleh punya satu request aktif agar request_id
     * tidak berubah-ubah saat halaman PMS dibuka ulang.
     */
    public static function activeRequestForUser($user, bool $lock = false): ?HrPmsAccessOtp
    {
        $query = HrPmsAccessOtp::where('user_id', (int) $user->id)
            ->where('purpose', self::PURPOSE)
            ->whereNull('consumed_at')
            ->where('request_expires_at', '>', now())
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Request aktif yang sudah punya kode OTP dan kodenya masih berlaku.
     * Dipakai untuk idempotensi: klik kirim kedua tidak boleh menghasilkan
     * 429/error jika email pertama sebenarnya sudah terkirim.
     */
    public static function activeSentRequestForUser($user, bool $lock = false): ?HrPmsAccessOtp
    {
        $query = HrPmsAccessOtp::where('user_id', (int) $user->id)
            ->where('purpose', self::PURPOSE)
            ->whereNull('consumed_at')
            ->where('request_expires_at', '>', now())
            ->whereNotNull('code_hash')
            ->where('expires_at', '>', now())
            ->orderByDesc('last_sent_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public static function latestSentAtForUser($user): int
    {
        $latest = HrPmsAccessOtp::where('user_id', (int) $user->id)
            ->where('purpose', self::PURPOSE)
            ->whereNotNull('last_sent_at')
            ->orderByDesc('last_sent_at')
            ->first();

        return $latest && $latest->last_sent_at
            ? (int) $latest->last_sent_at->timestamp
            : 0;
    }

    /**
     * Mutex per user untuk mencegah dua request mengirim OTP bersamaan.
     * Memakai tabel lock khusus agar tidak mengunci row di tabel users.
     * Harus dipanggil di dalam transaksi database.
     */
    public static function lockUserForOtp($user): void
    {
        if (!$user) {
            return;
        }

        $userId = (int) (method_exists($user, 'getKey') ? $user->getKey() : ($user->id ?? 0));
        if ($userId <= 0) {
            return;
        }

        $now = now();

        if (DB::connection()->getDriverName() === 'mysql') {
            // Satu statement atomik: insert jika belum ada, atau update row yang
            // sudah ada. ON DUPLICATE KEY UPDATE langsung mengambil exclusive
            // lock sehingga aman dari deadlock INSERT IGNORE + SELECT FOR UPDATE.
            $table = DB::connection()->getQueryGrammar()->wrapTable('hr_pms_access_otp_locks');

            DB::statement(
                "INSERT INTO {$table} (`user_id`, `created_at`, `updated_at`) VALUES (?, ?, ?)"
                . " ON DUPLICATE KEY UPDATE `updated_at` = VALUES(`updated_at`)",
                [$userId, $now->format('Y-m-d H:i:s'), $now->format('Y-m-d H:i:s')]
            );

            return;
        }

        // Fallback untuk driver non-MySQL (misalnya SQLite saat test).
        DB::table('hr_pms_access_otp_locks')->insertOrIgnore([
            'user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('hr_pms_access_otp_locks')
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Reuse request aktif; buat baru hanya jika tidak ada request aktif.
     */
    public static function findOrCreateActiveRequest(Request $request, $user, ?string $intendedUri): HrPmsAccessOtp
    {
        // Prioritaskan request yang sudah punya kode valid agar user tidak
        // dipindah-pindah halaman dan kode dari email tetap bisa dipakai.
        $existing = self::activeSentRequestForUser($user)
            ?: self::activeRequestForUser($user);

        if ($existing) {
            if ($intendedUri !== null && $existing->intended_uri !== $intendedUri) {
                $existing->forceFill(['intended_uri' => $intendedUri])->save();
            }

            return $existing;
        }

        return DB::transaction(function () use ($request, $user, $intendedUri) {
            self::lockUserForOtp($user);

            $existing = self::activeSentRequestForUser($user, true)
                ?: self::activeRequestForUser($user, true);

            if ($existing) {
                if ($intendedUri !== null && $existing->intended_uri !== $intendedUri) {
                    $existing->forceFill(['intended_uri' => $intendedUri])->save();
                }

                return $existing;
            }

            return self::createRequest($request, $user, $intendedUri);
        });
    }

    public static function actionLabel(string $action): string
    {
        $labels = [
            'otp-request' => 'Permintaan OTP akses PMS',
            'otp-send' => 'Mengirim OTP akses PMS',
            'otp-verify' => 'Verifikasi OTP akses PMS berhasil',
            'otp-fail' => 'Pengiriman/verifikasi OTP akses PMS gagal',
        ];

        return $labels[$action] ?? 'Aktivitas OTP akses PMS';
    }

    public static function audit(Request $request, string $action, array $meta = [], ?int $status = 200): void
    {
        try {
            $user = Auth::user();
            $route = $request->route();
            $routeAction = $route ? (string) $route->getActionName() : null;

            HrPmsActivityLog::record([
                'user_id' => $user ? $user->id : null,
                'user_nik' => $user && !empty($user->nik) ? substr(trim((string) $user->nik), 0, 50) : null,
                'user_username' => $user && !empty($user->username) ? substr(trim((string) $user->username), 0, 100) : null,
                'user_name' => $user && !empty($user->name) ? substr(trim((string) $user->name), 0, 150) : null,
                'module' => 'pms-access-otp',
                'action' => $action,
                'action_label' => self::actionLabel($action),
                'route_action' => $routeAction ? substr($routeAction, 0, 150) : null,
                'http_method' => substr(strtoupper((string) $request->getMethod()), 0, 10),
                'uri' => substr((string) $request->getRequestUri(), 0, 500),
                'target_type' => 'otp',
                'target_id' => null,
                'target_nik' => $user && !empty($user->nik) ? substr(trim((string) $user->nik), 0, 50) : null,
                'meta' => $meta ?: null,
                'status_code' => $status,
                'ip_address' => substr((string) $request->ip(), 0, 45),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Header agar halaman/AJAX/download PMS tidak bisa dibuka dari cache
     * setelah grant 20 menit kedaluwarsa.
     */
    public static function applyNoStore($response)
    {
        if ($response && isset($response->headers)) {
            $response->headers->set('Cache-Control', 'private, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
