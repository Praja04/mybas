<?php

namespace App\Models\System5R;

use Illuminate\Database\Eloquent\Model;

class JawabanGroup extends Model
{
    protected $table = '5r_jawaban_group';
    protected $primaryKey = 'id_jawaban_group';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'id_jawaban_group', 'id_jawaban_group');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'id_periode', 'id_periode');
    }

    public function group()
    {
        return $this->belongsTo(MasterGroup::class, 'id_group', 'id_group');
    }
}
