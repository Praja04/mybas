<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LunchBreak extends Model
{
    protected $table = 'lunch_break_histories';
    
    protected $fillable = [
        'nik',
        'nama',
        'divisi',
        'jam_keluar',
        'jam_masuk',
        'menit_terlambat',
        'status',
    ];

    protected $dates = [
        'jam_keluar',
        'jam_masuk',
    ];
}
