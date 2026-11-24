<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrPkAssociateManagerMapping extends Model
{
    protected $table = 'hr_pk_associate_manager_mapping';

    protected $fillable = [
        'nik',
        'nama',
        'nik_associate_manager',
        'nama_associate_manager',
        'created_by',
    ];
}
