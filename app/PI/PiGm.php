<?php

namespace App\PI;

use Illuminate\Database\Eloquent\Model;

class PiGm extends Model
{
    protected $table = "pi_gm"; // menghubungkan ke dalam tabel pi_gm

    protected $guarded = [];

    public $primaryKey = 'id_gm';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function karyawan()
    {
        return $this->hasMany('App\PI\PiKaryawan');
    }
}
