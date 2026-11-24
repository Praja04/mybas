<?php

namespace App\Support;

use App\Models\HR\HrIpp;
use App\Models\HR\HrPmsEmployeeSnapshot;
use Illuminate\Support\Facades\DB;

/**
 * Resolusi peserta otorisasi IPP dari snapshot periode.
 * Dipakai form otorisasi IPP (template KPI); peserta level_group 2-3 ditolak
 * karena dikelola melalui IPP rubrik Level 2-3.
 */
class IppOtorisasiResolver
{
    /**
     * Resolusi satu peserta + manager/AM dari snapshot (fallback NIK manual).
     *
     * @return array{nik: string, name: string, level: int, nik_manager: ?string, nama_manager: ?string, nik_associate_manager: ?string, nama_associate_manager: ?string}|null
     */
    public static function resolve(HrIpp $ipp, array $validated, ?string &$error = null): ?array
    {
        $error = null;

        $periode = PmsEmployeeSnapshot::normalizePeriode((string) $ipp->periode);
        $nikInput = trim((string) ($validated['nik_yang_bersangkutan'] ?? ''));

        if ($periode === '') {
            $error = 'Periode IPP tidak valid.';
            return null;
        }

        if (!HrPmsEmployeeSnapshot::where('periode', $periode)->exists()) {
            $error = 'Peserta periode ' . $periode . ' belum di-capture. Silakan buat/upload template PK periode tersebut agar peserta tercapture.';
            return null;
        }

        $snap = PmsEmployeeSnapshot::attributesFor($periode, $nikInput);
        if (!$snap) {
            $error = "Peserta '{$nikInput}' tidak terdaftar pada snapshot periode {$periode}.";
            return null;
        }

        $group = trim((string) $snap->level_group);
        if ($group === '') {
            $group = (string) PmsEmployeeSnapshot::groupOf((int) $snap->level);
        }

        if (!$ipp->isRubric() && !in_array($group, ['4-5', '6-7'], true)) {
            $error = "Peserta '{$nikInput}' tidak dapat didaftarkan pada IPP KPI (level_group "
                . ($group !== '' ? $group : '-') . '). Peserta level_group 2-3 dikelola melalui Template IPP Rubrik Level 2-3.';
            return null;
        }

        $level = (int) $snap->level;
        $needsAm = ($level >= 3 && $level <= 5);

        $nikManager = trim((string) $snap->manager_nik);
        $namaManager = trim((string) $snap->manager_nama);
        $nikAssociate = $needsAm ? trim((string) $snap->associate_manager_nik) : '';
        $namaAssociate = $needsAm ? trim((string) $snap->associate_manager_nama) : '';

        if ($nikManager === '') {
            $manualManager = trim((string) ($validated['nik_manager'] ?? ''));
            if ($manualManager !== '') {
                $mgr = self::resolveUserByNik($manualManager);
                if (!$mgr) {
                    $error = "NIK manager '{$manualManager}' tidak ditemukan di tabel users.";
                    return null;
                }
                $nikManager = $mgr['nik'];
                $namaManager = $mgr['name'];
            }
        }

        if ($nikAssociate === '') {
            $manualAm = $needsAm ? trim((string) ($validated['nik_associate_manager'] ?? '')) : '';
            if ($manualAm !== '') {
                $asm = self::resolveUserByNik($manualAm);
                if (!$asm) {
                    $error = "NIK associate manager '{$manualAm}' tidak ditemukan di tabel users.";
                    return null;
                }
                $nikAssociate = $asm['nik'];
                $namaAssociate = $asm['name'];
            }
        }

        return [
            'nik' => (string) $snap->nik,
            'name' => (string) $snap->nama,
            'level' => $level,
            'nik_manager' => $nikManager !== '' ? $nikManager : null,
            'nama_manager' => $namaManager !== '' ? $namaManager : null,
            'nik_associate_manager' => $nikAssociate !== '' ? $nikAssociate : null,
            'nama_associate_manager' => $namaAssociate !== '' ? $namaAssociate : null,
        ];
    }

    /**
     * @return array{nik: string, name: string}|null
     */
    public static function resolveUserByNik(string $nik): ?array
    {
        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }

        $user = DB::table('users')
            ->where('nik', $nik)
            ->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->where('status', '1')
            ->first(['nik', 'name']);

        if (!$user) {
            return null;
        }

        return [
            'nik' => $user->nik,
            'name' => $user->name,
        ];
    }
}
