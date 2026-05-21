<?php
namespace App\Http\Controllers\HRConnect;

use App\Exports\HRConnect\KaryawanAktifExport;
use App\Exports\HRConnect\KaryawanKeluarExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\GaShiftOut;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class GAShiftOutController extends Controller
{
    public function getData(Request $req)
    {
        $query = HrKaryawan::with(['penghuni' => function ($q) {
            $q->where('is_active', 'Y');
        }])
        ->where('out_complete', 'N')
            ->where('tanggal_masuk', '>', '2025-01-01')
            ->orderBy('tanggal_masuk', 'desc');

        if ($req->tampilkan_semua == 0 && !empty($req->tanggal)) {
            $query->where('tanggal_masuk', $req->tanggal);
        }

        $data = $query->get();

        return Datatables::of($data)
        ->addColumn('checkStaff', function($row) {
            $dataLoker = DB::table('loker_penghuni')
            ->where('nik', $row->nik)
            ->first();

            if($dataLoker) {
                if(strtolower($dataLoker->kategori_karyawan) == 'staff') {
                    return 'Y';
                } else {
                    return 'N';
                }
            }

            return $row->staff;
        })
        ->make(true);
    }

    public function index()
    {
        $tanggalTersedia = HrKaryawan::select('tanggal_masuk')
        ->where('out_complete', 'N')
        ->whereNotNull('tanggal_masuk')
        ->groupBy('tanggal_masuk')
        ->orderBy('tanggal_masuk', 'desc')
        ->pluck('tanggal_masuk');

        return view('hr-connect.ga.shift-out', compact('tanggalTersedia'));
    }

    public function update(Request $req)
    {
        $data = $req->input('data');

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang diproses.',
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $nik          = $item['nik'];
                $idKaryawan   = $item['id_karyawan'];
                $alasanKeluar = $item['alasan'];

                $karyawan = HrKaryawan::find($idKaryawan);
                $nama     = ucwords(strtolower($karyawan->nama ?? '-'));

                $penghuni = DB::table('loker_penghuni')
                    ->where('nik', $nik)->first();

                DB::table('loker_transaksi')->insert([
                    'nik'            => $nik,
                    'nama'           => $nama,
                    'kode_rak'       => $penghuni ? $penghuni->kode_rak : '-',
                    'no_loker'       => $penghuni ? $penghuni->no_loker : '-',
                    'tipe_transaksi' => 'KELUAR',
                    'operator'       => auth()->user()->name ?? 'Sistem GA',
                    'keterangan'     => $penghuni
                        ? 'Keluar (Loker): ' . $alasanKeluar
                        : 'Keluar (Non-Loker): ' . $alasanKeluar,
                    'created_at'     => now(),
                ]);

                if ($penghuni) {
                    DB::table('loker_penghuni')->where('id', $penghuni->id)->delete();
                }

                HrKaryawan::where('id', $idKaryawan)->update([
                    'out_complete' => 'Y',
                ]);
            }

            DB::commit();

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })->select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->get();

            if ($email_hr->isNotEmpty()) {
                $to   = $email_hr->pluck('email')->toArray();
                $link = url('/hr-connect/dept-ga/karyawan-keluar');
                KaryawanKeluarSelesaiToHR::dispatch($to, $data, $link);
            }

            return response()->json(['success' => true, 'message' => 'Loker telah berhasil dikosongkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data: ' . $e->getMessage(),
            ], 500);
        }
    }

// public function update(Request $req)
    // {
    //     $data = $req->input('data');
    //     if (! empty($data)) {
    //         // [x] Uncomment ketika selesai testing email
    //         foreach ($data as $item) {
    //             if ($item['checklistId'] !== 'on') {
    //                 $nik           = $item['nik'];
    //                 $alasan_keluar = $item['alasankeluar'];

//                 // $sebelumnya = DB::table('loker_master_user')
    //                 //     ->where('nik', $nik)->first();

