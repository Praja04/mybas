<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppApprovalNote extends Model
{
    protected $table = 'hr_ipp_approval_note';

    protected $fillable = [
        'ipp_otorisasi_id',
        'tahapan',
        'role',
        'note',
    ];

    public function otorisasi()
    {
        return $this->belongsTo(HrIppOtorisasi::class, 'ipp_otorisasi_id');
    }
}
