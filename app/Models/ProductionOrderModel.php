<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderModel extends Model
{
    protected $table = 'logging_machine_no_prod';
    protected $guarded = [];
    public $timestamps = false;
    
}
