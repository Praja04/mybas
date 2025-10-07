<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PKWKaryawan extends Model
{
    protected $table = 'pkw_karyawan';
    protected $guarded = [];

    public function divisi()
    {
    	return $this->belongsTo('App\PKWDivisi', 'id_divisi');
    }
    
    public function bagian()
    {
    	return $this->belongsTo('App\PKWBagian', 'id_bagian');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\PKWJabatan', 'id_jabatan');
    }

    public function group()
    {
        return $this->belongsTo('App\PKWGroup', 'id_group');
    }

    public function admin()
    {
        return $this->belongsTo('App\PKWAdmin', 'id_admin');
    }

    public function pkw()
    {
        return $this->hasOne('App\PKW', 'id_karyawan');
    }

    public function ktp_desa()
    {
        return $this->belongsTo('App\Models\Village', 'alamat_ktp_desa');
    }

    public function ktp_kecamatan()
    {
        return $this->belongsTo('App\Models\District', 'alamat_ktp_kecamatan');
    }

    public function ktp_kota()
    {
        return $this->belongsTo('App\Models\Regency', 'alamat_ktp_kota');
    }

    public function ktp_provinsi()
    {
        return $this->belongsTo('App\Models\Province', 'alamat_ktp_provinsi');
    }

    public function sekarang_desa()
    {
        return $this->belongsTo('App\Models\Village', 'alamat_sekarang_desa');
    }

    public function sekarang_kecamatan()
    {
        return $this->belongsTo('App\Models\District', 'alamat_sekarang_kecamatan');
    }

    public function sekarang_kota()
    {
        return $this->belongsTo('App\Models\Regency', 'alamat_sekarang_kota');
    }

    public function sekarang_provinsi()
    {
        return $this->belongsTo('App\Models\Province', 'alamat_sekarang_provinsi');
    }

    public function anak_anak()
    {
        return $this->hasMany('App\PKWAnakAnak', 'id_karyawan');
    }
}
