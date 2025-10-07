<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class DepartmentComittee extends Model
{
    protected $table = '5r_department_committee';
    protected $guarded = [];

    public function groups()
    {
        return $this->hasMany('App\Models\System5R\MasterGroup', 'id_department', 'id_department');
    }

    public function comittee()
    {
        return $this->belongsTo('App\Models\System5R\MasterDepartment', 'id_department', 'id_department');
    }
}
