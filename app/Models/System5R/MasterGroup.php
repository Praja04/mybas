<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterGroup extends Model
{
    protected $table = '5r_master_group';
    protected $guarded = [];
    public $primaryKey = 'id_group';
    public $ketType = 'string';
    public $incrementing = false;

    public function pertanyaan()
    {
        // With order by jenis
        return $this->hasMany(MasterPertanyaan::class, 'id_group', 'id_group')->orderBy('jenis', 'asc');
    }

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'id_department', 'id_department');
    }
}
