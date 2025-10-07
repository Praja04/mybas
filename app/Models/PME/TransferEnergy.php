<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class TransferEnergy extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_transfer_energy';
    protected $guarded = [];
}
