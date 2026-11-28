<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrWorkingTimeAndOvertime extends Model
{
    protected $table = 'hr_workingtimeandovertime';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nik', 'nama', 'dept', 'section', 'tgl_in',
        'jam_spkl', 'jam_hovt', 'no_spkl', 'send_by_username',
    ];
}
