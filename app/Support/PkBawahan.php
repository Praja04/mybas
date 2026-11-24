<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class PkBawahan
{
    public static function resolveForTarget($pkId, $nik): array
    {
        $nik = trim((string) $nik);
        if ($nik === '') {
            return [null, null];
        }

        $pkOto = DB::table('hr_pk_otorisasi')
            ->where('nik_yang_bersangkutan', $nik)
            ->first(['jumlah_bawahan']);
        if ($pkOto && $pkOto->jumlah_bawahan !== null) {
            return [(int) $pkOto->jumlah_bawahan, 'pk-otorisasi'];
        }

        $oto = DB::table('hr_ipp_otorisasi')
            ->where('nik_yang_bersangkutan', $nik)
            ->orderByDesc('id')
            ->first(['jumlah_bawahan']);
        if ($oto && $oto->jumlah_bawahan !== null) {
            return [(int) $oto->jumlah_bawahan, 'ipp'];
        }

        return [null, null];
    }

    public static function isNoLeadership($pkId, $nik, $group): bool
    {
        if (!in_array(trim((string) $group), ['2-3', '4-5'], true)) {
            return false;
        }

        return self::resolveForTarget($pkId, $nik)[0] === 0;
    }

    public static function isLeadershipDetail($detail): bool
    {
        $cat = strtoupper(trim((string) ($detail->category ?? '')));
        $sec = strtoupper(trim((string) ($detail->section ?? '')));

        return str_contains($cat, 'LEADERSHIP') || str_contains($sec, 'LEADERSHIP');
    }

    public static function bulkResolve(array $pkIds, array $niks): array
    {
        $pkIds = array_values(array_unique(array_map('intval', $pkIds)));
        $niks = array_values(array_unique(array_filter(array_map('trim', $niks), fn ($n) => $n !== '')));

        $map = [];
        if (empty($pkIds) || empty($niks)) {
            return $map;
        }

        $missingNiks = $niks;

        $pkOtos = DB::table('hr_pk_otorisasi')
            ->whereIn('nik_yang_bersangkutan', $missingNiks)
            ->get(['nik_yang_bersangkutan', 'jumlah_bawahan']);
        $pkOtoMap = [];
        foreach ($pkOtos as $o) {
            if ($o->jumlah_bawahan !== null) {
                $pkOtoMap[$o->nik_yang_bersangkutan] = (int) $o->jumlah_bawahan;
            }
        }

        foreach ($pkIds as $pkId) {
            foreach ($missingNiks as $nik) {
                if (array_key_exists($pkId . '|' . $nik, $map)) {
                    continue;
                }
                if (array_key_exists($nik, $pkOtoMap)) {
                    $map[$pkId . '|' . $nik] = $pkOtoMap[$nik];
                }
            }
        }

        $stillMissing = [];
        foreach ($pkIds as $pkId) {
            foreach ($missingNiks as $nik) {
                if (!array_key_exists($pkId . '|' . $nik, $map)) {
                    $stillMissing[$nik] = true;
                }
            }
        }
        $stillMissingNiks = array_keys($stillMissing);
        if (empty($stillMissingNiks)) {
            return $map;
        }

        $otos = DB::table('hr_ipp_otorisasi as o')
            ->joinSub(
                DB::table('hr_ipp_otorisasi')
                    ->selectRaw('MAX(id) as max_id')
                    ->whereIn('nik_yang_bersangkutan', $stillMissingNiks)
                    ->groupBy('nik_yang_bersangkutan'),
                'm',
                'm.max_id',
                '=',
                'o.id'
            )
            ->get(['o.nik_yang_bersangkutan', 'o.jumlah_bawahan']);

        $otoMap = [];
        foreach ($otos as $o) {
            if ($o->jumlah_bawahan !== null) {
                $otoMap[$o->nik_yang_bersangkutan] = (int) $o->jumlah_bawahan;
            }
        }

        foreach ($pkIds as $pkId) {
            foreach ($stillMissingNiks as $nik) {
                if (array_key_exists($pkId . '|' . $nik, $map)) {
                    continue;
                }
                if (array_key_exists($nik, $otoMap)) {
                    $map[$pkId . '|' . $nik] = $otoMap[$nik];
                }
            }
        }

        return $map;
    }
}
