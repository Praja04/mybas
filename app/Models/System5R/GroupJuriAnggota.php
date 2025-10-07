<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class GroupJuriAnggota extends Model
{
    protected $table = '5r_master_group_juri_anggota';
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(GroupJuri::class, 'id_group_juri');
    }

    public function department()
    {
        return $this->belongsTo(MasterGroupJuriDepartment::class, 'id_group_juri');
    }
}
