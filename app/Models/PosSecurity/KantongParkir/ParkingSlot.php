<?php

namespace App\Models\PosSecurity\KantongParkir;

use App\Models\PosSecurity\KantongParkir\ParkingAssignment;
use App\Models\PosSecurity\KantongParkir\ParkingSlotStatusHistory;
use App\Models\PosSecurity\KantongParkir\ParkingZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingSlot extends Model
{
    use SoftDeletes;

    protected $table = 'parking_slots';
    protected $guarded = [];

    public function zone()
    {
        return $this->belongsTo(ParkingZone::class, 'parking_zone_id');
    }

    public function assignments()
    {
        return $this->hasMany(ParkingAssignment::class, 'parking_slot_id');
    }

    public function activeAssignment()
    {
        return $this->hasOne(ParkingAssignment::class, 'parking_slot_id')
            ->whereIn('status_assignment', ['assigned', 'parked'])
            ->orderBy('id', 'desc');
    }

    public function histories()
    {
        return $this->hasMany(ParkingSlotStatusHistory::class, 'parking_slot_id');
    }
}
