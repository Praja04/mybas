<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SertifikasiOperasional extends Model
{
    protected $table = 'sigra_sertifikasi_operasional';
    protected $guarded = [];

    // simpan sertifikasi dari table sigra_sertifikasi_operasional ke table attachments_local
    public function attachments()
    {
        return $this->hasMany('App\Models\LocalAttachment', 'transaction_id', 'transaction_id');
    }
}
