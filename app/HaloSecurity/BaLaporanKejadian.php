<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaLaporanKejadian extends Model
{
    use SoftDeletes;

    protected $table = 'ba_laporan_kejadian';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public $primaryKey = 'lk_id';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function dokumentasis()
    {
        return $this->hasMany('App\HaloSecurity\DokumentasiKejadian', 'lk_id', 'lk_id');
    }

    public function faktas()
    {
        return $this->hasMany('App\HaloSecurity\FaktaKejadian', 'lk_id', 'lk_id');
    }

    public function saksis()
    {
        return $this->hasMany('App\HaloSecurity\SaksiKejadian', 'lk_id', 'lk_id');
    }
}
