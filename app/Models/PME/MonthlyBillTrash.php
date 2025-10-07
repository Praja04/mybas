<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class MonthlyBillTrash extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_monthly_bill_trash';
    protected $guarded = [];
}
