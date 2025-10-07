<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = '5r_jadwal_penilaian';
    protected $guarded = [];
    public $primaryKey = 'id_jadwal';
    public $ketType = 'string';
    public $incrementing = false;

    public function periodes()
    {
        return $this->hasMany(Periode::class, 'id_jadwal', 'id_jadwal');
    }
}
