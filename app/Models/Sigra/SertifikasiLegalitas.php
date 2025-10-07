<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SertifikasiLegalitas extends Model
{
    protected $table = 'sigra_sertifikasi_legalitas';
    protected $guarded = [];

    // simpan sertifikasi dari table sigra_sertifikasi_legalitas ke table attachments_local
    public function attachments()
    {
        return $this->hasMany('App\Models\LocalAttachment', 'transaction_id', 'transaction_id');
    }
}
