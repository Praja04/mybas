<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsControlNotifikasi extends Model
{
    protected $table = 'hr_pms_control_notifikasi';

    protected $fillable = [
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Notifikasi email PMS (IPP & PK) aktif. Default aktif bila baris belum ada.
     */
    public static function isActive(): bool
    {
        $value = static::query()->orderBy('id')->value('is_active');

        return $value === null ? true : (bool) $value;
    }

    public static function state(): array
    {
        $row = static::query()->orderBy('id')->first();

        return [
            'is_active' => $row ? (bool) $row->is_active : true,
            'updated_by' => $row ? $row->updated_by : null,
            'updated_at' => ($row && $row->updated_at) ? $row->updated_at->format('d/m/Y H:i') : null,
        ];
    }

    public static function setActive(bool $active, ?string $updatedBy = null): self
    {
        $row = static::query()->orderBy('id')->first();
        if (!$row) {
            $row = new static();
        }

        $row->is_active = $active;
        $row->updated_by = $updatedBy;
        $row->save();

        return $row;
    }
}
