<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsActivityLog extends Model
{
    public const MAX_ROWS = 100000;

    private const PRUNE_BATCH = 5000;

    protected $table = 'hr_pms_activity_log';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'user_nik',
        'user_username',
        'user_name',
        'module',
        'action',
        'action_label',
        'route_action',
        'http_method',
        'uri',
        'target_type',
        'target_id',
        'target_nik',
        'meta',
        'status_code',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'target_id' => 'integer',
        'status_code' => 'integer',
        'meta' => 'array',
    ];

    public static function record(array $attributes): ?self
    {
        try {
            $attributes['created_at'] = $attributes['created_at'] ?? now();

            $log = static::query()->create($attributes);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        // Prune dijalankan lewat scheduler, bukan di setiap request.
        // count(*) + delete di akhir request bisa memperlambat response
        // setelah email/DB sudah sukses.
        return $log;
    }

    /**
     * Batasi jumlah log dengan menghapus baris paling lama berdasarkan created_at.
     */
    public static function prune(?int $maxRows = null): int
    {
        $maxRows = max(1, $maxRows ?? self::MAX_ROWS);

        $excess = static::query()->count() - $maxRows;
        if ($excess <= 0) {
            return 0;
        }

        $deleted = 0;

        while ($excess > 0) {
            $batch = min($excess, self::PRUNE_BATCH);

            $affected = static::query()
                ->orderBy('created_at')
                ->orderBy('id')
                ->limit($batch)
                ->delete();

            $deleted += $affected;
            $excess -= $affected;

            if ($affected < $batch) {
                break;
            }
        }

        return $deleted;
    }
}
