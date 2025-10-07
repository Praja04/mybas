<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SertifikatBDKTMiInstan extends Model
{
    protected $table = 'sigra_sertifikat_bdkt_mi_instan';
    protected $guarded = [];

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment', 'transaction_id', 'transaction_id');
    }
}
