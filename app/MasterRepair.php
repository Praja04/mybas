<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterRepair extends Model
{
    public $timestamps = false;

    protected $table = 'master_repair_mesin_s2';

    protected $guarded = [];
}
