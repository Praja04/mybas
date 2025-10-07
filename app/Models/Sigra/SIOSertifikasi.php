<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class SIOSertifikasi extends Model
{
    protected $table = 'sigra_sertifikasi_sio';
    protected $guarded = [];

    public function attachments()
    {
        return $this->hasMany('App\Models\LocalAttachment', 'transaction_id', 'transaction_id');
    }
}
