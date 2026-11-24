<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkOtorisasi extends Model
{
    protected $table = 'hr_pk_otorisasi';

    protected $fillable = [
        'nik_yang_bersangkutan',
        'nama_yang_bersangkutan',
        'nik_manager',
        'nama_manager',
        'nik_associate_manager',
        'nama_associate_manager',
        'send_by_username',
        'jumlah_bawahan',
        'kategori',
    ];

    protected $casts = [
        'jumlah_bawahan' => 'integer',
    ];
}
