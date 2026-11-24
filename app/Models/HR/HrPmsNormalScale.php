<?php

namespace App\Models\HR;

use App\Support\PaScaleNormalizer;
use Illuminate\Database\Eloquent\Model;

class HrPmsNormalScale extends Model
{
    public const GROUP_23 = '2-3';
    public const GROUP_47 = '4-7';
    public const GROUPS = [self::GROUP_23, self::GROUP_47];

    protected $table = 'hr_pms_normal_scale';

    protected $fillable = [
        'periode',
        'level_group',
        'grade_a',
        'grade_b',
        'grade_c',
        'grade_d',
        'grade_e',
        'minimum_peserta',
        'updated_by',
    ];

    protected $casts = [
        'grade_a' => 'float',
        'grade_b' => 'float',
        'grade_c' => 'float',
        'grade_d' => 'float',
        'grade_e' => 'float',
        'minimum_peserta' => 'integer',
    ];

    /**
     * Fraksi grade keyed E..A (urutan sama dengan PaScaleNormalizer::DISTRIBUTION).
     */
    public function distribution(): array
    {
        return [
            'E' => ((float) $this->grade_e) / 100,
            'D' => ((float) $this->grade_d) / 100,
            'C' => ((float) $this->grade_c) / 100,
            'B' => ((float) $this->grade_b) / 100,
            'A' => ((float) $this->grade_a) / 100,
        ];
    }

    public function percentages(): array
    {
        return [
            'E' => round((float) $this->grade_e, 2),
            'D' => round((float) $this->grade_d, 2),
            'C' => round((float) $this->grade_c, 2),
            'B' => round((float) $this->grade_b, 2),
            'A' => round((float) $this->grade_a, 2),
        ];
    }

    /**
     * Kelompok normalisasi: level 2-3 tersendiri, level 4-5 & 6-7 digabung.
     */
    public static function bucketForGroup(?string $group): string
    {
        return trim((string) $group) === self::GROUP_23 ? self::GROUP_23 : self::GROUP_47;
    }

    public static function groupLabel(?string $group): string
    {
        return static::bucketForGroup($group) === self::GROUP_23
            ? 'Level 2-3'
            : 'Level 4-7 (4-5 & 6-7)';
    }

    public static function distributionFor(?string $periode, ?string $levelGroup = null): ?array
    {
        $periode = trim((string) $periode);
        if ($periode === '') {
            return null;
        }

        $levelGroup = static::bucketForGroup($levelGroup);
        $row = static::where('periode', $periode)->where('level_group', $levelGroup)->first();
        if (!$row) {
            return null;
        }

        $dist = $row->distribution();

        return PaScaleNormalizer::isValidDistribution($dist) ? $dist : null;
    }

    /**
     * Minimum peserta kurva normal per periode + group (fallback ke default).
     */
    public static function minimumFor(?string $periode, ?string $levelGroup = null): int
    {
        $periode = trim((string) $periode);
        if ($periode === '') {
            return PaScaleNormalizer::MIN_N;
        }

        $levelGroup = static::bucketForGroup($levelGroup);
        $value = static::where('periode', $periode)
            ->where('level_group', $levelGroup)
            ->value('minimum_peserta');

        return $value !== null ? max(1, (int) $value) : PaScaleNormalizer::MIN_N;
    }

    /**
     * @param  array<int, string>  $periodes
     * @return array<string, int>
     */
    public static function minimumMap(array $periodes, ?string $levelGroup = null): array
    {
        $periodes = array_values(array_filter(array_map(function ($p) {
            return trim((string) $p);
        }, $periodes), function ($p) {
            return $p !== '';
        }));

        if (!$periodes) {
            return [];
        }

        $levelGroup = static::bucketForGroup($levelGroup);
        $out = [];
        foreach (static::whereIn('periode', $periodes)->where('level_group', $levelGroup)->get() as $row) {
            if ($row->minimum_peserta !== null) {
                $out[(string) $row->periode] = max(1, (int) $row->minimum_peserta);
            }
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $periodes
     * @return array<string, array<string, float>>
     */
    public static function distributionMap(array $periodes, ?string $levelGroup = null): array
    {
        $periodes = array_values(array_filter(array_map(function ($p) {
            return trim((string) $p);
        }, $periodes), function ($p) {
            return $p !== '';
        }));

        if (!$periodes) {
            return [];
        }

        $levelGroup = static::bucketForGroup($levelGroup);
        $out = [];
        foreach (static::whereIn('periode', $periodes)->where('level_group', $levelGroup)->get() as $row) {
            $dist = $row->distribution();
            if (PaScaleNormalizer::isValidDistribution($dist)) {
                $out[(string) $row->periode] = $dist;
            }
        }

        return $out;
    }
}
