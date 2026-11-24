<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppDiskusi extends Model
{
    protected $table = 'hr_ipp_diskusi';

    protected $fillable = [
        'ipp_otorisasi_id',
        'ipp_detail_id',
        'tahapan',
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

    public function otorisasi()
    {
        return $this->belongsTo(HrIppOtorisasi::class, 'ipp_otorisasi_id');
    }

    public function detail()
    {
        return $this->belongsTo(HrIppDetail::class, 'ipp_detail_id');
    }
}
