<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsLockingPascale extends Model
{
    protected $table = 'hr_pms_locking_pascale';

    protected $fillable = [
        'periode',
        'is_locked',
        'locked_by',
        'locked_at',
        'updated_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    /**
     * PA Scale Calibrated periode ini sedang dikunci.
     */
    public static function isLocked(?string $periode): bool
    {
        $periode = trim((string) $periode);
        if ($periode === '') {
            return false;
        }

        return (bool) static::where('periode', $periode)->value('is_locked');
    }

    /**
     * Peta periode terkunci, key: periode.
     *
     * @param  array<int, string>  $periodes
     * @return array<string, bool>
     */
    public static function lockedMap(array $periodes): array
    {
        $periodes = array_values(array_unique(array_filter(array_map(function ($p) {
            return trim((string) $p);
        }, $periodes), function ($p) {
            return $p !== '';
        })));

        if (!$periodes) {
            return [];
        }

        $out = [];
        foreach (static::whereIn('periode', $periodes)->where('is_locked', true)->pluck('periode') as $p) {
            $out[(string) $p] = true;
        }

        return $out;
    }

    /**
     * Status locking satu periode untuk ditampilkan di section.
     */
    public static function stateFor(?string $periode): array
    {
        $periode = trim((string) $periode);
        $row = $periode !== '' ? static::where('periode', $periode)->first() : null;

        return [
            'is_locked' => $row ? (bool) $row->is_locked : false,
            'locked_by' => $row ? $row->locked_by : null,
            'locked_at' => ($row && $row->locked_at) ? $row->locked_at->format('d/m/Y H:i') : null,
            'updated_by' => $row ? $row->updated_by : null,
            'updated_at' => ($row && $row->updated_at) ? $row->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
