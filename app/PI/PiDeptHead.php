<?php

namespace App\PI;

use Illuminate\Database\Eloquent\Model;

class PiDeptHead extends Model
{
    protected $table = "pi_dept_head"; // menghubungkan ke dalam tabel pi_dept_head

    protected $guarded = [];

    public $primaryKey = 'id_dept_head';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function karyawan()
    {
        return $this->hasMany('App\PI\PiKaryawan');
    }
}
