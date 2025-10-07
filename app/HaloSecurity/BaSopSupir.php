<?php

namespace App\HaloSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaSopSupir extends Model
{
    use SoftDeletes;
    
    protected $table = 'ba_sop_supir';
    protected $dates = ['deleted_at'];
    
    protected $guarded = [];
}
