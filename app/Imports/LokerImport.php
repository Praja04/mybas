<?php
namespace App\Imports;

use App\Models\Loker\Penghuni;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LokerImport implements ToModel, WithStartRow, WithCalculatedFormulas
{
    protected $gender;
    protected $prefix;
    private $lastLoker   = null;
    private $isValidated = false;

    public function __construct($gender)
    {
        $this->gender = $gender;
        $this->prefix = ($gender == 'L') ? 'LP' : 'LW';
    }

    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        // 1. SECURITY CHECK (Kolom C / Index 2)
        $kodeLokerExcel = isset($row[2]) ? trim((string) $row[2]) : '';
        if (! $this->isValidated && ! empty($kodeLokerExcel)) {
            if ($this->gender == 'L' && strpos($kodeLokerExcel, 'WS') !== false) {
                throw new Exception("File WANITA diupload di form PRIA.");
            }
            if ($this->gender == 'P' && strpos($kodeLokerExcel, 'PS') !== false) {
                throw new Exception("File PRIA diupload di form WANITA.");
            }
            $this->isValidated = true;
        }

        // 2. HANDLE NO LOKER (Kolom B / Index 1)
        $noLokerRaw = isset($row[1]) ? trim((string) $row[1]) : '';
        if ($noLokerRaw !== '' && is_numeric($noLokerRaw)) {
            $this->lastLoker = $noLokerRaw;
        }

        if (empty($this->lastLoker)) {
            return null;
        }

        // 3. AMBIL DATA PERSONIL
        $nik  = isset($row[6]) ? trim((string) $row[6]) : '';
        $nama = isset($row[8]) ? trim((string) $row[8]) : '';
        $nama = preg_replace('/\s+/', ' ', trim($nama));

        if (empty($nama) || $nama === '-' || $nama === 'Nama Karyawan') {
            return null;
        }

        if (empty($nik) || $nik === '-') {
            $nik = 'TMP-' . strtoupper(substr(md5($nama), 0, 6));
        }

        // 4. KATEGORI
        $rawKategori   = isset($row[9]) ? strtolower(trim((string) $row[9])) : '';
        $kategoriFinal = 'non_staff';
        if (strpos($rawKategori, 'staff') !== false && strpos($rawKategori, 'non') === false) {
            $kategoriFinal = 'staff';
        } elseif (strpos($rawKategori, 'mitra') !== false) {
            $kategoriFinal = 'mitra_kerja';
        }

        return new Penghuni([
            'nik'               => $nik,
            'nama'              => $nama,
            'divisi'            => isset($row[10]) ? substr(trim((string) $row[10]), 0, 50) : '-',
            'kode_rak'          => $this->prefix,
            'no_loker'          => (string) $this->lastLoker,
            'kategori_karyawan' => $kategoriFinal,
            'tgl_masuk'         => now(),
            'is_active'         => 'Y',
        ]);
    }

    // Tambahkan helper untuk mengambil nomor loker terakhir yang terbaca
    public function getLastLoker()
    {
        return $this->lastLoker;
    }
}
