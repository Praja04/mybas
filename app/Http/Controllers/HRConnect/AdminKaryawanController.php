<?php

namespace App\Http\Controllers\HRConnect;

use App\User;
use App\PKWAdmin;
use App\PKWGroup;
use Carbon\Carbon;
use App\HrKaryawan;
use App\AdminDepartment;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Jobs\HRConnect\NotifiedOut;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\AdmKaryawanKeluar;
use App\Imports\HRConnect\AdmKaryawanMasuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\HRConnect\KaryawanMasukToHR;
use Illuminate\Support\Facades\Validator;
use App\Jobs\HRConnect\KaryawanKeluarToGA;
use App\Mail\HRConnect\AttachExcelToHRMail;
use App\Mail\HRConnect\FyiGaShiftingOutMail;

class AdminKaryawanController extends Controller
{
    public function getDataFloting()
    {
        $kode_bagian = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_bagian');
        $kode_admin = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_admin');

        $hr = HrKaryawan::where([
            // 'in_complete' => 'N', // Paralel sama GA
            'in_kode_group' => 'N',
            'is_excuse_out' => 'N',
            'p_no' => 'N',
        ])
        // ->whereIn('kode_bagian', $kode_bagian)
        ->whereIn('kode_admin', $kode_admin)
        ->whereDate('tanggal_masuk', '>', '2024-09-30');

        return Datatables::of($hr)->make(true);
    }
    
    public function getDataOkb()
    {
        $kode_bagian = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_bagian');
        $kode_admin = AdminDepartment::where('nik_admin', auth()->user()->username)->pluck('kode_admin');

        $hr = HrKaryawan::where([
                // 'in_complete' => 'Y', // Paralel sama GA 
                'in_kode_group' => 'Y',
                'is_excuse_out' => 'N',
                'p_no' => 'N',
            ])
            ->where('tanggal_masuk', '!=', '0000-00-00')
            ->where('active', 'Y')
            ->where('shutdown', 'N');

        $hrd_ir = User::where('username', auth()->user()->username)
            ->whereHas('group.permissions', function ($query) {              
                $query->where('codename', 'hr_connect_ir');  
                
            })->exists();
            
        if(!$hrd_ir){
            $hr->whereIn('kode_bagian', $kode_bagian);
            // $hr->whereIn('kode_admin', $kode_admin);
        }
                            
        return Datatables::of($hr)->make(true);
    }
    
    public function index()
    {
        $data['title'] = 'Admin - Karyawan';
        $data['pkw_group'] = PKWGroup::all();
        $data['pkw_admin'] = PKWAdmin::all();
        
        $data['hrd_ir'] = User::where('username', auth()->user()->username)
            ->whereHas('group.permissions', function ($query) {              
                $query->where('codename', 'hr_connect_ir');  
            })->exists();

        return view('hr-connect.admin.index', $data);
    }

    public function setGroupCode(Request $req)
    {
        $data = $req->input('data');

        if(!empty($data)){
            foreach($data as $item){
                $id = $item['idCheckwish'];
                $kode_group = $item['kodeGroup'];
                $kode_admin = $item['kodeAdmin'];
                $status_proses = $item['p_in'];
                $p_in = $status_proses === "IN" ? "Y" : "N";
                $p_no = $status_proses === "NO-IN" ? "Y" : "N";

                $isNoProcess = [
                    // 'in_complete' => 'Y', // Paralel sama GA
                    'in_kode_group' => 'Y',
                    'p_in' => $p_in,
                    'p_no' => $p_no,
                    // 'tanggal_masuk' => NULL
                ];

                $isProcess = [
                    'kode_group' => $kode_group,
                    'kode_admin' => $kode_admin,
                    // 'in_complete' => 'Y', // Paralel sama GA
                    'in_kode_group' => 'Y',
                    'p_in' => $p_in,
                    'p_no' => $p_no,
                    // 'tanggal_masuk' => Carbon::parse(now())->format('Y-m-d')
                ];

                $status_proses == "NO-IN" ? HrKaryawan::where('id', $id)->update($isNoProcess) : HrKaryawan::where('id', $id)->update($isProcess);
            }
            
            // [x] Excel berantakan
            $email_hr_karyawan = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_in');
            })->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->get();
            
            $to = $email_hr_karyawan->pluck('email')->toArray();
            KaryawanMasukToHR::dispatch($to, $data);
        }

        return response()->json(['msg' => 'Berhasil memperbarui set group!']);
    }

    public function checkout(Request $req)
    {
        $data = $req->input('data');

        if(!empty($data)){
            $email_ga = User::whereHas('group.permissions', function ($query) {              
                $query->where('codename', 'hr_connect_ga');  
            })->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->get(); 
            
            $link = url('/hr-connect/dept-ga/karyawan-keluar');
            $to_ga = $email_ga->pluck('email')->toArray();
            // KaryawanKeluarToGA::dispatch($to_ga, $link); 
            
            // Reminder besok nya jam 09:00 
            // $reminder = Carbon::tomorrow()->setTime(9, 0, 0); 
            // KaryawanKeluarToGA::dispatch($to_ga, $link)->delay($reminder);  
            
            foreach ($data as $item) {
                $hrKaryawan = HrKaryawan::where('nik', $item['nik'])->first();

                if ($hrKaryawan) {
                    $hrKaryawan->update([
                        'tgl_shift_out' => date('Y-m-d'),
                        'is_excuse_out' => 'Y',
                        'alasan_keluar' => $item['alasan_keluar'],
                        'tanggal_keluar' => Carbon::parse($item['tanggal_keluar'])->format('Y-m-d')
                    ]);
                }
            }
            
            $emails = User::whereHas('group.permissions', function ($query) {
                $query->where('codename', 'hr_connect_notified_out');
            })->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->get();

            $to = $emails->pluck('email')->toArray();
            NotifiedOut::dispatch($to, $data);
        }

        return response()->json(['message' => 'Checkout berhasil'], 200);
    }

    public function uploadExcelKaryawanMasuk(Request $req)
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

                Excel::import(new AdmKaryawanMasuk, $file);

                return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
            } catch (\Exception $e) {
                \Log::error('Error during file upload: ' . $e->getMessage());

                return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
            }
        }

        return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    }

    public function uploadExcelKaryawanKeluar(Request $req)
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

                Excel::import(new AdmKaryawanKeluar, $file);

                return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
            } catch (\Exception $e) {
                \Log::error('Error during file upload: ' . $e->getMessage());

                return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data.'], 500);
            }
        }

        return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    }
}
