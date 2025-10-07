<?php

namespace App\Models\PME;

use Illuminate\Database\Eloquent\Model;

class TransferEnergyTrash extends Model
{
    protected $connection = 'pme';
    protected $table = 'pme_transfer_energy_trash';
    protected $guarded = [];
}
