<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterDepartment extends Model
{
    protected $table = '5r_master_department';
    protected $guarded = [];
    public $primaryKey = 'id_department';
    public $ketType = 'string';
    public $incrementing = false;

    public function groups()
    {
        return $this->hasMany('App\Models\System5R\DepartmentComittee', 'id_department', 'id_department');
    }
}


