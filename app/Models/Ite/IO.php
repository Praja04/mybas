<?php

namespace App\Models\Ite;

use Illuminate\Database\Eloquent\Model;

class IO extends Model
{
    protected $connection = 'ite';
    protected $table = 'orders_io';
    protected $guarded = [];
}
