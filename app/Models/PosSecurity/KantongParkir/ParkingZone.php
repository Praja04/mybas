<?php

namespace App\Models\PosSecurity\KantongParkir;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParkingZone extends Model
{
    use SoftDeletes;

    protected $table = 'parking_zones';
    protected $guarded = [];

    public function slots()
    {
        return $this->hasMany(ParkingSlot::class, 'parking_zone_id');
    }

    public function assignments()
    {
        return $this->hasMany(ParkingAssignment::class, 'parking_zone_id');
    }
}
