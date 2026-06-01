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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;

class AdminKaryawanController extends Controller
{
    public function index()
    {
        $data['title']     = 'Admin - Karyawan';
        $data['pkw_group'] = PKWGroup::all();
        $data['pkw_admin'] = PKWAdmin::all();

        // Cek apakah user yang login adalah HRD IR (Untuk menyembunyikan tombol aksi di frontend)
        $data['hrd_ir'] = User::where('id', auth()->user()->id)
            ->whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })->exists();

        return view('hr-connect.admin.index', $data);
    }

    // ====================================================================
    // DATATABLES ENDPOINTS
    // ====================================================================

    public function getDataFloting()
    {
        $list_kode_admin = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_admin')->toArray();

        if (empty($list_kode_admin)) {
            return Datatables::of(HrKaryawan::whereRaw('1=0'))->make(true);
        }

        // OPTIMASI: Hanya select kolom yang benar-benar dipakai di UI biar RAM gak jebol
        $hr = HrKaryawan::select('id', 'nama', 'nik', 'kode_divisi', 'kode_bagian', 'kode_admin', 'kode_group', 'tanggal_masuk')
            ->where([
                'in_kode_group' => 'N',
                'is_excuse_out' => 'N',
                'p_no'          => 'N',
            ])
            ->whereIn('kode_admin', $list_kode_admin)
            ->whereDate('tanggal_masuk', '>', '2024-09-30');

        return Datatables::of($hr)
            ->addColumn('kode_group', function ($row) {
                return $row->kode_group;
            })
            ->make(true);
    }

    public function getDataOkb()
    {
        $username = auth()->user()->username;

        $isHrdIr = User::where('username', $username)
            ->whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_ir');
            })->exists();

        // OPTIMASI: Select kolom secukupnya
        $hr = HrKaryawan::select('id', 'nama', 'nik', 'kode_divisi', 'kode_bagian', 'kode_admin', 'kode_group')
            ->where([
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

    public function setGroupCode(Request $req)
    {
        $data = $req->input('data');

        if (empty($data)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang diproses.'], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $status_proses = $item['p_in'];
                $p_in          = $status_proses === 'IN' ? 'Y' : 'N';
                $p_no          = $status_proses === 'NO-IN' ? 'Y' : 'N';

                $updateData = [
                    'in_kode_group' => 'Y',
                    'p_in'          => $p_in,
                    'p_no'          => $p_no,
                ];

                if ($status_proses === 'IN') {
                    $updateData['kode_group'] = $item['kodeGroup'] ?? null;
                    $updateData['kode_admin'] = $item['kodeAdmin'] ?? null;
                } else {
                    $updateData['kode_group'] = null;
                    $updateData['kode_admin'] = null;
                }

                HrKaryawan::where('id', $item['idCheckwish'])
                    ->lockForUpdate()
                    ->update($updateData);
            }

            $to = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })
                ->whereNotNull('email')
                ->pluck('email')
                ->unique()
                ->toArray();

            DB::commit();

            if (! empty($to)) {
                KaryawanMasukToHR::dispatch($to, $data);
            }

            return response()->json(['success' => true, 'msg' => 'Berhasil memproses data!']);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error proses Set Group Code Admin: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Gagal memproses data!'], 500);
        }
    }

    public function checkout(Request $req)
    {
        $data = $req->input('data');

        if (empty($data)) {
            return response()->json(['message' => 'Data tidak valid atau keranjang kosong'], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                // DEFENSIVE PROGRAMMING: Amankan tanggal dan alasan kosong
                $tglKeluar = ! empty($item['tanggal_keluar'])
                    ? Carbon::parse($item['tanggal_keluar'])->format('Y-m-d')
                    : null;

                $alasanKeluar = ! empty($item['alasan_keluar'])
                    ? $item['alasan_keluar']
                    : 'Tidak ada keterangan'; // Teks Default sesuai request lu

                HrKaryawan::where('nik', $item['nik'])
                    ->lockForUpdate()
                    ->update([
                        'tgl_shift_out'  => now()->format('Y-m-d'),
                        'is_excuse_out'  => 'Y',
                        'alasan_keluar'  => $alasanKeluar,
                        'tanggal_keluar' => $tglKeluar,
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

            // Dispatch Jobs
            if (! empty($emails)) {
                NotifiedOut::dispatch($emails, $data);
            }

            if (! empty($email_ga)) {
                $dataList = [
                    'list_karyawan' => $data,
                    'tautan'        => route('ga.karyawan-keluar'),
                ];
                KaryawanKeluarToGA::dispatch($email_ga, $dataList);
            }

            return response()->json(['message' => 'Checkout berhasil diproses'], 200);

        } catch (\Throwable $e) {
            DB::rollback();
            Log::error('Error proses Checkout Karyawan Admin: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses data: Terjadi kesalahan pada sistem'], 500);
        }
    }

    // ====================================================================
    // UPLOAD & EXCEL ENDPOINTS
    // ====================================================================

    public function templatePlotKaryawan()
    {
        return Excel::download(new TemplatePlotKaryawanExport, 'Template Upload Karyawan Masuk.xlsx');
    }

    public function uploadExcelKaryawanMasuk(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        if ($validator->fails() || ! $req->file('excel_file')->isValid()) {
            return response()->json(['message' => $validator->errors()->first() ?? 'File tidak valid atau rusak.'], 422);
        }

        try {
            Excel::import(new AdmKaryawanMasuk, $req->file('excel_file'));
            return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
        } catch (\Throwable $e) {
            Log::error('Error Upload Excel Karyawan Masuk: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengimpor data. Pastikan format kolom Excel sesuai dengan template.'], 500);
        }
    }

    public function templateCheckoutKaryawan()
    {
        return Excel::download(new TemplateCheckoutKaryawanExport, 'Template Upload Karyawan Keluar.xlsx');
    }

    public function uploadExcelKaryawanKeluar(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480',
        ]);

        if ($validator->fails() || ! $req->file('excel_file')->isValid()) {
            return response()->json(['message' => $validator->errors()->first() ?? 'File tidak valid atau rusak.'], 422);
        }

        try {
            Excel::import(new AdmKaryawanKeluar, $req->file('excel_file'));
            return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
        } catch (\Throwable $e) {
            Log::error('Error Upload Excel Karyawan Keluar: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengupload data. Pastikan format kolom Excel sesuai dengan template.'], 500);
        }
    }
}
