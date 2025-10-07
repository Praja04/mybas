<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;

class SecurityUserGA extends Model
{
    protected $table = "security_user_ga"; // menghubungkan ke dalam tabel security_user_ga

    protected $guarded = [];

    public $primaryKey = 'user_id';
    public $keyType = 'string';
    public $autoIncrement = 'false';
}
