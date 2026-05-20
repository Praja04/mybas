<?php
namespace App\Models\Loker;

use App\HrKaryawan;
use App\Models\Loker\Rak;
use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    // Nama tabel yang digunakan oleh model ini
    protected $table = 'loker_penghuni';

    public $timestamps = true;

    // Kolom yang tidak boleh diisi secara massal
    public $guarded = ['id'];

    protected $casts = [
        'no_loker'   => 'string',
        'tgl_masuk'  => 'date',
        'tgl_keluar' => 'date',
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'no_loker', 'no_loker')
            ->whereColumn('kode_rak', 'loker_penghuni.kode_rak');
    }

    public function hrKaryawan()
    {
        return $this->belongsTo(HrKaryawan::class, 'nik', 'nik');
    }
}
