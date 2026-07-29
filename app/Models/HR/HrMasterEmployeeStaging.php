<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrMasterEmployeeStaging extends Model
{
    protected $table = 'hr_master_employee_staging';
    protected $primaryKey = 'NIK';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'NIK', 'PWS', 'Level', 'Nama', 'Tgl Lahir', 'Tgl Masuk', 'Departmen', 'Sub Departmen',
        'Section', 'Tipe Karyawan', 'Jabatan', 'Jenis Kelamin', 'Work Status',
        'Status Nikah', 'Aktif', 'Valid From', 'send_by_username',
        'batch_id', 'status',
    ];
}
