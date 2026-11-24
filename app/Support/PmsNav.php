<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Konteks navigasi PMS (IPP & PK) untuk menu header dan subnav.
 *
 * Peran ditentukan dari hr_pms_employee_snapshot periode terbaru:
 * - nik                  => peserta (self)
 * - manager_nik          => manager
 * - associate_manager_nik => Associate Manager
 *
 * Satu sumber kebenaran agar menu header, subnav, dan gate halaman
 * memakai aturan akses yang sama. Hasil di-cache per request supaya
 * header yang tampil di semua halaman tidak menambah query berulang.
 */
class PmsNav
{
    public const ADMIN_PERMISSION = 'hr_upload_file_template_ipp';

    /** @var array<string, array<string, mixed>> */
    private static $cache = [];

    /**
     * @param  array<int, string>|null  $permissions
     * @return array<string, mixed>
     */
    public static function context(?string $nik = null, ?array $permissions = null): array
    {
        $user = Auth::user();

        if ($user === null) {
            return self::empty();
        }

        if ($permissions === null) {
            $permissions = method_exists($user, 'getAllPermissionCodenames')
                ? (array) $user->getAllPermissionCodenames()
                : [];
        }

        $isAdmin = in_array(self::ADMIN_PERMISSION, $permissions, true);

        $nik = trim((string) ($nik ?? ($user->nik ?? '')));
        if ($nik === '') {
            // Admin HR tetap mendapat menu admin walau akunnya tidak punya NIK.
            $context = self::empty();
            $context['admin'] = $isAdmin;
            $context['show'] = $isAdmin;

            return $context;
        }

        $cacheKey = $nik . '|' . (string) $user->getAuthIdentifier();
        if (array_key_exists($cacheKey, self::$cache)) {
            return self::$cache[$cacheKey];
        }

        $latestPeriode = DB::table('hr_pms_employee_snapshot')->max('periode');

        $snapshotRows = DB::table('hr_pms_employee_snapshot')
            ->when($latestPeriode !== null && $latestPeriode !== '', function ($q) use ($latestPeriode) {
                $q->where('periode', $latestPeriode);
            })
            ->where(function ($q) use ($nik) {
                $q->where('nik', $nik)
                    ->orWhere('manager_nik', $nik)
                    ->orWhere('associate_manager_nik', $nik);
            })
            ->get(['nik', 'level_group', 'manager_nik', 'associate_manager_nik']);

        $isParticipant = false;
        $isManager = false;
        $isAm = false;

        foreach ($snapshotRows as $row) {
            if ((string) $row->nik === $nik
                && in_array((string) $row->level_group, PmsSelfPageGate::ALLOWED_GROUPS, true)) {
                $isParticipant = true;
            }
            if ((string) $row->manager_nik === $nik) {
                $isManager = true;
            }
            if ((string) $row->associate_manager_nik === $nik) {
                $isAm = true;
            }
        }

        $ippApprover = DB::table('hr_ipp_otorisasi')
            ->where(function ($q) use ($nik) {
                $q->where('nik_manager', $nik)
                    ->orWhere('nik_associate_manager', $nik);
            })
            ->exists();

        $self = PmsSelfPageGate::allowsWithContext($nik, $isAdmin, $isParticipant);

        $context = [
            'nik' => $nik,
            'admin' => $isAdmin,
            'self' => $self,
            'approver' => $ippApprover || $isManager || $isAm,
            'am' => $isAm,
            'manager' => $isManager,
            'leader' => $isManager || $isAm,
            'show' => $isAdmin
                || $self
                || $ippApprover
                || $isManager
                || $isAm,
        ];

        self::$cache[$cacheKey] = $context;

        return $context;
    }

    /**
     * @return array<string, mixed>
     */
    private static function empty(): array
    {
        return [
            'nik' => '',
            'admin' => false,
            'self' => false,
            'approver' => false,
            'am' => false,
            'manager' => false,
            'leader' => false,
            'show' => false,
        ];
    }
}
