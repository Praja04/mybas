<?php

namespace App\Imports\HRConnect;

use App\HrKaryawan;
use App\HrGoodieApd;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GaShiftIn implements ToModel, WithHeadingRow
{
    public $hitung = 0;

    public function model(array $row)
    {
        try {
            $karyawan = HrKaryawan::where('nik', $row['nik'])->first();

            if ($karyawan && $row['id_card'] == '1' && $karyawan->in_complete == 'N') {
                HrKaryawan::where('id', $karyawan->id)
                    ->update(['in_complete' => 'Y']);

                HrGoodieApd::create([
                    'tgl_masuk' => Carbon::parse(now())->format('Y-m-d'),
                    'jumlah_orang' => $this->hitung,
                ]);

                $lokerId = $karyawan->lokerId;
                $idCard = $karyawan->idCard;

                if($karyawan->jenis_kelamin == 'L'){
                    $kodeArea = 'p_prob';
                }else{
                    $kodeArea = 'w_prob';
                }

                // $namaLoker = $karyawan->namaLoker;
                // $nomorLoker = $karyawan->nomorLoker;
                $nik = $karyawan->nik;
                $nama = $karyawan->nama;
                $jk = $karyawan->jk;
                $divisi = $karyawan->divisi;
                $bagian = $karyawan->bagian;
                $group = $karyawan->group;
                $kodekontrak = $karyawan->kodekontrak;

                // Buat histori transaksi dulu
                $sebelumnya = \DB::table('loker_master_user')
                                ->where([
                                    'kode_area' => $kodeArea,
                                    'kode_blok' => $row['kode_blok'],
                                    'no_loker' => $row['no_loker']
                                ])->first();

                $data = [
                    'nik' => $sebelumnya->nik,
                    'no_loker' => $row['no_loker'],
                    'status' => 'IN',
                    'keterangan' => 'Karyawan Baru Join',
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'tgl_pengisi' => date('Y-m-d'),
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'jam_pengisi' => date('H:i:s'),
                    'penghuni_sebelumnya' => $sebelumnya->nama,
                    'alasan' => 'Karyawan Baru Join',
                    'kode_area' => $kodeArea,
                    'kode_blok' => $row['kode_blok']
                ];

                \DB::table('loker_user_transaksi')->insert($data);

                // Update loker menjadi status 1
                \DB::table('loker_master_nomer')
                    ->where([
                        'kode_area' => $kodeArea,
                        'kode_blok' => $row['kode_blok'],
                        'no_loker' => $row['no_loker']
                    ])
                    ->update(['status' => 1]);

                \DB::table('loker_master_user')
                    ->where([
                        'kode_area' => $kodeArea,
                        'kode_blok' => $row['kode_blok'],
                        'no_loker' => $row['no_loker']
                    ])->update([
                        'nik' => $nik,
                        'nama' => $nama,
                        'jk' => $jk,
                        'divisi' => $divisi,
                        'bagian' => $bagian,
                        'group' => $group,
                        'kode_kontrak' => $kodekontrak,
                    ]);
            }

            $this->hitung++;
        } catch (\Exception $e) {
            throw $e;
        }
        
        return null; 
    }
}
