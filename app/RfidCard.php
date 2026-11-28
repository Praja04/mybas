<?php
namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class RfidCard extends Model
{
    protected $fillable = ['sn_card'];
    protected $casts    = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
