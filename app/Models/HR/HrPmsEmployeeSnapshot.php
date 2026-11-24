<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsEmployeeSnapshot extends Model
{
    protected $table = 'hr_pms_employee_snapshot';

    protected $fillable = [
        'periode',
        'nik',
        'nama',
        'jabatan',
        'departemen',
        'sub_departemen',
        'section',
        'level',
        'level_group',
        'manager_nik',
        'manager_nama',
        'associate_manager_nik',
        'associate_manager_nama',
        'jumlah_bawahan',
        'pendidikan',
        'tanggal_join',
        'tanggal_lahir',
        'usia',
        'source',
        'captured_at',
    ];

    protected $casts = [
        'jumlah_bawahan' => 'integer',
        'usia' => 'integer',
        'tanggal_join' => 'date',
        'tanggal_lahir' => 'date',
        'captured_at' => 'datetime',
    ];
}
