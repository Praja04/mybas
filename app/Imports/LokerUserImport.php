<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\WithValidation;
use Session;

class LokerUserImport implements ToCollection, WithHeadingRow, WithValidation
{

    public function rules(): array
    {
        return [
            'nik' => 'unique:loker_master_user',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nik.unique' => ':attribute Sistem Mendeteksi Adanya NIK Yang Double , Periksa Lagi Ya Excelnya..',
        ];
    }

    public function collection(Collection $rows)
    {
        $data = [];
        $transaksi = [];
        $compare = [];

        foreach ($rows as $row) {
            $data[$row['kode_area'] . $row['kode_blok'] . $row['no_loker']] =  array(
                'nik' => $row['nik'],
                'nama' => $row['nama'],
                'jk' => $row['jenis_kelamin'],
                'divisi' => $row['divisi'],
                'bagian' => $row['bagian'],
                'group' => $row['group'],
                'kode_kontrak' => $row['kode_kontrak'],
                'kode_area' => $row['kode_area'],
                'kode_blok' => $row['kode_blok'],
                'no_loker' => $row['no_loker'],
            );

            $transaksi[$row['kode_area'] . $row['kode_blok'] . $row['no_loker']] =  array(
                'nik' => $row['nik'],
                'no_loker' => $row['no_loker'],
                'status' => 'IN',
                'keterangan' => 'Karyawan Baru Join',
                'alasan' => 'Karyawan Baru Join',
                'nama_pengisi' => Auth::user()->name,
                'nik_pengisi' => Auth::user()->username,
                'tgl_pengisi' => date('Y-m-d'),
                'jam_pengisi' => date('H:i:s'),
                'penghuni_sebelumnya' => $row['nama'],
                'kode_area' => $row['kode_area'],
                'kode_blok' => $row['kode_blok'],
            );

            $compare[$row['kode_area'] . $row['kode_blok'] . $row['no_loker']]  = DB::table('loker_master_user')
                ->where('kode_area', $row['kode_area'])
                ->where('kode_blok', $row['kode_blok'])
                ->where('no_loker', $row['no_loker'])
                ->get();
        }

        foreach ($compare as $key => $list) {
            if ($list->count() > 0) {
                if ($list[0]->nama != null || $list[0]->nama != '') {
                    Session::flash('error', 'Sistem Mendeteksi Blok ' . $list[0]->kode_blok . ' No Loker ' .  $list[0]->no_loker . ' ' . 'Sudah Ada Yang Menempati, Tarik Kunci Dulu Ya, Biar Bisa Di Masukan Dalam Sistem Di Masukan Kedalam Loker');
                    return back();
                }
            }
        }

        foreach ($compare as $list) {
            if ($list->count() > 0) {
                if ($list[0]->nama == null || $list[0]->nama == '') {
                    $datayanglumau = $data[$list[0]->kode_area . $list[0]->kode_blok . $list[0]->no_loker];
                    DB::table('loker_master_user')
                        ->where('kode_area', $datayanglumau['kode_area'])
                        ->where('kode_blok', $datayanglumau['kode_blok'])
                        ->where('no_loker', $datayanglumau['no_loker'])
                        ->update(
                            [
                                'nik' => $datayanglumau['nik'],
                                'nama' => $datayanglumau['nama'],
                                'jk' => $datayanglumau['jk'],
                                'divisi' => $datayanglumau['divisi'],
                                'bagian' => $datayanglumau['bagian'],
                                'group' => $datayanglumau['group'],
                                'kode_kontrak' => $datayanglumau['kode_kontrak'],
                                'kode_area' => $datayanglumau['kode_area'],
                                'kode_blok' => $datayanglumau['kode_blok'],
                                'no_loker' => $datayanglumau['no_loker'],
                            ]
                        );

                    DB::table('loker_master_nomer')
                        ->where('kode_area', $datayanglumau['kode_area'])
                        ->where('kode_blok', $datayanglumau['kode_blok'])
                        ->where('no_loker', $datayanglumau['no_loker'])
                        ->update(
                            [
                                'status' => 1,
                            ]
                        );
                    DB::table('loker_user_transaksi')->insert($transaksi);
                    Session::flash('info', 'Berhasil Di Masukan Kedalam Loker');
                    return back();
                } else {
                    DB::table('loker_master_user')->insert($data);
                    DB::table('loker_master_nomer')
                        ->where('kode_area', $datayanglumau['kode_area'])
                        ->where('kode_blok', $datayanglumau['kode_blok'])
                        ->where('no_loker', $datayanglumau['no_loker'])
                        ->update(
                            [
                                'status' => 1,
                            ]
                        );
                    Session::flash('info', 'Berhasil Di Masukan Kedalam Loker');
                    return back();
                }
            }
        }
        $jikatidakada = [];
        $validasi_nomer = 0;
        foreach ($rows as $row) {
            $validasi_nomer = DB::table('loker_master_blok')
                ->where('kode_area', $row['kode_area'])
                ->where('kode_blok', $row['kode_blok'])
                ->count();
            if ($validasi_nomer == 0) {
                Session::flash('error', 'Kode Area Tidak Ada Dalam Sistem, Buat Dulu Ya Menu Database...');
                return back();
            }
        }
        if ($validasi_nomer > 0) {
            foreach ($rows as $row) {
                $jikatidakada[] =  array(
                    'nik' => $row['nik'],
                    'nama' => $row['nama'],
                    'jk' => $row['jenis_kelamin'],
                    'divisi' => $row['divisi'],
                    'bagian' => $row['bagian'],
                    'group' => $row['group'],
                    'kode_kontrak' => $row['kode_kontrak'],
                    'kode_area' => $row['kode_area'],
                    'kode_blok' => $row['kode_blok'],
                    'no_loker' => $row['no_loker'],
                );

                DB::table('loker_master_nomer')
                    ->where('kode_area', $row['kode_area'])
                    ->where('kode_blok', $row['kode_blok'])
                    ->where('no_loker', $row['no_loker'])
                    ->update(
                        [
                            'status' => 1,
                        ]
                    );
            }
            DB::table('loker_master_user')->insert($jikatidakada);
            DB::table('loker_user_transaksi')->insert($transaksi);
            Session::flash('info', 'Data Berhasil Di Masukan Kedalam Loker');
            return back();
        }
    }
}
