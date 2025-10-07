<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class KontrakVendor extends Model
{
    protected $table = 'sigra_kontrak_vendor';
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(MasterVendor::class, 'id_vendor');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    // simpan sertifikasi dari table sigra_kontrak_vendor ke table attachments_local
    public function attachments()
    {
        return $this->hasMany('App\Models\LocalAttachment', 'transaction_id', 'transaction_id');
    }
}
