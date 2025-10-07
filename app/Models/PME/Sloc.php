<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class Sloc extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_sloc';
    protected $guarded = [];
}
