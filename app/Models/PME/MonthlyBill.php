<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class MonthlyBill extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_monthly_bill';
    protected $guarded = [];
}
