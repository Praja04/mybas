<?php

namespace App\Models\PosSecurity\KantongParkir;

use Illuminate\Database\Eloquent\Model;

class ParkingSlotStatusHistory extends Model
{
    protected $table = 'parking_slot_status_histories';
    protected $guarded = [];

    public function slot()
    {
        return $this->belongsTo(ParkingSlot::class, 'parking_slot_id');
    }

    public function assignment()
    {
        return $this->belongsTo(ParkingAssignment::class, 'parking_assignment_id');
    }
}
