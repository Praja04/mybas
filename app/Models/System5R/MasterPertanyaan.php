<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class MasterPertanyaan extends Model
{
    protected $table = '5r_master_pertanyaan';
    protected $guarded = [];
    public $primaryKey = 'id_pertanyaan';
    public $ketType = 'string';
    public $incrementing = false;

    public function masterGroup()
    {
        return $this->belongsTo(MasterGroup::class, 'id_group');
    }

    public function temuan()
    {
        return $this->hasMany(Temuan::class, 'id_pertanyaan', 'id_pertanyaan');
    }
}
