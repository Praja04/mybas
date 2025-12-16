<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterArea extends Model
{
    protected $table = '5r_master_area';
    public $primaryKey = 'id_area';

    protected $guarded = [];
    public $incrementing = false;

    public function department()
    {
        return $this->belongsTo(MasterDepartment::class, 'id_department', 'id_department');
    }
}
