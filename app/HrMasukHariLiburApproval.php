<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrMasukHariLiburApproval extends Model
{
    protected $table = 'hr_masuk_hari_libur_approval';
    protected $fillable = [
        'dept',
        'nik_admin',
        'nama_admin',
        'nik_approval',
        'nama_approval',
        'status'
    ];
}
