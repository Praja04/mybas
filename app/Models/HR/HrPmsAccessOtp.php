<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPmsAccessOtp extends Model
{
    protected $table = 'hr_pms_access_otp';

    protected $fillable = [
        'request_id',
        'user_id',
        'nik',
        'intended_uri',
        'purpose',
        'code_hash',
        'request_expires_at',
        'expires_at',
        'attempts',
        'send_count',
        'last_sent_at',
        'consumed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'attempts' => 'integer',
        'send_count' => 'integer',
        'request_expires_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];
}
