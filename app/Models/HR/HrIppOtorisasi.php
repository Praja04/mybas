<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppOtorisasi extends Model
{
    protected $table = 'hr_ipp_otorisasi';

    protected $fillable = [
        'ipp_id',
        'nik_yang_bersangkutan',
        'nama_yang_bersangkutan',
        'nik_manager',
        'nama_manager',
        'nik_associate_manager',
        'nama_associate_manager',
        'send_by_username',
    ];

    public function ipp()
    {
        return $this->belongsTo(HrIpp::class, 'ipp_id');
    }
}
