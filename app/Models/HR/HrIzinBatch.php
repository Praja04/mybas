<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIzinBatch extends Model
{
    protected $table = 'hr_izin_batches';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'batch_id', 'filename', 'send_by_username',
        'total_data', 'created_count', 'updated_count', 'confirmed_count',
        'overlap_count', 'deleted_overtime_count', 'deleted_orphan_count',
        'confirm_status', 'confirm_total', 'confirm_processed', 'confirm_error',
        'overlap_log', 'status', 'file_path', 'error_message',
    ];
}
