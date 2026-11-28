<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;

class SIOExport implements FromArray
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function getSIOData(): array
    {
        $sioData = [];
        $sioData[] = ['No', 'Nama Perusahaan', 'Nama Perizinan', 'Nama Karyawan', 'NIK Karyawan', 'Departemen', 'Tanggal Mulai Ikatan Dinas', 'Tanggal Selesai Ikatan Dinas', 'Nomor Izin', 'Tanggal Terbit', 'Tanggal Habis', 'Harga', 'Keterangan'];

        $query = SIO::with(['department', 'perusahaan', 'sertifikasi'])
            ->where('status', '!=', 'deleted');

        if (!empty($this->filters['dept_id'])) {
            $query->where('dept_id', $this->filters['dept_id']);
        }

        if (!empty($this->filters['status'])) {
            $st = $this->filters['status'];
            if ($st === 'active' || $st === 'inactive') {
                $query->where('status', $st);
            }
        }

        $sios = $query->get();
        $no = 1;

        foreach ($sios as $sio) {
            $sioSertifikasi = $sio->sertifikasi->sortByDesc('tanggal_habis')->first();

            $expired = '-';
            if ($sioSertifikasi) {
                $overdue = empty($sioSertifikasi->tanggal_habis) || strtotime($sioSertifikasi->tanggal_habis) === false
                    ? null
                    : (strtotime($sioSertifikasi->tanggal_habis) - strtotime(date('Y-m-d'))) / 86400;

                if (!is_numeric($overdue)) {
                    $expired = 'secondary';
                } elseif ($overdue > 45) {
                    $expired = 'success';
                } elseif ($overdue > 0 && $overdue <= 45) {
                    $expired = 'warning';
                } elseif ($overdue <= 0) {
                    $expired = 'danger';
                }
            }

            if (!empty($this->filters['status'])) {
                $st = $this->filters['status'];
                if ($st === 'aman' && $expired !== 'success') {
                    continue;
                }
                if ($st === 'warning' && $expired !== 'warning') {
                    continue;
                }
                if ($st === 'expired' && $expired !== 'danger') {
                    continue;
                }
            }

            if ($sioSertifikasi && $sioSertifikasi->tanggal_terbit) {
                $tglTerbit = date('Y-m-d', strtotime($sioSertifikasi->tanggal_terbit));
                if (!empty($this->filters['tgl_terbit_awal']) && $tglTerbit < $this->filters['tgl_terbit_awal']) {
                    continue;
                }
                if (!empty($this->filters['tgl_terbit_akhir']) && $tglTerbit > $this->filters['tgl_terbit_akhir']) {
                    continue;
                }
            } else {
                if (!empty($this->filters['tgl_terbit_awal']) || !empty($this->filters['tgl_terbit_akhir'])) {
                    continue;
                }
            }

            if ($sioSertifikasi && $sioSertifikasi->tanggal_habis) {
                $tglExpired = date('Y-m-d', strtotime($sioSertifikasi->tanggal_habis));
                if (!empty($this->filters['tgl_expired_awal']) && $tglExpired < $this->filters['tgl_expired_awal']) {
                    continue;
                }
                if (!empty($this->filters['tgl_expired_akhir']) && $tglExpired > $this->filters['tgl_expired_akhir']) {
                    continue;
                }
            } else {
                if (!empty($this->filters['tgl_expired_awal']) || !empty($this->filters['tgl_expired_akhir'])) {
                    continue;
                }
            }

            if ($sioSertifikasi) {
                $array = [
                    $no++,
                    $sio->perusahaan->nama_perusahaan ?? '-',
                    $sio->nama_perizinan,
                    $sio->nama_karyawan,
                    $sio->nik_karyawan,
                    optional($sio->department)->name ?? '-',
                    $sio->tanggal_mulai_ikatan_dinas,
                    $sio->tanggal_selesai_ikatan_dinas,
                    $sioSertifikasi->nomor_izin,
                    $sioSertifikasi->tanggal_terbit,
                    $sioSertifikasi->tanggal_habis,
                    $sioSertifikasi->harga,
                    $sioSertifikasi->keterangan,
                ];
                $sioData[] = $array;
            }
        }
        return $sioData;
    }

    public function array(): array
    {
        return $this->getSIOData();
    }
}
