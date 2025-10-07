<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_plant';
    protected $guarded = [];
}
