<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = '5r_periode_penilaian';
    protected $guarded = [];
    public $primaryKey = 'id_periode';
    public $ketType = 'string';
    public $incrementing = false;

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public function temuan()
    {
        return $this->hasMany(Temuan::class, 'id_periode', 'id_periode');
    }
}
