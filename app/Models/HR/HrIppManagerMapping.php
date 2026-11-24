<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIppManagerMapping extends Model
{
    protected $table = 'hr_ipp_manager_mapping';

    protected $fillable = [
        'sub_departmen',
        'nik_manager',
        'nama_manager',
        'created_by',
    ];
}