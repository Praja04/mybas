<?php
namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ParkingHistories extends Model
{
    protected $fillable = [
        'nik',
        'sn_card',
        'nama',
        'tapped_at',
        'status',
    ];

    protected $casts = [
        'tapped_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
