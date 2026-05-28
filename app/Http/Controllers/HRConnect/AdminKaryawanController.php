<?php
namespace App\Http\Controllers\HRConnect;

use App\AdminDepartment;
use App\Exports\HRConnect\Admin\TemplateCheckoutKaryawanExport;
use App\Exports\HRConnect\Admin\TemplatePlotKaryawanExport;
use App\HrKaryawan;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\AdmKaryawanKeluar;
use App\Imports\HRConnect\AdmKaryawanMasuk;
use App\Jobs\HRConnect\KaryawanKeluarToGA;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use App\Jobs\HRConnect\NotifiedOut;
use App\PKWAdmin;
use App\PKWGroup;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class AdminKaryawanController extends Controller
{
    public function getDataFloting()
    {
        $list_kode_admin = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_admin')->toArray();

        if (empty($list_kode_admin)) {
            return Datatables::of(HrKaryawan::whereRaw('1=0'))->make(true);
        }

        $hr = HrKaryawan::where([
            'in_kode_group' => 'N',
            'is_excuse_out' => 'N',
            'p_no'          => 'N',
        ])
            ->whereIn('kode_admin', $list_kode_admin)
            ->whereDate('tanggal_masuk', '>', '2024-09-30');

        return Datatables::of($hr)->make(true);
    }

    public function getDataOkb()
    {
        $username = auth()->user()->username;

        $isHrdIr = User::where('username', $username)
            ->whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })->exists();

        $hr = HrKaryawan::where([
            'in_kode_group' => 'Y',
            'is_excuse_out' => 'N',
            'p_no'          => 'N',
            'active'        => 'Y',
            'shutdown'      => 'N',
        ])->where('tanggal_masuk', '!=', '0000-00-00');

        if (! $isHrdIr) {
            $list_kode_bagian = AdminDepartment::where('nik_admin', $username)
                ->pluck('kode_bagian')
                ->toArray();

            if (empty($list_kode_bagian)) {
                return Datatables::of(HrKaryawan::whereRaw('1=0'))->make(true);
            }

            $hr->whereIn('kode_bagian', $list_kode_bagian);
        }

        return Datatables::of($hr)->make(true);
    }

    public function index()
    {
        $data['title']     = 'Admin - Karyawan';
        $data['pkw_group'] = PKWGroup::all();
        $data['pkw_admin'] = PKWAdmin::all();

        $data['hrd_ir'] = User::where('id', auth()->user()->id)
            ->whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })->exists();

        return view('hr-connect.admin.index', $data);
    }

    public function setGroupCode(Request $req)
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
                $id            = $item['idCheckwish'];
                $status_proses = $item['p_in'];
                $kode_group    = $item['kodeGroup'] ?? null;
                $kode_admin    = $item['kodeAdmin'] ?? null;

                $p_in = $status_proses === 'IN' ? 'Y' : 'N';
                $p_no = $status_proses === 'NO-IN' ? 'Y' : 'N';

                $updateData = [
                    'in_kode_group' => 'Y',
                    'p_in'          => $p_in,
                    'p_no'          => $p_no,
                ];

                if ($status_proses === 'IN') {
                    $updateData['kode_group']  = $kode_group;
                    $updateData['kode_admin']  = $kode_admin;
                } else {
                    $updateData['kode_group'] = null;
                    $updateData['kode_admin'] = null;
                }

                HrKaryawan::where('id', $id)
                    ->lockForUpdate()
                    ->update($updateData);
            }

            $to = User::where('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            DB::commit();

            if (! empty($to)) {
                KaryawanMasukToHR::dispatch($to, $data);
            }

            return response()->json([
                'success' => true,
                'msg'     => 'Berhasil memproses data!',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error proses Set Group Code Karyawan: ', $e->getMessage());
            return response()->json([
                'success' => false,
                'msg'     => 'Gagal memproses data!',
            ], 500);
        }
    }

    public function checkout(Request $req)
    {
        $data = $req->input('data');
        $link = route('ga.karyawan-keluar');

        $dataList = [
            'list_karyawan' => $data,
            'tautan'        => $link,
        ];

        if (empty($data)) {
            return response()->json([
                'message' => 'Data tidak valid atau keranjang kosong',
            ], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                HrKaryawan::where('nik', $item['nik'])
                    ->lockForUpdate()
                    ->update([
                        'tgl_shift_out'  => now()->format('Y-m-d'),
                        'is_excuse_out'  => 'Y',
                        'alasan_keluar'  => $item['alasan_keluar'],
                        'tanggal_keluar' => Carbon::parse($item['tanggal_keluar'])->format('Y-m-d'),
                    ]);
            }

            $emails = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            $email_ga = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ga');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            DB::commit();

            if (! empty($emails)) {
                NotifiedOut::dispatch($emails, $data);
            }

            if (! empty($email_ga)) {
                KaryawanKeluarToGA::dispatch($email_ga, $dataList);
            }
        } catch (\Throwable $e) {
            DB::rollback();

            Log::error('Error proses Checkout Karyawan: ', $e->getMessage());
            return response()->json([
                'message' => 'Gagal memproses data: Terjadi kesalahan pada sistem',
            ], 500);
        }

        // if (! empty($data)) {
        //     $email_ga = User::whereHas('group.permissions', function ($query) {
        //         $query->where('codename', 'hr_connect_ga');
        //     })->select('email')
        //         ->whereNotNull('email')
        //         ->groupBy('email')
        //         ->get();

        //     $link  = url('/hr-connect/dept-ga/karyawan-keluar');
        //     $to_ga = $email_ga->pluck('email')->toArray();
        //     // KaryawanKeluarToGA::dispatch($to_ga, $link);

        //     // Reminder besok nya jam 09:00
        //     // $reminder = Carbon::tomorrow()->setTime(9, 0, 0);
        //     // KaryawanKeluarToGA::dispatch($to_ga, $link)->delay($reminder);

        //     foreach ($data as $item) {
        //         $hrKaryawan = HrKaryawan::where('nik', $item['nik'])->first();

        //         if ($hrKaryawan) {
        //             $hrKaryawan->update([
        //                 'tgl_shift_out'  => date('Y-m-d'),
        //                 'is_excuse_out'  => 'Y',
        //                 'alasan_keluar'  => $item['alasan_keluar'],
        //                 'tanggal_keluar' => Carbon::parse($item['tanggal_keluar'])->format('Y-m-d'),
        //             ]);
        //         }
        //     }

        //     $emails = User::whereHas('group.permissions', function ($query) {
        //         $query->where('codename', 'hr_connect_notified_out');
        //     })->select('email')
        //         ->whereNotNull('email')
        //         ->groupBy('email')
        //         ->get();

        //     $to = $emails->pluck('email')->toArray();
        //     NotifiedOut::dispatch($to, $data);
        // }

        // return response()->json(['message' => 'Checkout berhasil'], 200);
    }

    public function templatePlotKaryawan()
    {
        $nama_file = 'Template Upload Karyawan Masuk.xlsx';

        return Excel::download(new TemplatePlotKaryawanExport, $nama_file);
    }

    public function uploadExcelKaryawanMasuk(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $file = $req->file('excel_file');

        if (! $file->isValid()) {
            return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
        }

        try {
            Excel::import(new AdmKaryawanMasuk, $file);

            return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
        } catch (\Throwable $e) {
            Log::error('Error Upload Excel Karyawan Masuk: ' . $e->getMessage());

            return response()->json([
                'message'      => 'Gagal mengimpor data. Pastikan format kolom Excel sesuai dengan template Info.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function templateCheckoutKaryawan()
    {
        $nama_file = 'Template Upload Karyawan Keluar.xlsx';

        return Excel::download(new TemplateCheckoutKaryawanExport, $nama_file);
    }

    public function uploadExcelKaryawanKeluar(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $file = $req->file('excel_file');

        if (! $file->isValid()) {
            return response()->json([
                'message' => 'File rusak atau gagal diunggah ke server',
            ], 400);
        }

        try {
            Excel::import(new AdmKaryawanKeluar, $file);

            return response()->json([
                'message' => 'Data berhasil diunggah dan diproses.',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error Upload Excel Karyawan Keluar: ' . $e->getMessage() . ' pada baris ' . $e->getLine());

            return response()->json([
                'message' => 'Gagal mengupload data. Pastikan format kolom Excel sesuai dengan template Info.',
            ], 500);
        }
    }
}
