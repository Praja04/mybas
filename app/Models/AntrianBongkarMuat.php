<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntrianBongkarMuat extends Model
{
    use SoftDeletes;
    protected $table = 'antrian_bongkar_muat';

    protected $fillable = [
        'nomor_antrian',
        'kategori',
        'status',
        'foto',
        'waktu_dipanggil',
        'waktu_selesai',
    ];

    protected $casts = [
        'waktu_dipanggil' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Menghitung durasi pelayanan dalam menit.
     */
    public function getDurasiPelayananAttribute()
    {
        if ($this->waktu_dipanggil && $this->waktu_selesai) {
            return $this->waktu_dipanggil->diffInMinutes($this->waktu_selesai);
        }
        return null;
    }

    /**
     * Menghitung total waktu tunggu.
     */
    public function getTotalWaktuTungguAttribute()
    {
        if ($this->created_at && $this->waktu_selesai) {
            return $this->created_at->diffInMinutes($this->waktu_selesai);
        }
        return null;
    }
}
