<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UploadMasukHariLiburModel extends Model
{
    //
    public $timestamps = false;

    protected $table = 'hr_masuk_hari_libur_karyawan';

    protected $guarded = [];
}
