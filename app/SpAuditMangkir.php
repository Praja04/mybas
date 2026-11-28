<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpAuditMangkir extends Model
{
    protected $table = 'sp_audit_mangkirs';

    protected $fillable = [
        'periode',
        'periode_label',
        'employee_id',
        'nik',
        'nama',
        'department',
        'section',
        'tanggal',
        'mangkir_ke',
        'kode_absensi',
        'filename',
        'created_by_user_id',
    ];

    public function employee()
    {
        return $this->belongsTo(HrKaryawan::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
