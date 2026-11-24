<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppBukti extends Model
{
    protected $table = 'hr_ipp_bukti';

    protected $fillable = [
        'ipp_otorisasi_id',
        'ipp_detail_id',
        'tahapan',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function otorisasi()
    {
        return $this->belongsTo(HrIppOtorisasi::class, 'ipp_otorisasi_id');
    }

    public function detail()
    {
        return $this->belongsTo(HrIppDetail::class, 'ipp_detail_id');
    }
}
