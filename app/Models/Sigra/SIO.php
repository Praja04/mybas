<?php

namespace App\Models\Sigra;

use App\Department;
use Illuminate\Database\Eloquent\Model;

class SIO extends Model
{
    protected $table = 'sigra_sio';
    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }
}
