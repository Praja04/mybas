<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SertifikatSHBahanBaku extends Model
{
    protected $table = 'sigra_sertifikat_sh_bahan_baku';
    protected $guarded = [];

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment', 'transaction_id', 'transaction_id');
    }
}
