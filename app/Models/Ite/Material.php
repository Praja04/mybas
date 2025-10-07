<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'ite';
    protected $fillable = [
    	'mid','name','status', 'stock', 'piece'
    ];
}
