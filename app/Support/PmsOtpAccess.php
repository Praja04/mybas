<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmsOtpAccess
{
    public const ADMIN_PERMISSION = 'hr_upload_file_template_ipp';

    /**
     * Admin HR PMS (pemilik permission template IPP).
     */
    public static function isAdmin($user): bool
    {
        if (!$user || !method_exists($user, 'getAllPermissionCodenames')) {
            return false;
        }

        return in_array(
            self::ADMIN_PERMISSION,
            (array) $user->getAllPermissionCodenames(),
            true
        );
    }

    /**
     * Manager PMS berdasarkan snapshot periode. Associate Manager sengaja
     * tidak termasuk.
     */
    public static function isManager($user): bool
    {
        $nik = strtoupper(trim((string) ($user->nik ?? '')));
        if ($nik === '') {
            return false;
        }

        static $cache = [];
        $cacheKey = 'pms|' . (string) ($user->id ?? '') . '|' . $nik;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        return $cache[$cacheKey] = DB::table('hr_pms_employee_snapshot')
            ->where('manager_nik', $nik)
            ->exists();
    }

    /**
     * Manager IPP: bisa dari snapshot atau otorisasi IPP periode lama.
     */
    public static function isIppManager($user): bool
    {
        $nik = strtoupper(trim((string) ($user->nik ?? '')));
        if ($nik === '') {
            return false;
        }

        static $cache = [];
        $cacheKey = 'ipp|' . (string) ($user->id ?? '') . '|' . $nik;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $isManager = DB::table('hr_pms_employee_snapshot')
            ->where('manager_nik', $nik)
            ->exists();

        if (!$isManager) {
            $isManager = DB::table('hr_ipp_otorisasi')
                ->where('nik_manager', $nik)
                ->exists();
        }

        return $cache[$cacheKey] = $isManager;
    }

    /**
     * Manager PK: snapshot, otorisasi PK, atau otorisasi IPP lama.
     */
    public static function isPkManager($user): bool
    {
        $nik = strtoupper(trim((string) ($user->nik ?? '')));
        if ($nik === '') {
            return false;
        }

        static $cache = [];
        $cacheKey = 'pk|' . (string) ($user->id ?? '') . '|' . $nik;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $isManager = DB::table('hr_pms_employee_snapshot')
            ->where('manager_nik', $nik)
            ->exists();

        if (!$isManager) {
            $isManager = DB::table('hr_pk_otorisasi')
                ->where('nik_manager', $nik)
                ->exists();
        }

        if (!$isManager) {
            $isManager = DB::table('hr_ipp_otorisasi')
                ->where('nik_manager', $nik)
                ->exists();
        }

        return $cache[$cacheKey] = $isManager;
    }

    /**
     * Associate Manager IPP (hanya dipakai untuk export yang memuat kolom
     * achievement; modal biasa tetap tidak kena OTP).
     */
    public static function isIppAssociateManager($user): bool
    {
        $nik = strtoupper(trim((string) ($user->nik ?? '')));
        if ($nik === '') {
            return false;
        }

        static $cache = [];
        $cacheKey = 'ipp-am|' . (string) ($user->id ?? '') . '|' . $nik;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $isAm = DB::table('hr_pms_employee_snapshot')
            ->where('associate_manager_nik', $nik)
            ->exists();

        if (!$isAm) {
            $isAm = DB::table('hr_ipp_otorisasi')
                ->where('nik_associate_manager', $nik)
                ->exists();
        }

        return $cache[$cacheKey] = $isAm;
    }

    /**
     * Apakah request ini wajib OTP untuk user tersebut.
     */
    public static function requiredFor(string $scope, $user, Request $request): bool
    {
        $admin = self::isAdmin($user);
        $manager = self::isManager($user);

        switch ($scope) {
            case 'admin':
                return $admin;

            case 'ipp_user_approval':
            case 'ipp_user_approval_page':
                return self::isIppManager($user);

            case 'ipp_user_approval_export':
                return self::isIppManager($user) || self::isIppAssociateManager($user);

            case 'pms_total_scoring':
                return $admin || $manager;

            case 'pk_assessment':
            case 'pk_assessment_page':
            case 'pk_assessment_export':
                return self::isPkManager($user);

            case 'pk_assessment_leader':
                $mode = strtolower(trim((string) $request->input('mode', 'self')));

                return $mode === 'leader' && self::isPkManager($user);

            default:
                // Scope tidak dikenal: perlakukan seperti gate OTP lama.
                return true;
        }
    }
}
