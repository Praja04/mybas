<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkPeserta extends Model
{
    protected $table = 'hr_pk_peserta';

    protected $fillable = [
        'hr_pk_id',
        'nik',
        'jumlah_bawahan',
    ];

    protected $casts = [
        'jumlah_bawahan' => 'integer',
    ];
}
