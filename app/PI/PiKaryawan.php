<?php

namespace App\PI;

use App\Models\HR\Karyawan;
use Illuminate\Database\Eloquent\Model;

class PiKaryawan extends Model
{
    protected $table = "pi_pengajuan_izin"; // menghubungkan ke dalam tabel pi_pengajuan_izin

    protected $guarded = [];

    public $primaryKey = 'id_pengajuan';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function hr_karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik_karyawan', 'nik');
    }

    public function jenis()
    {
        return $this->belongsTo(PiJenis::class, 'id_jenis');
    }

    public function gm()
    {
        return $this->belongsTo('App\PI\PiGm', 'id_gm');
    }

    public function dh()
    {
        return $this->belongsTo(PiDeptHead::class, 'id_dept_head');
    }

    public function pic()
    {
        return $this->belongsTo('App\PI\PiPic', 'id_pic');
    }
}
