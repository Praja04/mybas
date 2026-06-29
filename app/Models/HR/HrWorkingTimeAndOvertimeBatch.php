<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrWorkingTimeAndOvertimeBatch extends Model
{
    protected $table = 'hr_workingtimeandovertime_batches';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'batch_id', 'filename', 'send_by_username',
        'total_data', 'created_count', 'updated_count', 'confirmed_count',
        'confirm_status', 'confirm_total', 'confirm_processed', 'confirm_error',
        'status', 'file_path', 'error_message',
    ];
}
