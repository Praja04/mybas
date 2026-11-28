<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpPelanggaranDate extends Model
{
    protected $table = 'sp_pelanggaran_dates';

    protected $fillable = [
        'sp_pelanggaran_id',
        'tanggal',
        'keterangan',
    ];

    public function spPelanggaran()
    {
        return $this->belongsTo(SpPelanggaran::class, 'sp_pelanggaran_id');
    }
}
