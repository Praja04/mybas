<?php

namespace App\Http\Controllers;

use App\Department;
use App\Imports\KbbmImport;
use App\Kbbm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KbbmController extends Controller
{
    public function index()
    {
        $kbbms = Kbbm::all();
        return view('hr.kbbm', ['kbbms' => $kbbms]);
    }

    public function upload(Request $request)
    {
        Kbbm::truncate(); // Hapus seluruh data

        $excel = $request->file('file');
        Excel::import(new KbbmImport, $excel);

        return redirect('/hr/kbbm')->with('success', 'Upload Succeed');
    }

    public function formatTanggal($tanggal)
    {
        $tanggal = date('d/m/Y', strtotime($tanggal));
        return $tanggal;
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

        $kbbm = Kbbm::where('nik', $user->NIK)->first();

        if($kbbm != null) {
            $data['message'] = 'Belum Boleh Masuk';
            $data['tanggal_boleh_masuk'] = $kbbm->tanggal_masuk == '' ? 'Belum Ditentukan' : $this->formatTanggal($kbbm->tanggal_masuk);
            $data['dept'] = $kbbm->department;
            return response()->json([
                'success' => 0,
                'data' => $data
            ]);
        }

        $data['message'] = 'Silahkan Masuk';
        $data['tanggal_boleh_masuk'] = '';
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);

//        $data['message'] = '<strong style="color: red !important">ANDA TIDAK BOLEH MASUK</strong> Data tidak ada di system';
    }
}
