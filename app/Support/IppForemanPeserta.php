<?php

namespace App\Support;

use App\Models\HR\HrPmsEmployeeSnapshot;

/**
 * Peserta IPP rubrik Level 2-3: dibaca langsung dari hr_pms_employee_snapshot
 * (periode + level_group 2-3), tanpa baris hr_ipp_otorisasi.
 */
class IppForemanPeserta
{
    /**
     * @return \Illuminate\Support\Collection<int, HrPmsEmployeeSnapshot>
     */
    public static function participants(string $periode, ?string $nikAtasan = null)
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode($periode);
        if ($periode === '') {
            return collect();
        }

        $q = HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where('level_group', '2-3');

        if ($nikAtasan !== null && trim($nikAtasan) !== '') {
            $nikAtasan = trim($nikAtasan);
            $q->where(function ($qq) use ($nikAtasan) {
                $qq->where('manager_nik', $nikAtasan)
                    ->orWhere('associate_manager_nik', $nikAtasan);
            });
        }

        return $q->orderBy('nik')->get();
    }

    public static function snapshot(string $periode, string $nik): ?HrPmsEmployeeSnapshot
    {
        $periode = PmsEmployeeSnapshot::normalizePeriode($periode);
        $nik = trim($nik);
        if ($periode === '' || $nik === '') {
            return null;
        }

        return HrPmsEmployeeSnapshot::where('periode', $periode)
            ->where('nik', $nik)
            ->where('level_group', '2-3')
            ->first();
    }

    /**
     * Peran user (nik) pada peserta rubrik: 'manager' | 'associate_manager' | null.
     */
    public static function role($snap, string $nik): ?string
    {
        if (!$snap) {
            return null;
        }

        $nik = trim($nik);
        if ($nik === '') {
            return null;
        }

        if (trim((string) $snap->manager_nik) === $nik) {
            return 'manager';
        }

        $am = trim((string) $snap->associate_manager_nik);
        if ($am !== '' && $am === $nik) {
            return 'associate_manager';
        }

        return null;
    }

    /**
     * AM berbeda dengan Manager (dua tahap approval).
     */
    public static function hasDistinctAm($snap): bool
    {
        $am = trim((string) ($snap->associate_manager_nik ?? ''));
        if ($am === '') {
            return false;
        }

        return $am !== trim((string) ($snap->manager_nik ?? ''));
    }

    /**
     * Bentuk payload peserta yang kompatibel dengan payload otorisasi KPI.
     */
    public static function payload($snap, ?string $role): array
    {
        return [
            'id' => (string) $snap->nik,
            'nik_yang_bersangkutan' => (string) $snap->nik,
            'nama_yang_bersangkutan' => (string) $snap->nama,
            'nik_manager' => $snap->manager_nik,
            'nama_manager' => $snap->manager_nama,
            'nik_associate_manager' => $snap->associate_manager_nik,
            'nama_associate_manager' => $snap->associate_manager_nama,
            'my_role' => $role,
            'jumlah_bawahan' => $snap->jumlah_bawahan !== null ? (int) $snap->jumlah_bawahan : null,
        ];
    }

    /**
     * Objek peserta untuk kebutuhan generator PDF (pmsHeaderData dsb).
     */
    public static function otoObject($snap): \stdClass
    {
        $o = new \stdClass();
        $o->id = null;
        $o->nik_yang_bersangkutan = (string) $snap->nik;
        $o->nama_yang_bersangkutan = (string) $snap->nama;
        $o->nik_manager = $snap->manager_nik;
        $o->nama_manager = $snap->manager_nama;
        $o->nik_associate_manager = $snap->associate_manager_nik;
        $o->nama_associate_manager = $snap->associate_manager_nama;
        $o->jumlah_bawahan = $snap->jumlah_bawahan;

        return $o;
    }
}
