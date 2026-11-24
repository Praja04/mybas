<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Gate halaman self-service IPP/PK (/hr/ipp-aktual & /hr/pk-assessment).
 *
 * Peserta ditentukan dari hr_pms_employee_snapshot periode terbaru:
 * - baris dengan nik = NIK user dan level_group 4-5 / 6-7 => peserta (self).
 * - baris manager_nik / associate_manager_nik => peran penilai.
 *   AM yang juga punya baris peserta tetap boleh self karena dia juga dinilai
 *   oleh manager-nya, dan tetap mendapat menu Leader/AM.
 *
 * Group 2-3 tetap dikunci (penilaian 2-3 diisi Atasan/Leader Assessment).
 * Tidak ada fallback ke hr_master_employee.Level.
 *
 * Pengecualian:
 * - Admin HR (permission hr_upload_file_template_ipp)
 * - Peserta terdaftar otorisasi IPP/PK (nik_yang_bersangkutan);
 *   khusus PK, kategori 'foreman' (peserta 2-3) TIDAK dianggap whitelist.
 */
class PmsSelfPageGate
{
    public const ADMIN_PERMISSION = 'hr_upload_file_template_ipp';

    public const ALLOWED_GROUPS = ['4-5', '6-7'];

    public static function allows(?string $nik = null): bool
    {
        $nik = trim((string) ($nik ?? (Auth::user()->nik ?? '')));
        if ($nik === '') {
            return false;
        }

        return self::allowsWithContext(
            $nik,
            self::isAdmin(),
            self::isParticipant($nik)
        );
    }

    /**
     * Versi allows() yang menerima hasil pengecekan admin / peserta snapshot
     * yang sudah dihitung pemanggil, supaya query tidak diulang.
     */
    public static function allowsWithContext(string $nik, bool $isAdmin, bool $isParticipant): bool
    {
        $nik = trim($nik);
        if ($nik === '') {
            return false;
        }

        if ($isAdmin || $isParticipant) {
            return true;
        }

        return self::isOtorisasiPeserta($nik);
    }

    public static function isAdmin(): bool
    {
        $user = Auth::user();
        if ($user === null || !method_exists($user, 'getAllPermissionCodenames')) {
            return false;
        }

        return in_array(self::ADMIN_PERMISSION, (array) $user->getAllPermissionCodenames(), true);
    }

    /**
     * Peserta self = ada snapshot periode terbaru dengan nik = NIK
     * dan level_group 4-5 / 6-7.
     */
    public static function isParticipant(string $nik, ?string $periode = null): bool
    {
        $nik = trim($nik);
        if ($nik === '') {
            return false;
        }

        $query = DB::table('hr_pms_employee_snapshot')
            ->where('nik', $nik)
            ->whereIn('level_group', self::ALLOWED_GROUPS);

        if ($periode === null) {
            $latest = DB::table('hr_pms_employee_snapshot')->max('periode');
            if ($latest !== null && $latest !== '') {
                $query->where('periode', $latest);
            }
        } else {
            $query->where('periode', $periode);
        }

        return $query->exists();
    }

    private static function isOtorisasiPeserta(string $nik): bool
    {
        // hr_pk_otorisasi kategori 'foreman' = peserta PK group 2-3 (diblokir),
        // jadi hanya kategori selain itu (mis. 'spv') yang dianggap whitelist.
        return DB::table('hr_ipp_otorisasi')->where('nik_yang_bersangkutan', $nik)->exists()
            || DB::table('hr_pk_otorisasi')
                ->where('nik_yang_bersangkutan', $nik)
                ->where('kategori', '!=', 'foreman')
                ->exists();
    }
}
