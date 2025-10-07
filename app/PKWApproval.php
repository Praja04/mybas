<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PKWApproval extends Model
{
    protected $table = 'pkw_approval_manager';
    protected $guarded = [];

    public function bagian()
    {
        return $this->belongsTo('App\PKWBagian', 'id_bagian');
    }

    public function approval_1()
    {
        return $this->belongsTo('App\User', 'approval1');
    }

    public function approval_2()
    {
        return $this->belongsTo('App\User', 'approval2');
    }

    public function approval_3()
    {
        return $this->belongsTo('App\User', 'approval3');
    }
}
