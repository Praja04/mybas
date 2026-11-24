<?php

namespace App\Support;

class IppTahapan
{
    /**
     * Daftar tahapan dari periode IPP, contoh: "2026 - 2027" => MID 2026, END 2026, MID 2027, END 2027.
     *
     * @return array<int, string>
     */
    public static function build(?string $periode): array
    {
        $tahapan = [];
        if (!$periode) {
            return $tahapan;
        }

        $years = [];
        if (preg_match('/(\d{4})\s*[-–—]\s*(\d{4})/', $periode, $m)) {
            $start = (int) $m[1];
            $end = (int) $m[2];
            if ($end >= $start && $end - $start <= 50) {
                for ($y = $start; $y <= $end; $y++) {
                    $years[] = $y;
                }
            }
        }
        if (empty($years)) {
            preg_match_all('/\d{4}/', $periode, $m);
            $years = array_map('intval', array_values(array_unique($m[0] ?? [])));
        }
        sort($years);

        foreach ($years as $year) {
            $tahapan[] = 'MID ' . $year;
            $tahapan[] = 'END ' . $year;
        }

        return $tahapan;
    }

    public static function label(string $tahapan): string
    {
        $parts = explode(' ', trim($tahapan));
        if (count($parts) === 2) {
            $kind = strtoupper($parts[0]);
            if ($kind === 'MID') {
                return 'Mid Year ' . $parts[1];
            }
            if ($kind === 'END') {
                return 'End Year ' . $parts[1];
            }
        }

        return $tahapan;
    }

    public static function isKnown(string $tahapan): bool
    {
        return (bool) preg_match('/^(MID|END) \d{4}$/', trim($tahapan));
    }
}
