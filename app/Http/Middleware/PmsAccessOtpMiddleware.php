<?php

namespace App\Http\Middleware;

use App\Support\PmsAccessOtpService;
use App\Support\PmsOtpAccess;
use Closure;
use Illuminate\Support\Facades\Auth;

class PmsAccessOtpMiddleware
{
    /**
     * Gate OTP untuk area PMS yang sensitif (IPP & PK).
     *
     * Parameter route:
     *   pms.access_otp:enforce,scope
     *   pms.access_otp:deferred,scope
     *
     * - enforce  : tanpa grant -> 401 JSON / redirect challenge / 401 POST.
     * - deferred : halaman tetap terbuka; modal OTP disiapkan untuk AJAX 401.
     *
     * Scope menentukan role yang wajib OTP (lihat PmsOtpAccess).
     */
    public function handle($request, Closure $next, $mode = 'enforce', $scope = null)
    {
        if (!PmsAccessOtpService::enabled()) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        if ($scope !== null && !PmsOtpAccess::requiredFor((string) $scope, $user, $request)) {
            return $next($request);
        }

        $grant = PmsAccessOtpService::grant();

        if ($grant !== null) {
            $request->attributes->set('pms_access_otp', true);
            $request->attributes->set('pms_access_otp_expires_at', (int) ($grant['expires_at'] ?? 0));

            if ($mode === 'deferred') {
                $request->attributes->set('pms_access_otp_context', true);
                $request->attributes->set('pms_access_otp_deferred', true);
            }

            return PmsAccessOtpService::applyNoStore($next($request));
        }

        if ($mode === 'deferred') {
            $request->attributes->set('pms_access_otp_context', true);
            $request->attributes->set('pms_access_otp_deferred', true);
            $request->attributes->set('pms_access_otp_expires_at', 0);

            return PmsAccessOtpService::applyNoStore($next($request));
        }

        if ($request->expectsJson() || $request->ajax()) {
            return PmsAccessOtpService::applyNoStore(response()->json([
                'otp_required' => true,
                'message' => 'Sesi akses PMS belum ada atau sudah berakhir. Verifikasi OTP untuk melanjutkan.',
                'ttl_minutes' => PmsAccessOtpService::ttlMinutes(),
                'server_time' => now()->timestamp,
            ], 401));
        }

        $method = strtoupper((string) $request->getMethod());
        if (!in_array($method, ['GET', 'HEAD'], true)) {
            return PmsAccessOtpService::applyNoStore(response(
                'Sesi akses PMS sudah berakhir. Silakan muat ulang halaman dan verifikasi OTP.',
                401
            ));
        }

        $intendedUri = PmsAccessOtpService::normalizeIntendedUri($request->getRequestUri(), $request);
        $otp = PmsAccessOtpService::findOrCreateActiveRequest($request, $user, $intendedUri);

        return PmsAccessOtpService::applyNoStore(
            redirect()->route('hr.pms-access-otp.show', ['requestId' => $otp->request_id])
        );
    }
}
