<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrMasterEmployeeBatch extends Model
{
    protected $table = 'hr_master_employee_batches';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'batch_id', 'filename', 'send_by_username',
        'total_data', 'created_count', 'updated_count', 'confirmed_count',
        'status', 'file_path', 'error_message',
    ];
}
