<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPk extends Model
{
    protected $table = 'hr_pk';

    protected $fillable = [
        'periode',
        'file_name',
        'send_by_username',
    ];

    /**
     * Periode PK yang berupa satu tahun saja (contoh: 2026).
     * Rentang seperti "2026 - 2027" tidak ikut.
     */
    public static function singleYearPeriodes(): array
    {
        return static::query()
            ->whereNotNull('periode')
            ->distinct()
            ->pluck('periode')
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => preg_match('/^\d{4}$/', $v) === 1)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }

    public function details()
    {
        return $this->hasMany(HrPkDetail::class, 'hr_pk_id')
            ->orderByRaw("COALESCE(CAST(SUBSTRING_INDEX(no_urut, '.', 1) AS UNSIGNED), 0) ASC, COALESCE(CAST(SUBSTRING_INDEX(no_urut, '.', -1) AS UNSIGNED), 0) ASC, id ASC");
    }

    public function detailsForLevel(string $levelGroup)
    {
        return $this->details()
            ->where(function ($q) use ($levelGroup) {
                $q->where('level_group', 'all')->orWhere('level_group', $levelGroup);
            });
    }
}