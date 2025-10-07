<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaSopKaryawan extends Model
{
    use SoftDeletes;

    protected $table = 'ba_sop_karyawan';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nama',
        'nik',
        'jabatan',
        'jenis_kelamin',
        'shift',
        'nama_pembuat',
        'jabatan_pembuat',
        'nama_area',
        'barang',
    ];
}
