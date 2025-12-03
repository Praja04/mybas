<?php

namespace App\Models\PosSecurity\Absensi;

use Illuminate\Database\Eloquent\Model;

class GateAccessLog extends Model
{
    protected $table = 'ga_gate_access_logs_security';
    protected $guarded = [];

    protected $casts = [
        'waktu' => 'datetime',
    ];
}
