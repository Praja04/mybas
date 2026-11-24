<?php

namespace App\Support;

/**
 * Skor PK (Penilaian Karya) dari komponen per grup level.
 *
 * Komponen: WINGS CORE VALUE, BEHAVIOR (termasuk legacy SIKAP KERJA),
 * LEADERSHIP COMPETENCY. COMPLIANCE tidak dihitung.
 *
 * score():
 * - group 2-3: rata-rata WINGS + BEHAVIOR + LEADERSHIP (LEADERSHIP hanya bila
 *   punya bawahan — komponennya null saat noLeadership);
 * - group lain: 70% WINGS + 30% LEADERSHIP; tanpa bawahan (2-3 / 4-5) PK = WINGS.
 *
 * $role 'leader' (default) memakai nilai Leader, 'self' memakai nilai Karyawan.
 */
class PkComponentScore
{
    private const PK_WINGS_WEIGHT = 0.7;
    private const PK_LEAD_WEIGHT = 0.3;

    /**
     * Rata-rata per komponen Penilaian Karya (2 desimal), dari nilai Self & Leader.
     * Bila $noLeadership (2-3 / 4-5 tanpa bawahan), komponen LEADERSHIP dikosongkan.
     *
     * @return array{wings_self: float|null, wings_leader: float|null, behavior_self: float|null,
     *               behavior_leader: float|null, leadership_self: float|null, leadership_leader: float|null}
     */
    public static function components(iterable $nilaiRows, iterable $details, string $group, bool $noLeadership): array
    {
        $buckets = [
            'wings_self' => [], 'wings_leader' => [],
            'behavior_self' => [], 'behavior_leader' => [],
            'leadership_self' => [], 'leadership_leader' => [],
        ];

        foreach ($nilaiRows as $row) {
            $detail = $details[$row->hr_pk_detail_id] ?? null;
            if (!$detail) {
                continue;
            }
            if (!in_array($detail->level_group, ['all', $group], true)) {
                continue;
            }

            $cat = strtoupper(trim((string) $detail->category));
            $sec = strtoupper(trim((string) $detail->section));

            $key = null;
            if (str_contains($cat, 'CORE VALUE') || str_contains($sec, 'CORE VALUE')) {
                $key = 'wings';
            } elseif (str_contains($cat, 'LEADERSHIP') || str_contains($sec, 'LEADERSHIP')) {
                $key = 'leadership';
            } elseif (
                str_contains($cat, 'BEHAVIOR') || str_contains($cat, 'SIKAP KERJA')
                || str_contains($sec, 'BEHAVIOR') || str_contains($sec, 'SIKAP KERJA')
            ) {
                $key = 'behavior';
            }
            if ($key === null) {
                continue;
            }

            if ($row->nilai_self !== null && $row->nilai_self !== '') {
                $buckets[$key . '_self'][] = (int) $row->nilai_self;
            }
            if ($row->nilai_leader !== null && $row->nilai_leader !== '') {
                $buckets[$key . '_leader'][] = (int) $row->nilai_leader;
            }
        }

        $out = [];
        foreach ($buckets as $name => $values) {
            $out[$name] = $values ? round(array_sum($values) / count($values), 2) : null;
        }

        if ($noLeadership) {
            $out['leadership_self'] = null;
            $out['leadership_leader'] = null;
        }

        return $out;
    }

    /**
     * @param  array{wings_leader?: float|null, behavior_leader?: float|null, leadership_leader?: float|null,
     *               wings_self?: float|null, behavior_self?: float|null, leadership_self?: float|null}  $components
     */
    public static function score(array $components, string $group, string $role = 'leader'): ?float
    {
        $wingsAvg = $components['wings_' . $role] ?? null;
        $leadAvg = $components['leadership_' . $role] ?? null;

        if ($group === '2-3') {
            $parts = array_values(array_filter(
                [$wingsAvg, $components['behavior_' . $role] ?? null, $leadAvg],
                fn ($v) => $v !== null
            ));

            return $parts ? round(array_sum($parts) / count($parts), 2) : null;
        }

        if ($wingsAvg !== null && $leadAvg !== null) {
            return round(($wingsAvg * self::PK_WINGS_WEIGHT) + ($leadAvg * self::PK_LEAD_WEIGHT), 2);
        }

        return $wingsAvg ?? $leadAvg;
    }
}
