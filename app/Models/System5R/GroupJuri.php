<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class GroupJuri extends Model
{
    protected $table = '5r_master_group_juri';
    protected $guarded = [];
    public $primaryKey = 'id_group_juri';
    public $ketType = 'string';
    public $incrementing = false;

    public function anggota()
    {
        return $this->hasMany(GroupJuriAnggota::class, 'id_group_juri');
    }

    public function groupJuriDepartment()
    {
        return $this->belongsTo(MasterGroupJuriDepartment::class, 'id_group_juri');
    }
}
