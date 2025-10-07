<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\UploadMasukHariLiburModel;
use Illuminate\Support\Facades\Session;
use Auth;

class UploadMasukHariLiburImport implements ToModel, WithHeadingRow
{
    private $tanggal;
    private $id_mhl;

    public function __construct($tanggal, $id_mhl)
    {
        $this->tanggal = $tanggal;
        $this->id_mhl = $id_mhl;
    }

    public function model(array $row)
    {
        $status = strtolower(trim($row['status']));
        $shift = trim($row['shift']);

        if ($status !== 'staff' && $status !== 'non staff') {
            Session::flash('error', 'hanya bisa diisi dengan status staff atau non staff');
            return null;
        }

        if (!in_array($shift, ['1', '2', '3'])) {
            Session::flash('error', 'hanya bisa diisi dengan shift 1,2 3.');
            return null;
        }

        $dataduplicate = UploadMasukHariLiburModel::where('nik', $row['nik_secure_access'])
            ->where('nama', $row['nama'])
            ->where('tanggal', $this->tanggal)
            ->first();

        if (empty($dataduplicate)) {
            return new UploadMasukHariLiburModel([
                'nik_karyawan' => $row['nik_karyawan'],
                'nik' => $row['nik_secure_access'],
                'nama' => $row['nama'],
                'department' => $row['departemen'],
                'shift' => $row['shift'],
                'status_karyawan' => $row['status'],
                'uploaded_by' => Auth::user()->name,
                'uploaded_at' => date('Y-m-d H:i:s'),
                'tanggal' => $this->tanggal,
                'id_mhl' => $this->id_mhl
            ]);
        } else {
            Session::flash('error', 'Data Sudah Ada');
            return null;
        }
    }
}
