<?php

namespace App\Http\Controllers;

use App\CekSuhu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScanSuhuExport;

class CekSuhuController extends Controller
{
    public function display()
    {
        return view('display.cek-suhu');
    }

    public function scan(Request $request)
    {
        $data = [];

        // Untuk antisipaso
        $rfid = (int)$request->rfid;

        $employee = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK','EMPNM')
            ->where(['CARDNODEVICE' => $rfid ])
            // ->orderBy('CREATEDON', 'desc')
            ->orderByRaw('SUBSTR(NIK, 8) desc')
            ->first();

        // Cek apakah ada data nya ada apa engga di server security
        if($employee == null || $rfid == 0)
        {
            // Kalau ga ada maka langsung return error
            $data['image'] = 'default';
            $data['message'] = 'Kartu tidak terdaftar di system';
            return response()->json(['success' => 0, 'data' => $data]);
        }

        $user = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE','NIK','EMPNM','DEPTID','FOTOBLOB')
            ->where(['NIK' => $employee->NIK, 'CARDNODEVICE' => $rfid ])
            ->first();

        $data['idcard']  = $user->CARDNODEVICE;
        $data['nik']     = $user->NIK;
        $data['name']    = $user->EMPNM;
        $data['dept']    = $user->DEPTID;
        $data['image']   = base64_encode($user->FOTOBLOB);

        $data['message'] = 'Data Ditemukan';

        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }

    public function submit(Request $request)
    {
        // Gete department name
        $employee = DB::connection('payroll_non_staff')
            ->table('masteremployee')
            ->select(DB::raw('`Kode Bagian` AS dept'))
            ->where('Aktif', '1')
            ->where('Endda', '9998-12-31')
            ->where('Kode Periode', '!=', 'Non Payroll')
            ->where('Kode Kontrak', '!=', 'Staff Permanen')
            ->where('nip', $request->nik)
            ->first();
        $jenis_karyawan = 'non-staff';
        if(is_null($employee)) {
            $jenis_karyawan = 'staff';
            $employee = DB::connection('payroll_staff')
                ->table('masteremployee')
                ->select(DB::raw('`Kode Bagian` AS dept'))
                ->where('Aktif', '1')
                ->where('Endda', '9998-12-31')
                ->where('nip', $request->nik)
                ->first();
        }
        $cek_suhu = new CekSuhu;
        $cek_suhu->tanggal = date('Y-m-d');
        $cek_suhu->dept = $employee->dept;
        $cek_suhu->jenis_karyawan = $jenis_karyawan;
        $cek_suhu->nik = $request->nik;
        $cek_suhu->nama = $request->nama;
        $cek_suhu->suhu = $request->suhu.'.'.$request->suhu_belakang_koma;
        $cek_suhu->waktu_scan = date('Y-m-d H:i:s');
        $cek_suhu->save();
        return response()->json(['success' => 1, 'message' => 'Submit Succeed']);
    }

    public function reportExportExcel($tanggal)
    {
        return Excel::download(new ScanSuhuExport($tanggal), 'SCAN-SUHU-'.$tanggal.time().'.xls');
    }

    public function report($tanggal = '',$divisi = '',$shift = '')
    {
        $tanggal = $tanggal == '' ? date('Y-m-d') : $tanggal;
        $divisi = $divisi == '' ? 'PRO' : $divisi;
        $shift = $shift == '' ? 'S1' : $shift;
            $filter = [
            $tanggal,
            $divisi,
            $shift
        ];
        $suhu_ok = [];
        $suhu_not_ok = [];
        $belum_scan = [];
        $bagian = [];
        $jumlahKaryawan = [];
        $divisiData = DB::connection('payroll_non_staff')->table('masterdivisi')->get();
        $jumlahKaryawanData = DB::connection('payroll_non_staff')
            ->table('masteremployee')
            ->join('jadwal', 'masteremployee.Kode Group', '=', 'jadwal.Kode Group')
            ->join('masterbagian', 'masteremployee.Kode Bagian', '=', 'masterbagian.Kode Bagian')
            ->select(DB::raw('COUNT(masteremployee.`Kode Bagian`) AS jumlah, masterbagian.`Nama Bagian` AS dept'))
            ->where('Aktif', '1')
            ->where('masteremployee.Endda', '9998-12-31')
            ->where('Kode Periode', '!=', 'Non Payroll')
            ->where('Kode Kontrak', '!=', 'Staff Permanen')
            ->where('masteremployee.Kode Divisi', $divisi)
            ->where('jadwal.Tgl', $tanggal)
            ->where('jadwal.Kode Shift', $shift)
            ->groupBy('masteremployee.Kode Bagian')
            ->orderBy('masteremployee.Kode Bagian', 'asc')
            ->get();
        foreach($jumlahKaryawanData as $b)
        {
            // Jumlah yang sudah scan
            $ok = CekSuhu::where('tanggal', date('Y-m-d'))
                ->where('jenis_karyawan', 'non-staff')
                ->where('suhu', '<', 37.3)
                ->where('tanggal', $tanggal)
                ->where('dept', $b->dept)
                ->count();
            $not_ok = CekSuhu::where('tanggal', date('Y-m-d'))
                ->where('jenis_karyawan', 'non-staff')
                ->where('suhu', '>=', 37.3)
                ->where('tanggal', $tanggal)
                ->where('dept', $b->dept)
                ->count();
            $suhu_ok[] = $ok;
            $suhu_not_ok[] = $not_ok;
            $bagian[] = $b->dept;
            $jumlahKaryawan[] = $b->jumlah;
            $belum_scan[] = (int)$b->jumlah - ((int)$ok+(int)$not_ok);
        }

        $where_per_department = [];
        $where_per_department[] = ['dept', Auth::user()->department->name];
        if($tanggal != '') {
            $where_per_department[] = ['tanggal', $tanggal];   
        }
        $data_per_department = CekSuhu::where($where_per_department)->get();

        // dd($data_per_department);
        return view('cek-suhu.report',[
            'bagian' => $bagian,
            'jumlah_karyawan' => $jumlahKaryawan,
            'ok' => $suhu_ok,
            'not_ok' => $suhu_not_ok,
            'belum_scan' => $belum_scan,
            'divisi' => $divisiData,
            'filter' => $filter,
            'data_per_department' => $data_per_department
        ]);
    }
}
