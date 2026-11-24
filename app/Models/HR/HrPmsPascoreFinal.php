<?php

namespace App\Models\HR;

use App\Support\PmsPaScaleFinalCipher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HrPmsPascoreFinal extends Model
{
    protected $table = 'hr_pms_pascore_final';

    protected $fillable = [
        'periode',
        'nik',
        'pa_scale_final',
        'updated_by',
    ];

    /**
     * Nilai pa_scale_final di database selalu tersimpan terenkripsi.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getPaScaleFinalAttribute($value)
    {
        return PmsPaScaleFinalCipher::decrypt($value);
    }

    /**
     * @param  mixed  $value
     */
    public function setPaScaleFinalAttribute($value): void
    {
        $this->attributes['pa_scale_final'] = PmsPaScaleFinalCipher::encrypt($value);
    }

    /**
     * Nilai PA Scale Calibrated untuk kombinasi periode + NIK, key: "periode|nik".
     *
     * @return Collection<string, HrPmsPascoreFinal>
     */
    public static function mapFor(array $periodes, array $niks): Collection
    {
        $periodes = array_values(array_unique(array_filter(array_map(function ($p) {
            return trim((string) $p);
        }, $periodes), function ($p) {
            return $p !== '';
        })));

        $niks = array_values(array_unique(array_filter(array_map(function ($n) {
            return trim((string) $n);
        }, $niks), function ($n) {
            return $n !== '';
        })));

        if (!$periodes || !$niks) {
            return collect();
        }

        return static::whereIn('periode', $periodes)
            ->whereIn('nik', $niks)
            ->get()
            ->keyBy(function ($row) {
                return $row->periode . '|' . $row->nik;
            });
    }
}
