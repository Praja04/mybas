<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class JenisProject extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'name','status'
    ];
}
