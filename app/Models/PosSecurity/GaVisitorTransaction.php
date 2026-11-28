<?php

namespace App\Models\PosSecurity;

use Illuminate\Database\Eloquent\Model;

class GaVisitorTransaction extends Model
{

    protected $table = 'ga_visitor_transaction';
    protected $guarded = [];

    public function cekKendaraan()
    {
        return $this->hasOne(GaCekKendaraan::class, 'trnvisitorid', 'trnvisitorid')
            ->whereNotNull('checked_in_at')
            ->orderBy('created_at', 'desc');
    }
}