//                 $sebelumnya = DB::table('loker_penghuni')
    //                     ->where('nik', $nik)->first();

//                 if ($sebelumnya != null) {
    //                     $data = [
    //                         'nik'                 => $sebelumnya->nik,
    //                         'no_loker'            => $sebelumnya->no_loker,
    //                         'status'              => 'OUT',
    //                         'keterangan'          => 'Sudah Keluar',
    //                         'nama_pengisi'        => auth()->user()->name ?? '',
    //                         'tgl_pengisi'         => date('Y-m-d'),
    //                         'nik_pengisi'         => auth()->user()->username ?? '',
    //                         'jam_pengisi'         => date('H:i:s'),
    //                         'penghuni_sebelumnya' => $sebelumnya->nama,
    //                         'alasan'              => $alasan_keluar,
    //                         'kode_area'           => $sebelumnya->kode_area ?? '',
    //                         'kode_blok'           => $sebelumnya->kode_blok ?? '',
    //                         'kode_rak'            => $sebelumnya->kode_rak ?? '',
    //                     ];

//                     DB::table('loker_user_transaksi')->insert($data);

//                     // DB::table('loker_master_nomer')
    //                     //     ->where([
    //                     //         'kode_blok' => $sebelumnya->kode_blok,
    //                     //         'no_loker' => $sebelumnya->no_loker,
    //                     //         'kode_area' => $sebelumnya->kode_area
    //                     //     ])->update(['status' => 0]);

//                     // DB::table('loker_master_user')->where('nik', $nik)
    //                     //     ->update([
    //                     //         'nik' => '',
    //                     //         'nama' => ''
    //                     //     ]);

//                     // TODO: Harusnya set is_active N dan tanggal keluar
    //                     DB::transaction(function () use ($nik) {
    //                         DB::table('loker_penghuni')->where('nik', $nik)->delete();
    //                     });
    //                 }

//                 HrKaryawan::where('id', $item['checklistId'])
    //                     ->update([
    //                         'out_complete' => $item['status'] == 'check' ? 'Y' : 'N',
    //                     ]);
    //             }
    //         }

//         $email_hr = User::whereHas('group.permissions', function ($query) {
    //             $query->where('codename', 'hr_connect_notified_out');
    //         })->select('email')
    //             ->whereNotNull('email')
    //             ->groupBy('email')
    //             ->get();

//         $to   = $email_hr->pluck('email')->toArray();
    //         $link = url('/hr-connect/dept-ga/karyawan-keluar');
    //         KaryawanKeluarSelesaiToHR::dispatch($to, $data, $link);

//         return response()->json(['success' => true, 'message' => 'Data berhasil dikirim.']);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.']);
    //     }
    // }

    // public function uploadExcel(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'excel_file' => 'required|file|mimes:xlsx,xls',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()->first()], 422);
    //     }

    //     if ($req->hasFile('excel_file')) {
    //         $file = $req->file('excel_file');

    //         if (! $file->isValid()) {
    //             return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
    //         }

    //         try {
    //             if ($file->getClientOriginalExtension() !== 'xlsx') {
    //                 return response()->json(['message' => 'Format file tidak valid. Hanya menerima file .xlsx.'], 400);
    //             }

    //             Excel::import(new GaShiftOut, $file);

    //             return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
    //         } catch (\Exception $e) {
    //             Log::error('Error during file upload: ' . $e->getMessage());

    //             return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
    //         }
    //     }

    //     return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    // }
    public function exportExcel(Request $request) {
        $byDate  = $request->input('tanggal');
        $showAll = $request->input('tampilkan_semua');

        return Excel::download(
            new KaryawanAktifExport($byDate, $showAll),
            'Data Karyawan - ' . ($byDate != null ? 'Per Tanggal ' . $byDate : 'Data Keseluruhan') . '.xlsx'
        );
    }

}
