<?php
namespace App;

use App\Models\Loker\Penghuni;
use Illuminate\Database\Eloquent\Model;

class HrKaryawan extends Model
{
    protected $table   = 'hr_karyawan';
    protected $guarded = [];

    public function penghuni()
    {
        return $this->hasOne(Penghuni::class, 'nik', 'nik')->where('is_active', 'Y');
    }

    public function spPelanggarans()
    {
        return $this->hasMany(SpPelanggaran::class, 'employee_id');
    }
}
