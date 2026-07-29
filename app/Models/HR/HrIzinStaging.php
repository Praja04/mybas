<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIzinStaging extends Model
{
    protected $table = 'hr_izin_staging';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nik', 'nama', 'dept', 'section', 'tgl',
        'no_spi', 'kode_ijin', 'keterangan', 'send_by_username',
        'batch_id', 'status',
    ];
}
