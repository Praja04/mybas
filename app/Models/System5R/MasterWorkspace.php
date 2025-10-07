<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterWorkspace extends Model
{
    protected $table = '5r_master_workspace';
    protected $primaryKey = 'id_workspace';
    public $keyType = 'string';
    public $incrementing = false;    
    protected $guarded = [];

    public function departments()
    {
        return $this->hasMany(MasterDepartment::class, 'id_workspace', 'id_workspace');
    }
}
