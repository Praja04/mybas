<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterGroupJuriDepartment extends Model
{
    protected $table = '5r_master_group_juri_department';
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'id_department');
    }

    public function group()
    {
        return $this->belongsTo(GroupJuri::class, 'id_group_juri');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'id_periode');
    }
}
