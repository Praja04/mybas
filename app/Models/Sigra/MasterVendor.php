<?php

namespace App\Models\Sigra;

use Illuminate\Database\Eloquent\Model;

class MasterVendor extends Model
{
    protected $table = 'sigra_vendor';
    protected $guarded = [];
    
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
    }

    public function kontrakVendor()
    {
        return $this->hasMany(KontrakVendor::class, 'id_vendor')->where('status', '!=', 'deleted');
    }
}
