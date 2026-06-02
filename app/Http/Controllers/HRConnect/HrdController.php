<?php
namespace App\Http\Controllers\HRConnect;

use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\HrdIrKaryawanKeluar;
use App\Jobs\HRConnect\KaryawanKeluarSelesaiToHR;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class HrdController extends Controller
{
    public function index()
    {
        $data['title'] = 'HRD IR - Karyawan Keluar';

        $data['tanggalTersedia'] = HrKaryawan::where('is_excuse_out', 'Y')
            ->where('checked_ir', 'N')
            ->whereNotNull('tgl_shift_out')
            ->select('tgl_shift_out')
            ->distinct()
            ->orderBy('tgl_shift_out', 'desc')
            ->pluck('tgl_shift_out');

        return view('hr-connect.hrd.karyawan-keluar', $data);
    }

    public function report()
    {
        $data['title'] = 'HRD IR - Report Karyawan Keluar';

        $data['kodeDivisi'] = HrKaryawan::whereNotNull('kode_divisi')->where('kode_divisi', '!=', '')->distinct()->pluck('kode_divisi')->toArray();
        $data['kodeBagian'] = HrKaryawan::whereNotNull('kode_bagian')->where('kode_bagian', '!=', '')->distinct()->pluck('kode_bagian')->toArray();
        $data['kodeGroup']  = HrKaryawan::whereNotNull('kode_group')->where('kode_group', '!=', '')->distinct()->pluck('kode_group')->toArray();

        // $data['tanggalTersedia'] = HrKaryawan::where('checked_ir', 'Y')
        //     ->whereNotNull('tanggal_keluar')
        //     ->where('tanggal_keluar', '!=', '0000-00-00')
        //     ->select('tanggal_keluar')
        //     ->distinct()
        //     ->orderBy('tanggal_keluar', 'desc')
        //     ->pluck('tanggal_keluar')
        //     ->toArray();

        return view('hr-connect.hrd.report-karyawan-keluar', $data);
    }

    public function restore()
    {
        $data['title'] = 'HRD IR - Pemulihan Data';
        return view('hr-connect.hrd.restore', $data);
    }

    public function getFilterBulanTahunFinalisasi(Request $req)
    {
        $search = $req->input('q');
        $page   = $req->input('page', 1);

        $allMonths = Cache::remember('list_bulan_finalisasi_hrd', now()->addHours(24), function () {
            return HrKaryawan::select(
                DB::raw("DATE_FORMAT(tanggal_keluar, '%Y-%m') as id_bulan"),
                DB::raw("DATE_FORMAT(tanggal_keluar, '%M %Y') as text")
            )
                ->where([
                    'in_kode_group' => 'Y',
                    'is_excuse_out' => 'Y',
                    'out_complete'  => 'Y',
                    'checked_ir'    => 'Y',
                ])
                ->whereNotNull('tanggal_keluar')
                ->where('tanggal_keluar', '!=', '0000-00-00')
                ->distinct()
                ->orderBy('id_bulan', 'desc')
                ->get()
                ->map(function ($month) {
                    return (object) [
                        'id'   => $month->id_bulan,
                        'text' => $month->text,
                    ];
                });
        });

        if (! empty($search)) {
            $allMonths = $allMonths->filter(function ($item) use ($search) {
                return stripos(strtolower($item->text), strtolower($search)) !== false;
            })->values();
        }

        $perPage = 10;
        $offset  = ($page - 1) * $perPage;
        $items   = $allMonths->slice($offset, $perPage)->values();

        return response()->json([
            'results'    => $items,
            'pagination' => [
                'more' => ($offset + $perPage) < $allMonths->count(),
            ],
        ]);
    }

    public function getData(Request $req)
    {
        $hr = HrKaryawan::select('id', 'nama', 'nik', 'kode_divisi', 'kode_bagian', 'kode_group', 'tanggal_keluar', 'alasan_keluar', 'tgl_shift_out')
            ->where([
                'is_excuse_out' => 'Y',
                'out_complete'  => 'Y',
                'checked_ir'    => 'N',
            ]);

        if ($req->tampilkan_semua == 0 && ! empty($req->tanggal)) {
            $hr->where('tgl_shift_out', $req->tanggal);
        }

        $hr->orderBy('tgl_shift_out', 'desc');

        return Datatables::of($hr)->make(true);
    }

    public function getDataReport(Request $req)
    {
        $hr = HrKaryawan::select('id', 'nama', 'nik', 'kode_divisi', 'kode_bagian', 'kode_group', 'tanggal_keluar', 'alasan_keluar', 'tgl_shift_out', 'p_in', 'p_no')
            ->where([
                'in_kode_group' => 'Y',
                'is_excuse_out' => 'Y',
                'out_complete'  => 'Y',
                'checked_ir'    => 'Y',
            ])
            ->whereNotNull('tanggal_keluar')
            ->where('tanggal_keluar', '!=', '0000-00-00');

        if (! empty($req->divisi)) {$hr->where('kode_divisi', $req->divisi);}
        if (! empty($req->bagian)) {$hr->where('kode_bagian', $req->bagian);}
        if (! empty($req->group)) {$hr->where('kode_group', $req->group);}

        if (! empty($req->tanggal)) {
            $parts = explode('-', $req->tanggal);

            if (count($parts) == 2) {
                $hr->whereYear('tanggal_keluar', $parts[0])
                    ->whereMonth('tanggal_keluar', $parts[1]);
            }
        }

        $hr->orderBy('tanggal_keluar', 'desc');

        return Datatables::of($hr)
            ->addColumn('status_in', function ($row) {
                if ($row->p_no == 'Y') {
                    return 'NO-IN';
                } elseif ($row->p_in == 'Y') {
                    return 'IN';
                }
                return 'BELUM DISET';
            })
            ->make(true);
    }

    public function getDataPemulihan(Request $req)
    {
        $data = HrKaryawan::select('id', 'nama', 'nik', 'kode_bagian', 'kode_group', 'tanggal_keluar', 'alasan_keluar', 'p_no', 'tgl_shift_out')
            ->whereNotNull('alasan_keluar')
            ->orWhere('p_no', 'Y')
            ->get();

        return response()->json(['data' => $data]);
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
            $ids = [];

            foreach ($data as $item) {
                if (isset($item['checklistId']) && $item['checklistId'] !== 'on') {
                    HrKaryawan::where('id', $item['checklistId'])->lockForUpdate()->update([
                        'checked_ir' => $item['status'] == 'check' ? 'Y' : 'N',
                    ]);

                    $ids[] = $item['checklistId'];
                }
            }

            $karyawan = HrKaryawan::whereIn('id', $ids)->get();

            $email_hr = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            Cache::forget('list_bulan_finalisasi_hrd');

            DB::commit();

            if (! empty($email_hr) && $karyawan->isNotEmpty()) {
                $link = url('/hr-connect/dept-hrd/karyawan-keluar');
                KaryawanKeluarSelesaiToHR::dispatch($email_hr, $karyawan, $link);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil divalidasi dan disimpan.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Update Karyawan Keluar HRD IR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses data: Terjadi kesalahan sistem.',
            ], 500);
        }
    }

    public function restore_data(Request $req)
    {
        if (empty($req->nik)) {
            return response()->json([
                'success' => false,
                'message' => 'NIK tidak valid',
            ], 400);
        }

        DB::beginTransaction();

        try {
            HrKaryawan::where('nik', $req->nik)
                ->lockForUpdate()
                ->update([
                    'tgl_shift_out'  => null,
                    'is_excuse_out'  => 'N',
                    'alasan_keluar'  => null,
                    'tanggal_keluar' => null,
                    'p_no'           => 'N',
                    'checked_ir'     => 'N',
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dipulihkan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Restore Data HRD IR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan data: Terjadi kesalahan sistem.',
            ], 500);
        }
    }

    public function uploadExcel(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $file = $req->file('excel_file');

        if (! $file->isValid()) {
            return response()->json(['message' => 'File rusak atau gagal diunggah ke server.'], 400);
        }

        try {
            Excel::import(new HrdIrKaryawanKeluar, $file);
            return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
        } catch (\Throwable $e) {
            Log::error('Error Upload Excel HRD IR Karyawan Keluar: ' . $e->getMessage());
            return response()->json([
                'message'      => 'Gagal mengimpor data. Pastikan format Excel sesuai dengan template.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    // public function getDataPemulihan(Request $req)
    // {
    //     $data = HrKaryawan::whereNotNull('alasan_keluar')
    //         ->orWhere('p_no', 'Y')->get();

    //     return response()->json(['data' => $data]);
    // }

    // public function getData(Request $req)
    // {
    //     $hr = HrKaryawan::where([
    //         // 'in_complete' => 'Y',
    //         // 'in_kode_group' => 'Y',
    //         'is_excuse_out' => 'Y',
    //         'checked_ir' => 'N'
    //     ]);

    //     if ($req->tampilkan_semua == 0) {
    //         $hr->where([
    //             'tgl_shift_out' => $req->tanggal
    //         ]);
    //     }

    //     $hr->orderBy('tanggal_keluar', 'desc');

    //     return Datatables::of($hr)->make(true);
    // }

    // public function getDataReport(Request $req)
    // {
    //     $hr = HrKaryawan::where([
    //         // 'in_complete' => 'Y',
    //         'in_kode_group' => 'Y',
    //         'is_excuse_out' => 'Y',
    //         'checked_ir' => 'Y'
    //     ]);

    //     if ($req->tanggal != 'semua') {
    //         $hr->where('tgl_shift_out', $req->tanggal);
    //     }

    //     $hr->orderBy('tanggal_keluar', 'desc');

    //     return Datatables::of($hr)->make(true);
    // }

    // public function index()
    // {
    //     $data['title'] = 'HRD IR - Karyawan Keluar';

    //     return view('hr-connect.hrd.karyawan-keluar', $data);
    // }

    // public function report()
    // {
    //     $data['title'] = 'HRD IR - Report Karyawan Keluar';

    //     return view('hr-connect.hrd.report-karyawan-keluar', $data);
    // }

    // public function restore()
    // {
    //     $data['title'] = 'HRD IR - Pemulihan Data';

    //     return view('hr-connect.hrd.restore', $data);
    // }

    // public function restore_data(Request $req)
    // {
    //     $hrKaryawan = HrKaryawan::where('nik', $req->nik);

    //     $hrKaryawan->update([
    //         'tgl_shift_out' => null,
    //         'is_excuse_out' => 'N',
    //         'alasan_keluar' => null,
    //         'tanggal_keluar' => null,
    //         'p_no' => 'N'
    //     ]);

    //     return response()->json(['message' => 'Data berhasil dipulihkan.']);
    // }

    // // public function update(Request $req)
    // // {
    // //     $data = $req->input('data');

    // //     if(!empty($data)){
    // //         foreach ($data as $item) {
    // //             if($item['checklistId'] !== 'on'){
    // //                 HrKaryawan::where('id', $item['checklistId'])->update([
    // //                     'checked_ir' => $item['status'] == 'check' ? 'Y' : 'N'
    // //                 ]);
    // //             }
    // //         }

    // //         $email_hr = User::whereHas('group.permissions', function ($query) {
    // //             $query->where('codename', 'hr_connect_ir');
    // //         })->select('email')
    // //         ->whereNotNull('email')
    // //         ->groupBy('email')
    // //         ->get();

    // //         $to = $email_hr->pluck('email')->toArray();
    // //         $link = url('/hr-connect/dept-hrd/karyawan-keluar');
    // //         KaryawanKeluarSelesaiToHR::dispatch($to, $data, $link);

    // //         return response()->json(['success' => true, 'message' => 'Data berhasil dikirim.']);
    // //     }else {
    // //         return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.']);
    // //     }
    // // }

    // public function update(Request $req)
    // {
    //     $data = $req->input('data');

    //     if (!empty($data)) {
    //         $ids = []; // tampung ID karyawan yg diupdate

    //         foreach ($data as $item) {
    //             if ($item['checklistId'] !== 'on') {
    //                 HrKaryawan::where('id', $item['checklistId'])->update([
    //                     'checked_ir' => $item['status'] == 'check' ? 'Y' : 'N'
    //                 ]);
    //                 $ids[] = $item['checklistId'];
    //             }
    //         }

    //         // ambil data lengkap dari tabel hr_karyawan
    //         $karyawan = HrKaryawan::whereIn('id', $ids)->get();

    //         $email_hr = User::whereHas('group.permissions', function ($query) {
    //             $query->where('codename', 'hr_connect_ir');
    //         })->select('email')
    //             ->whereNotNull('email')
    //             ->groupBy('email')
    //             ->get();

    //         $to = $email_hr->pluck('email')->toArray();
    //         $link = url('/hr-connect/dept-hrd/karyawan-keluar');

    //         // lempar data karyawan lengkap, bukan array mentah
    //         KaryawanKeluarSelesaiToHR::dispatch($to, $karyawan, $link);

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

    //         if (!$file->isValid()) {
    //             return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
    //         }

    //         try {
    //             if ($file->getClientOriginalExtension() !== 'xlsx') {
    //                 return response()->json(['message' => 'Format file tidak valid. Hanya menerima file .xlsx.'], 400);
    //             }

    //             Excel::import(new HrdIrKaryawanKeluar, $file);

    //             return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
    //         } catch (\Exception $e) {
    //             \Log::error('Error during file upload: ' . $e->getMessage());

    //             return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
    //         }
    //     }

    //     return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    // }
}
