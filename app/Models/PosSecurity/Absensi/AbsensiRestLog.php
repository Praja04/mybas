<?php

namespace App\Models\PosSecurity\Absensi;

use Illuminate\Database\Eloquent\Model;
use App\Models\PosSecurity\GaVisitorTransaction;
use App\Models\PosSecurity\GaVisitorVendorTransaction;

class AbsensiRestLog extends Model
{
    protected $table = 'ga_visitor_rest_logs';

    protected $fillable = [
        'trnvisitorid',
        'source_origin',       // ← HARUS ADA!
        'activity_type',
        'scan_time',
        'tanggal_log',
        'catatan',
        'logged_by',

        // Tambahan dari visitor
        'no_kartu',
        'no_ktp_sim',
        'nama',
        'namavisitor',
        'namacomp',
        'host',
        'hostdeptid',
        'purpose',
        'nopol',
        'nohpdriver',
        'nama_kernet',
        'tgl_lahir',
        'plant',
        'imgvisitorpathin',
        'foto',
        'kartu_dikembalikan',
    ];

    // protected $casts = [
    //   'scan_time' => 'datetime',
    //   'tanggal_log' => 'date',
    // ];

    protected $casts = [
        'scan_time' => 'datetime',
        'tanggal_log' => 'date',
        'tgl_lahir' => 'date',
        'kartu_dikembalikan' => 'boolean',
    ];

    // Relasi ke tamu (opsional)
    public function visitorpos2()
    {
        return $this->belongsTo(GaVisitorVendorTransaction::class, 'trnvisitorid', 'trnvisitorid');
    }

    public function visitorpos1()
    {
        return $this->belongsTo(GaVisitorTransaction::class, 'trnvisitorid', 'trnvisitorid');
    }
}
