<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BerasPicGa extends Model
{
    protected $table = 'beras_pic_ga';
    protected $guarded = [];

    public static function getActiveEmails()
    {
        return self::where('is_active', 'Y')->pluck('email')->toArray();
    }
}
