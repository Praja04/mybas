<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrMasterEmployee extends Model
{
    protected $table = 'hr_master_employee';
    protected $primaryKey = 'NIK';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'NIK', 'Nama', 'Tgl Lahir', 'Tgl Masuk', 'Departmen', 'Sub Departmen',
        'Section', 'Tipe Karyawan', 'Jabatan', 'Jenis Kelamin', 'Work Status',
        'Status Nikah', 'Aktif', 'Valid From', 'send_by_username',
    ];
}
