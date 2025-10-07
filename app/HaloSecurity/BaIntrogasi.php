<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaIntrogasi extends Model
{
    use SoftDeletes;

    protected $table = 'ba_introgasi';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public $primaryKey = 'bai_id';
    public $keyType = 'string';
    public $autoIncrement = 'false';

    public function dokumentasibais()
    {
        return $this->hasMany('App\HaloSecurity\DokumentasiIntrogasi', 'bai_id', 'bai_id');
    }

    public function baiitems()
    {
        return $this->hasMany('App\HaloSecurity\BaiItems', 'bai_id', 'bai_id');
    }
}
