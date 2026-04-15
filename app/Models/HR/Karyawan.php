<?php
namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table     = 'hr_karyawan';
    protected $timestamp = true;
    protected $fillable  = ['nik', 'nama', 'jenis_kelamin', 'staff', 'kode_divisi', 'active'];
}
