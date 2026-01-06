<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterIncrement extends Model
{
    protected $table = '5r_master_increment';
    protected $guarded = [];
    public $ketType = 'string';
    public $incrementing = false;

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'id_department');
    }
}
