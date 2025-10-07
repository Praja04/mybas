<?php

namespace App\Http\Controllers\HRConnect;

use Carbon\Carbon;
use App\HrKaryawan;
use App\HrGoodieApd;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\GaShiftIn;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class GAShiftInController extends Controller
{
    public function getData(Request $req)
    {
        $query = HrKaryawan::where('in_complete', 'N')
            ->where('tanggal_masuk', '>', '2025-01-01');

        if ($req->tampilkan_semua == 0) {
            $query->where([
                'tanggal_masuk' => $req->tanggal
            ]);
        }

        $data = $query->get();

        return Datatables::of($data)->make(true);
    }

    public function index()
    {
        // dd('test');
        $data['title'] = 'GA - Karyawan Masuk';
        $data['lokers_pria'] = \DB::table('loker_master_nomer')
            ->where(['status' => 0, 'kode_area' => 'pb1'])
            ->orWhere(['status' => 0, 'kode_area' => 'pb2'])
            ->get();

        $data['lokers_wanita'] = \DB::table('loker_master_nomer')
            ->where(['status' => 0, 'kode_area' => 'wb1'])
            ->orWhere(['status' => 0, 'kode_area' => 'wb2'])
            ->get();


        return view('hr-connect.ga.shift-in', $data);
    }

    // public function updateStatus(Request $request)
    // {
    //     $data = $request->input('data');
    //      dd($data);
    //     if (!empty($data)) {
    //         $totalChecked = count($data);

    //         HrGoodieApd::create([
    //             'tgl_masuk' => Carbon::parse(now())->format('Y-m-d'),
    //             'jumlah_orang' => $totalChecked,
    //         ]);

    //         // [x] Uncommenct jangan lupa kalo udah selesai Goodie APD nya!
    //         foreach ($data as $item) {
    //             $lokerId = $item['lokerId'];
    //             $idCard = $item['idCard'];
    //             $kodeArea = $item['kodeArea'];
    //             $namaLoker = $item['namaLoker'];
    //             $nomorLoker = $item['nomorLoker'];
    //             $nik = $item['nik'];
    //             $nama = $item['nama'];
    //             $jk = $item['jk'];
    //             $divisi = $item['divisi'];
    //             $bagian = $item['bagian'];
    //             $group = $item['group'];
    //             $kodekontrak = $item['kodekontrak'];

    //             // Buat histori transaksi dulu
    //             $sebelumnya = \DB::table('loker_master_user')
    //                             ->where([
    //                                 'kode_area' => $kodeArea,
    //                                 'kode_blok' => $namaLoker,
    //                                 'no_loker' => $nomorLoker
    //                             ])->first();

    //             $data = [
    //                 'nik' => $sebelumnya->nik,
    //                 'no_loker' => $nomorLoker,
    //                 'status' => 'IN',
    //                 'keterangan' => 'Karyawan Baru Join',
    //                 'nama_pengisi' => auth()->user()->name ?? '',
    //                 'tgl_pengisi' => date('Y-m-d'),
    //                 'nik_pengisi' => auth()->user()->username ?? '',
    //                 'jam_pengisi' => date('H:i:s'),
    //                 'penghuni_sebelumnya' => $sebelumnya->nama,
    //                 'alasan' => 'Karyawan Baru Join',
    //                 'kode_area' => $kodeArea,
    //                 'kode_blok' => $namaLoker
    //             ];

    //             \DB::table('loker_user_transaksi')->insert($data);

    //             // Update loker menjadi status 1
    //             \DB::table('loker_master_nomer')
    //                 ->where('id', $lokerId)
    //                 ->update(['status' => 1]);

    //             // $data = [
    //             //     'nik' => $nik,
    //             //     'nama' => $nama,
    //             //     'jk' => $jk,
    //             //     'divisi' => $divisi,
    //             //     'bagian' => $bagian,
    //             //     'group' => $group,
    //             //     'kode_kontrak' => $kodekontrak,
    //             //     'kode_area' => $kodeArea,
    //             //     'kode_blok' => $namaLoker,
    //             //     'no_loker' => $nomorLoker,
    //             // ]; 
    //             // \DB::table('loker_master_user')->insert($data);

    //             \DB::table('loker_master_user')
    //                 ->where([
    //                     'kode_area' => $kodeArea,
    //                     'kode_blok' => $namaLoker,
    //                     'no_loker' => $nomorLoker
    //                 ])->update([
    //                     'nik' => $nik,
    //                     'nama' => $nama,
    //                     'jk' => $jk,
    //                     'divisi' => $divisi,
    //                     'bagian' => $bagian,
    //                     'group' => $group,
    //                     'kode_kontrak' => $kodekontrak,
    //                 ]);

    //             \DB::table('hr_karyawan')
    //                 ->where('id', $idCard)
    //                 ->update(['in_complete' => 'Y']);
    //         }

    //         return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.']);
    //     }
    // }
    public function updateStatus(Request $request)
    {
        try {
            $data = $request->input('data');
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dikirim.'
                ], 400);
            }

            $totalChecked = count($data);

            HrGoodieApd::create([
                'tgl_masuk' => Carbon::now()->format('Y-m-d'),
                'jumlah_orang' => $totalChecked,
            ]);

            foreach ($data as $item) {
                $lokerId = $item['lokerId'];
                $idCard = $item['idCard'];
                $kodeArea = $item['kodeArea'];
                $namaLoker = $item['namaLoker'];
                $nomorLoker = $item['nomorLoker'];
                $nik = $item['nik'];
                $nama = $item['nama'];
                $jk = $item['jk'];
                $divisi = $item['divisi'];
                $bagian = $item['bagian'];
                $group = $item['group'];
                $kodekontrak = $item['kodekontrak'];

                $sebelumnya = \DB::table('loker_master_user')->where([
                    'kode_area' => $kodeArea,
                    'kode_blok' => $namaLoker,
                    'no_loker' => $nomorLoker
                ])->first();

                if (!$sebelumnya) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data loker sebelumnya tidak ditemukan.'
                    ], 404);
                }

                \DB::table('loker_user_transaksi')->insert([
                    'nik' => $sebelumnya->nik,
                    'no_loker' => $nomorLoker,
                    'status' => 'IN',
                    'keterangan' => 'Karyawan Baru Join',
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'tgl_pengisi' => date('Y-m-d'),
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'jam_pengisi' => date('H:i:s'),
                    'penghuni_sebelumnya' => $sebelumnya->nama,
                    'alasan' => 'Karyawan Baru Join',
                    'kode_area' => $kodeArea,
                    'kode_blok' => $namaLoker
                ]);

                \DB::table('loker_master_nomer')->where('id', $lokerId)->update(['status' => 1]);

                \DB::table('loker_master_user')->where([
                    'kode_area' => $kodeArea,
                    'kode_blok' => $namaLoker,
                    'no_loker' => $nomorLoker
                ])->update([
                    'nik' => $nik,
                    'nama' => $nama,
                    'jk' => $jk,
                    'divisi' => $divisi,
                    'bagian' => $bagian,
                    'group' => $group,
                    'kode_kontrak' => $kodekontrak,
                ]);

                \DB::table('hr_karyawan')->where('id', $idCard)->update(['in_complete' => 'Y']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Update Status Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadExcel(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($req->hasFile('excel_file')) {
            $file = $req->file('excel_file');

            if (!$file->isValid()) {
                return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
            }

            try {
                if ($file->getClientOriginalExtension() !== 'xlsx') {
                    return response()->json(['message' => 'Format file tidak valid. Hanya menerima file .xlsx.'], 400);
                }

                Excel::import(new GaShiftIn, $file);

                return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
            } catch (\Exception $e) {
                \Log::error('Error during file upload: ' . $e->getMessage());

                return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
            }
        }

        return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    }
}
