<?php

namespace App\Models\PosSecurity\KantongParkir;

use App\Models\PosSecurity\KantongParkir\ParkingSlot;
use App\Models\PosSecurity\KantongParkir\ParkingZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'parking_assignments';
    protected $guarded = [];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    public function zone()
    {
        return $this->belongsTo(ParkingZone::class, 'parking_zone_id');
    }

    public function slot()
    {
        return $this->belongsTo(ParkingSlot::class, 'parking_slot_id');
    }
}
