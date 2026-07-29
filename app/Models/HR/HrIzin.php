<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class HrIzin extends Model
{
    protected $table = 'hr_izin';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    public const KODE_IJIN_MAP = [
        'Cuti'    => ['CB', 'CDC1', 'CDC2', 'CDC3', 'CIM', 'CK', 'CKT', 'CH', 'CM', 'CNA', 'CHJ', 'C2', 'C', 'CUT'],
        'Sakit'   => ['CHD', 'IM', 'KD', 'S'],
        'Sakit KK' => ['SKK'],
        'Mangkir' => ['A'],
    ];

    protected $fillable = [
        'nik', 'nama', 'dept', 'section', 'tgl',
        'no_spi', 'kode_ijin', 'keterangan', 'send_by_username',
    ];

    public static function getKategoriIjin(?string $kodeIjin): string
    {
        if ($kodeIjin === null || $kodeIjin === '') {
            return '';
        }

        $upper = strtoupper(trim($kodeIjin));
        foreach (self::KODE_IJIN_MAP as $kategori => $kodes) {
            if (in_array($upper, $kodes, true)) {
                return $kategori;
            }
        }
        return $upper;
    }
}
