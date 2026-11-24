<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkDiskusi extends Model
{
    protected $table = 'hr_pk_diskusi';

    protected $fillable = [
        'hr_pk_id',
        'nik_yang_dibahas',
        'hr_pk_detail_id',
        'nik_pengaju',
        'nama_pengaju',
        'pesan',
        'jawaban',
        'nik_penjawab',
        'nama_penjawab',
        'status',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];
}
