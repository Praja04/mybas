<?php
namespace App\Imports;

use App\Models\Loker\Penghuni;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LokerImport implements ToModel, WithStartRow, WithCalculatedFormulas
{
    protected $gender;
    protected $prefix;
    private $lastLoker   = null;
    private $lastStatus  = 'Aktif | Aktif';
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
        $kodeLokerExcel = isset($row[1]) ? trim((string) $row[1]) : '';
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
        $noLokerRaw = isset($row[0]) ? trim((string) $row[0]) : '';
        if ($noLokerRaw !== '' && is_numeric($noLokerRaw)) {
            $this->lastLoker = $noLokerRaw;
        }

        if (empty($this->lastLoker)) {
            return null;
        }

        $statusSepatu = isset($row[2]) ? trim((string) $row[2]) : '';
        $statusBaju   = isset($row[4]) ? trim((string) $row[4]) : '';

        $cekSepatu = strtolower($statusSepatu);
        $cekBaju   = strtolower($statusBaju);

        $ignored = ['', '-', 'null', '0'];

        if (! in_array($cekSepatu, $ignored) || ! in_array($cekBaju, $ignored)) {
            $newSepatu = (! in_array($cekSepatu, $ignored)) ? $statusSepatu : 'Aktif';
            $newBaju   = (! in_array($cekBaju, $ignored)) ? $statusBaju : 'Aktif';

            $this->lastStatus = "Sepatu: {$newSepatu} | Baju: {$newBaju}";
        }

        DB::table('loker_rak')
            ->where('kode_rak', $this->prefix)
            ->where('no_loker', $noLokerRaw)
            ->update([
                'keterangan_kondisi' => $this->lastStatus,
                'updated_at'         => now(),
            ]);

        // 3. AMBIL DATA EXCEL
        $nikExcel  = isset($row[5]) ? trim((string) $row[5]) : '';
        $namaExcel = isset($row[7]) ? trim((string) $row[7]) : '';
        $namaExcel = strtoupper(preg_replace('/\s+/', ' ', trim($namaExcel)));

        if (empty($namaExcel) || $namaExcel === '-' || $namaExcel === 'Nama Karyawan') {
            return null;
        }

        // --- LOGIC VALIDASI & SINKRONISASI NIK PUSAT ---
        $nikFinal    = $nikExcel;
        $divisiFinal = isset($row[9]) ? strtoupper(substr(trim((string) $row[9]), 0, 50)) : '-';

        try {
            $dataPusat = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK', 'DEPTID', 'EMPNM')
                ->where(function ($q) use ($nikExcel, $namaExcel) {
                    $q->whereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$nikExcel])
                        ->orWhere('EMPNM', $namaExcel);
                })
                ->first();

            if ($dataPusat) {
                $divisiFinal = $dataPusat->DEPTID ?? $divisiFinal;

                // Log jika terjadi penggantian NIK untuk audit
                // if ($nikExcel !== $dataPusat->NIK) {
                //     Log::info("NIK Sync: Nama $namaExcel diubah dari $nikExcel menjadi $nikFinal (Source: Pusat)");
                // }
            } elseif (empty($nikExcel) || $nikExcel === '-') {
                $nikFinal = 'TMP-' . strtoupper(substr(md5($namaExcel), 0, 6));
            }
        } catch (Exception $e) {
            Log::error("Import Error - DB Pusat Offline: " . $e->getMessage());
        }

        // 4. KATEGORI (Logic tetap sama)
        $rawKategori   = isset($row[8]) ? strtolower(trim((string) $row[8])) : '';
        $kategoriFinal = 'non_staff';
        if (strpos($rawKategori, 'staff') !== false && strpos($rawKategori, 'non') === false) {
            $kategoriFinal = 'staff';
        } elseif (strpos($rawKategori, 'mitra') !== false) {
            $kategoriFinal = 'mitra_kerja';
        }

        return new Penghuni([
            'nik'               => $nikFinal, // NIK resmi hasil sinkronisasi
            'nama'              => $namaExcel,
            'divisi'            => $divisiFinal,
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
