<?php

namespace App\PI;

use Illuminate\Database\Eloquent\Model;

class PiJenis extends Model
{
    protected $table = "pi_jenis"; // menghubungkan ke dalam tabel pi_jenis

    protected $guarded = [];

    public $primaryKey = 'id_jenis';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function karyawan()
    {
        return $this->hasMany(PiKaryawan::class, 'id_pengajuan');
    }
}
