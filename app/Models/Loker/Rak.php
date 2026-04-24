<?php
namespace App\Models\Loker;

use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table   = 'loker_rak';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'no_loker' => 'string',
    ];

    public function penghuni()
    {
        return $this->hasMany(Penghuni::class, 'no_loker', 'no_loker')
            ->whereColumn('kode_rak', 'loker_rak.kode_rak')
            ->where('is_active', 'Y')
            ->whereNull('tgl_keluar');
    }

    public function getStatusLabelAttribute()
    {
        if ($this->is_active === 'N') {
            return 'rusak';
        }

        $listPenghuni = $this->penghuni;
        $count        = $listPenghuni->count();

        $adaStaff = $listPenghuni->contains('kategori_karyawan', 'staff');

        $maxKapasitas = $adaStaff ? 1 : ($this->kapasitas ?? 2);

        return ($count >= $maxKapasitas) ? 'penuh' : 'tersedia';
    }
}
