<?php

namespace App\PI;

use Illuminate\Database\Eloquent\Model;

class PiPic extends Model
{
    protected $table = "pi_pic"; // menghubungkan ke dalam tabel pi_pic

    protected $guarded = [];

    public $primaryKey = 'id_pic';
    public $keyType = 'string';
    public $autoIncrement = 'false';
}
