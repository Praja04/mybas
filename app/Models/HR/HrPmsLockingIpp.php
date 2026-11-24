<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsLockingIpp extends Model
{
    protected $table = 'hr_pms_locking_ipp';

    /**
     * Kolom aktual IPP yang bisa dikunci: nama logis => kolom tabel.
     */
    public const COLUMNS = [
        'departemen' => 'lock_aktual_departemen',
        'karyawan' => 'lock_aktual_karyawan',
        'atasan' => 'lock_aktual_atasan',
        'batalkan_approval' => 'lock_batalkan_approval',
    ];

    public const LABELS = [
        'departemen' => 'Input Nilai IPP Departemen (Self Assesment)',
        'karyawan' => 'Input Nilai IPP Karyawan (Self Assesment)',
        'atasan' => 'Input Nilai IPP Atasan (Leader Assesment)',
        'batalkan_approval' => 'Batalkan Approval',
    ];

    protected $fillable = [
        'tahapan',
        'lock_aktual_departemen',
        'lock_aktual_karyawan',
        'lock_aktual_atasan',
        'lock_batalkan_approval',
        'locked_by',
        'locked_at',
        'updated_by',
    ];

    protected $casts = [
        'lock_aktual_departemen' => 'boolean',
        'lock_aktual_karyawan' => 'boolean',
        'lock_aktual_atasan' => 'boolean',
        'lock_batalkan_approval' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public static function columnKey(string $column): ?string
    {
        return self::COLUMNS[strtolower(trim($column))] ?? null;
    }

    public static function label(string $column): string
    {
        $column = strtolower(trim($column));

        return self::LABELS[$column] ?? ('Aktual (' . $column . ')');
    }

    /**
     * Kolom aktual tahapan ini sedang dikunci.
     */
    public static function isLocked(?string $tahapan, string $column): bool
    {
        $tahapan = trim((string) $tahapan);
        $key = self::columnKey($column);
        if ($tahapan === '' || $key === null) {
            return false;
        }

        return (bool) static::where('tahapan', $tahapan)->value($key);
    }

    /**
     * Peta tahapan terkunci per kolom (hanya tahapan yang punya lock aktif).
     *
     * @param  array<int, string>  $tahapanList
     * @return array<string, array{departemen: bool, karyawan: bool, atasan: bool, batalkan_approval: bool}>
     */
    public static function lockedMap(array $tahapanList): array
    {
        $tahapanList = array_values(array_unique(array_filter(array_map(function ($t) {
            return trim((string) $t);
        }, $tahapanList), function ($t) {
            return $t !== '';
        })));

        if (!$tahapanList) {
            return [];
        }

        $out = [];
        foreach (static::whereIn('tahapan', $tahapanList)->get() as $row) {
            $flags = [];
            foreach (self::COLUMNS as $name => $key) {
                $flags[$name] = (bool) $row->{$key};
            }
            if (in_array(true, $flags, true)) {
                $out[(string) $row->tahapan] = $flags;
            }
        }

        return $out;
    }

    /**
     * Status locking satu tahapan (per kolom + meta pengubah).
     */
    public static function stateFor(?string $tahapan): array
    {
        $tahapan = trim((string) $tahapan);
        $row = $tahapan !== '' ? static::where('tahapan', $tahapan)->first() : null;

        $columns = [];
        $anyLocked = false;
        foreach (self::COLUMNS as $name => $key) {
            $isLocked = $row ? (bool) $row->{$key} : false;
            $anyLocked = $anyLocked || $isLocked;
            $columns[$name] = [
                'label' => self::LABELS[$name],
                'is_locked' => $isLocked,
            ];
        }

        return [
            'tahapan' => $tahapan,
            'columns' => $columns,
            'any_locked' => $anyLocked,
            'locked_by' => $row ? $row->locked_by : null,
            'locked_at' => ($row && $row->locked_at) ? $row->locked_at->format('d/m/Y H:i') : null,
            'updated_by' => $row ? $row->updated_by : null,
            'updated_at' => ($row && $row->updated_at) ? $row->updated_at->format('d/m/Y H:i') : null,
        ];
    }

    /**
     * Banding nilai aktual lama vs baru (angka dan/atau teks label).
     */
    public static function valueChanged($oldNumber, $oldVarchar, $newNumber, $newVarchar): bool
    {
        $oldNumEmpty = $oldNumber === null || $oldNumber === '';
        $newNumEmpty = $newNumber === null || $newNumber === '';
        if ($oldNumEmpty && $newNumEmpty) {
            $numbersSame = true;
        } elseif ($oldNumEmpty || $newNumEmpty) {
            $numbersSame = false;
        } else {
            $numbersSame = abs((float) $oldNumber - (float) $newNumber) < 0.0000000001;
        }

        return !$numbersSame || trim((string) $oldVarchar) !== trim((string) $newVarchar);
    }
}
