<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class PkAmMapping
{
    public static function isAssociateManager(string $nik): bool
    {
        $nik = trim($nik);
        if ($nik === '') {
            return false;
        }

        $inPkMapping = DB::table('hr_pk_associate_manager_mapping')
            ->where('nik_associate_manager', $nik)
            ->exists();

        if ($inPkMapping) {
            return true;
        }

        return DB::table('hr_ipp_otorisasi')
            ->where('nik_associate_manager', $nik)
            ->exists();
    }

    public static function isAssociateManagerOrLevel(string $nik, int $minLevel): bool
    {
        return self::isAssociateManager($nik) || self::levelOf($nik) > $minLevel;
    }

    public static function levelOf(string $nik): int
    {
        $nik = trim($nik);
        if ($nik === '') {
            return 0;
        }

        $level = DB::table('hr_master_employee')->where('NIK', $nik)->value('Level');

        return $level === null ? 0 : (int) $level;
    }
}
