<?php
namespace App\Imports;

use App\Models\Loker\Penghuni;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LokerImport implements ToModel, WithStartRow, WithCalculatedFormulas, WithBatchInserts, WithChunkReading
{
    protected $gender;
    protected $prefix;
    private $lastLoker   = null;
    private $lastStatus  = 'Sepatu: Aktif | Baju: Aktif';
    private $isValidated = false;

    public function __construct($gender)
    {
        $this->gender = $gender;
        // Prefix untuk database loker_rak
        $this->prefix = ($gender == 'L') ? 'LP' : 'LW';
    }

    public function startRow(): int
    {
        // Data dimulai dari baris ke-4
        return 4;
    }

    public function model(array $row)
    {
        // --- 1. IDENTIFIKASI DATA DASAR ---
        $noLokerRaw = isset($row[0]) ? trim((string) $row[0]) : '';
        $nikExcel   = isset($row[5]) ? trim((string) $row[5]) : '';
        $namaExcel  = isset($row[7]) ? trim((string) $row[7]) : '';
        $namaExcel  = strtoupper(preg_replace('/\s+/', ' ', trim($namaExcel)));

        // --- 2. PAGAR FOOTER & DATA SAMPAH ---
        $cekFooter = strtolower($namaExcel . $nikExcel . $noLokerRaw . ($row[2] ?? '') . ($row[4] ?? ''));

        if (
            str_contains($cekFooter, 'rumah') ||
            str_contains($cekFooter, 'total') ||
            str_contains($cekFooter, 'pcs') ||
            str_contains($cekFooter, 'terbuka') ||
            str_contains($cekFooter, 'rp.') ||
            (empty($noLokerRaw) && empty($namaExcel))
        ) {
            return null;
        }

        // --- 3. SECURITY CHECK (GENDER MISMATCH) ---
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

        // --- 4. HANDLE NOMOR LOKER (LOGIC MEMORY) ---
        // if ($noLokerRaw !== '' && ! is_numeric($noLokerRaw)) {
        //     return null;
        // }

        // Jika angka, simpan ke memori lastLoker
        if ($noLokerRaw !== '' && is_numeric($noLokerRaw)) {
            $this->lastLoker  = $noLokerRaw;
            $this->lastStatus = 'Sepatu: Aktif | Baju: Aktif'; // Reset status ke default
        }

        // Jika sampai sini lastLoker masih kosong, skip
        if (empty($this->lastLoker)) {
            return null;
        }

        $noLokerFix = trim((string) $this->lastLoker);

        // --- 5. HANDLE STATUS KONDISI FISIK ---
        $statusSepatu = isset($row[2]) ? trim((string) $row[2]) : '';
        $statusBaju   = isset($row[4]) ? trim((string) $row[4]) : '';

        $cekSepatu = strtolower($statusSepatu);
        $cekBaju   = strtolower($statusBaju);
        $ignored   = ['', '-', 'null', '0', 'aktif'];

        if (! in_array($cekSepatu, $ignored) || ! in_array($cekBaju, $ignored)) {
            if (strlen($statusSepatu) < 15 && strlen($statusBaju) < 15) {
                $newSepatu        = (! in_array($cekSepatu, $ignored)) ? $statusSepatu : 'Aktif';
                $newBaju          = (! in_array($cekBaju, $ignored)) ? $statusBaju : 'Aktif';
                $this->lastStatus = "Sepatu: {$newSepatu} | Baju: {$newBaju}";
            }
        }

        // --- 6. UPDATE DATABASE (LOKER RAK) ---
        DB::table('loker_rak')->updateOrInsert(
            ['kode_rak' => $this->prefix, 'no_loker' => $noLokerFix],
            [
                'kondisi_fisik' => $this->lastStatus,
                'gender'        => $this->gender,
                'is_active'     => 'Y',
                'updated_at'    => now(),
            ]
        );

        if (empty($namaExcel) || $namaExcel === '-' || $namaExcel === 'Nama Karyawan') {
            return null;
        }

        // --- 7. SINKRONISASI DATA PUSAT & PENGHUNI ---
        // $nikFinal    = $nikExcel;
        $nikFinal    = (empty($nikExcel) || $nikExcel === '-') ? null : $nikExcel;
        $divisiFinal = isset($row[9]) ? strtoupper(substr(trim((string) $row[9]), 0, 50)) : '-';

        try {
            $dataPusat = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
            // ->select('NIK', 'DEPTID', 'EMPNM')
                ->where(function ($q) use ($nikFinal, $namaExcel) {
                    if ($nikFinal !== null) {
                        $q->whereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$nikFinal])
                            ->orWhere('EMPNM', $namaExcel);
                    } else {
                        $q->where('EMPNM', $namaExcel);
                    }
                })
                ->exists();

            if (! $dataPusat) {
                $nikLog = $nikFinal ?? 'KOSONG';
                Log::warning("Import LokerBAS: Karyawan tidak ditemukan di DB Pusat (PAS). Nama: {$namaExcel}, NIK: {$nikLog}");
            }
        } catch (Exception $e) {
            Log::error("Import Error - DB Pusat Offline: " . $e->getMessage());
        }

        // 8. KATEGORI KARYAWAN
        $rawKategori   = isset($row[8]) ? strtolower(trim((string) $row[8])) : '';
        $kategoriFinal = 'non_staff';

        if (strpos($rawKategori, 'staff') !== false && strpos($rawKategori, 'non') === false) {
            $kategoriFinal = 'staff';
        } elseif (strpos($rawKategori, 'mitra') !== false) {
            $kategoriFinal = 'mitra_kerja';
        }

        // 9. RETURN UNTUK INSERT KE TABEL LOKER_PENGHUNI
        return new Penghuni([
            'nik'               => $nikFinal,
            'nama'              => $namaExcel,
            'divisi'            => $divisiFinal,
            'kode_rak'          => $this->prefix,
            'no_loker'          => $noLokerFix,
            'kategori_karyawan' => $kategoriFinal,
            'tgl_masuk'         => now(),
            'is_active'         => 'Y',
        ]);
    }

    public function getLastLoker()
    {
        return $this->lastLoker;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
