<?php

namespace App\Http\Controllers\HRConnect;

use App\User;
use App\HrKaryawan;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\GaShiftOut;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;

class GAShiftOutController extends Controller
{
    public function getData(Request $req)
    {
        $query = HrKaryawan::where([
                // 'in_complete' => 'Y', 
                // 'in_kode_group' => 'Y',
                'is_excuse_out' => 'Y',
                'out_complete' => 'N',
            ])
            ->Where('tanggal_keluar', '>', '2025-01-18');

        if($req->tampilkan_semua == 0){
            $query->where([
                'tgl_shift_out' => $req->tanggal
            ]);
        }
        
        $data = $query->get();

        return Datatables::of($data)->make(true);
    }

    public function index()
    {
        $data['title'] = 'GA - Karyawan Keluar';

        return view('hr-connect.ga.shift-out', $data);
    }

    public function update(Request $req)
    {
        $data = $req->input('data');
        // dd($data);
        if(!empty($data)){
            // [x] Uncomment ketika selesai testing email 
            foreach ($data as $item) {
                if($item['checklistId'] !== 'on'){
                    $nik = $item['nik'];
                    $alasan_keluar = $item['alasankeluar'];

                    $sebelumnya = \DB::table('loker_master_user')
                        ->where('nik', $nik)->first();

                    if($sebelumnya != null){
                        $data = [
                            'nik' => $sebelumnya->nik,
                            'no_loker' => $sebelumnya->no_loker,
                            'status' => 'OUT',
                            'keterangan' => 'Sudah Keluar',
                            'nama_pengisi' => auth()->user()->name ?? '',
                            'tgl_pengisi' => date('Y-m-d'),
                            'nik_pengisi' => auth()->user()->username ?? '',
                            'jam_pengisi' => date('H:i:s'),
                            'penghuni_sebelumnya' => $sebelumnya->nama,
                            'alasan' => $alasan_keluar,
                            'kode_area' => $sebelumnya->kode_area,
                            'kode_blok' => $sebelumnya->kode_blok,

                        ];
    
                        \DB::table('loker_user_transaksi')->insert($data);
                        
                        \DB::table('loker_master_nomer')
                            ->where([
                                'kode_blok' => $sebelumnya->kode_blok,
                                'no_loker' => $sebelumnya->no_loker,
                                'kode_area' => $sebelumnya->kode_area
                            ])->update(['status' => 0]);
    
                        // Senin tanya ke mas her soal ini!
                        \DB::table('loker_master_user')->where('nik', $nik)
                                ->update([
                                    'nik' => '',
                                    'nama' => ''
                                ]); 
                    }

                    HrKaryawan::where('id', $item['checklistId'])
                        ->update([
                            'out_complete' => $item['status'] =='check' ? 'Y' : 'N'
                        ]);
                }
            }

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->get();

            $to = $email_hr->pluck('email')->toArray();  
            $link = url('/hr-connect/dept-ga/karyawan-keluar');
            KaryawanKeluarSelesaiToHR::dispatch($to, $data, $link);

            return response()->json(['success' => true, 'message' => 'Data berhasil dikirim.']);
        }else {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.']);
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

                Excel::import(new GaShiftOut, $file);

                return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
            } catch (\Exception $e) {
                \Log::error('Error during file upload: ' . $e->getMessage());

                return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
            }
        }

        return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    }
}
