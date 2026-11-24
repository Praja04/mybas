<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsLockingPk extends Model
{
    protected $table = 'hr_pms_locking_pk';

    /**
     * Kolom PK yang bisa dikunci: nama logis => kolom tabel.
     */
    public const COLUMNS = [
        'input_karyawan' => 'lock_input_karyawan',
        'input_atasan' => 'lock_input_atasan',
        'approval_am' => 'lock_approval_am',
        'approval_manager' => 'lock_approval_manager',
        'unapproval' => 'lock_unapproval',
    ];

    public const LABELS = [
        'input_karyawan' => 'Input Nilai Karyawan (Self Assessment)',
        'input_atasan' => 'Input Nilai Atasan (Leader Assessment)',
        'approval_am' => 'Approval Associate Manager',
        'approval_manager' => 'Approval Manager',
        'unapproval' => 'Unapproval (Batalkan Approval)',
    ];

    protected $fillable = [
        'periode',
        'lock_input_karyawan',
        'lock_input_atasan',
        'lock_approval_am',
        'lock_approval_manager',
        'lock_unapproval',
        'locked_by',
        'locked_at',
        'updated_by',
    ];

    protected $casts = [
        'lock_input_karyawan' => 'boolean',
        'lock_input_atasan' => 'boolean',
        'lock_approval_am' => 'boolean',
        'lock_approval_manager' => 'boolean',
        'lock_unapproval' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public static function columnKey(string $column): ?string
    {
        return self::COLUMNS[strtolower(trim($column))] ?? null;
    }

    public static function label(string $column): string
    {
        $column = strtolower(trim($column));

        return self::LABELS[$column] ?? ('Locking (' . $column . ')');
    }

    /**
     * Kolom PK periode ini sedang dikunci.
     */
    public static function isLocked(?string $periode, string $column): bool
    {
        $periode = trim((string) $periode);
        $key = self::columnKey($column);
        if ($periode === '' || $key === null) {
            return false;
        }

        return (bool) static::where('periode', $periode)->value($key);
    }

    /**
     * Peta periode terkunci per kolom (hanya periode yang punya lock aktif).
     *
     * @param  array<int, string>  $periodeList
     * @return array<string, array<string, bool>>
     */
    public static function lockedMap(array $periodeList): array
    {
        $periodeList = array_values(array_unique(array_filter(array_map(function ($p) {
            return trim((string) $p);
        }, $periodeList), function ($p) {
            return $p !== '';
        })));

        if (!$periodeList) {
            return [];
        }

        $out = [];
        foreach (static::whereIn('periode', $periodeList)->get() as $row) {
            $flags = [];
            foreach (self::COLUMNS as $name => $key) {
                $flags[$name] = (bool) $row->{$key};
            }
            if (in_array(true, $flags, true)) {
                $out[(string) $row->periode] = $flags;
            }
        }

        return $out;
    }

    /**
     * Flag locking satu periode (nama logis => bool, semua kolom).
     */
    public static function flagsFor(?string $periode): array
    {
        $periode = trim((string) $periode);
        $row = $periode !== '' ? static::where('periode', $periode)->first() : null;

        $flags = [];
        foreach (self::COLUMNS as $name => $key) {
            $flags[$name] = $row ? (bool) $row->{$key} : false;
        }

        return $flags;
    }

    /**
     * Status locking satu periode (per kolom + meta pengubah).
     */
    public static function stateFor(?string $periode): array
    {
        $periode = trim((string) $periode);
        $row = $periode !== '' ? static::where('periode', $periode)->first() : null;

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
            'periode' => $periode,
            'columns' => $columns,
            'any_locked' => $anyLocked,
            'locked_by' => $row ? $row->locked_by : null,
            'locked_at' => ($row && $row->locked_at) ? $row->locked_at->format('d/m/Y H:i') : null,
            'updated_by' => $row ? $row->updated_by : null,
            'updated_at' => ($row && $row->updated_at) ? $row->updated_at->format('d/m/Y H:i') : null,
        ];
    }
}
