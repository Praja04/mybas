<?php

namespace App\Models\PosSecurity\Logging;

use Illuminate\Database\Eloquent\Model;
use App\Models\PosSecurity\GaVisitorTransaction;

class BlacklistIdentitas extends Model
{

    protected $table = 'ga_lgtk_blacklist_identitas';
    protected $guarded = [];

    protected $fillable = [
        'no_identitas',
        'tanggal_lahir',
        'nama',
        'jenis_identitas',
        'alasan_blacklist',
        'tanggal_blacklist',
        'diblacklist_oleh',
        'aktif',
    ];

    protected $casts = [
        'tanggal_blacklist' => 'datetime',
        'tanggal_lahir' => 'date',
        'aktif' => 'boolean',
    ];

    public function transaksi()
    {
        return $this->hasOne(GaVisitorTransaction::class, 'trnvisitorid', 'trnvisitorid');
    }
}
