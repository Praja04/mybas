<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PKWFormPA extends Model
{
    protected $table = 'pkw_form_pa';
    protected $guarded = [];

    public function pkw()
    {
        return $this->belongsTo('App\PKW', 'id_pkw');
    }

    public function aspek_penilaian()
    {
        return $this->belongsToMany('App\PKWAspekPenilaian', 'pkw_form_pa_aspek_penilaian', 'id_form_pa', 'id_aspek_penilaian')->withPivot('skala','catatan');
    }

    public function approval()
    {
        return $this->belongsTo('App\PKWApproval', 'id_approval');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\User', 'nama_supervisor');
    }

    public function manager()
    {
        return $this->belongsTo('App\User', 'nama_manager');
    }
}
