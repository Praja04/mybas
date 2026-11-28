<?php

namespace App\Models\PosSecurity;

use Illuminate\Database\Eloquent\Model;

class GaVisitorVendorTransaction extends Model
{
  protected $table = 'ga_visitor_vendor';
  protected $guarded = [];

  public function cekKendaraan()
  {
      return $this->hasOne(GaCekKendaraan::class, 'trnvisitorid', 'trnvisitorid')
          ->whereNotNull('checked_in_at')
          ->orderBy('created_at', 'desc');
  }
}
