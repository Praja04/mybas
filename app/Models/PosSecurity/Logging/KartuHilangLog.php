<?php

namespace App\Models\PosSecurity\Logging;

use Illuminate\Database\Eloquent\Model;

class KartuHilangLog extends Model
{

    protected $table = 'ga_lgtk_kartu_hilang_log';
    protected $guarded = [];

    protected $fillable = [
        'no_kartu',
        'no_identitas',
        'nama',
        'alasan_hilang',
        'tanggal_lapor',
        'dilaporkan_oleh',
        'aktif',
    ];

    protected $casts = [
        'tanggal_lapor' => 'datetime',
        'aktif' => 'boolean',
    ];
}
