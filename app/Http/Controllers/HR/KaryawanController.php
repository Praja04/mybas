<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HR\Karyawan;
use App\Models\HR\KaryawanUpdate;
use App\Models\HR\KaryawanUpdateBatch;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index()
    {
        return view('hr.karyawan.index');
    }

    public function all()
    {
        $karyawan = Karyawan::select('*');

        return Datatables::of($karyawan)->make(true);
    }

    public function compareData()
    {
        // Get data from payroll
        $payroll_array = [];
        $data_payroll_non_staff = DB::connection('payroll_non_staff')
        ->table('masteremployee')
        ->select(DB::raw('Nip as nik'), DB::raw('Nama as name'))
        ->where('Endda', '9998-12-31')
        ->where('Kode Periode', 'Bulanan')
        ->where('Alasan Keluar', '')
        ->get();

        foreach($data_payroll_non_staff as $dp)
        {
            $payroll_array[] = $dp->nik;
        }

        // Get data local
        $local_array = [];
        $data_local_non_staff = Karyawan::select('nik', DB::raw('nama as name'))->where('active', 'Y')->get();

        foreach($data_local_non_staff as $dl)
        {
            $local_array[] = $dl->nik;
        }

        $nik_ada = [];
        $semua_nik = [];
        foreach($payroll_array as $dp)
        {
            $semua_nik[] = $dp; 
            if(in_array($dp, $local_array))
            {
                $nik_ada[] = $dp;
            }
        }

        // dd(in_array( [
        //     "nik" => "010104-11647",
        //     "name" => "Hei"
        // ] ,$payroll_array));

        // dd(in_array)

        // Do compare
        $difference = array_diff($semua_nik, $nik_ada);

        return response()->json([
            'data_payroll_count' => count($data_payroll_non_staff),
            'data_local_count' => count($data_local_non_staff),
            'difference' => $difference,
            'difference_count' => count($difference)
        ]);
    }

    public function syncronizeData()
    {
        $payroll_array = [];
        // Get data from payroll
        $data_payroll_non_staff = DB::connection('payroll_non_staff')
        ->table('masteremployee')
        ->select('*')
        ->where('Endda', '9998-12-31')
        ->where('Kode Periode', 'Bulanan')
        ->where('Alasan Keluar', '')
        ->get();

        foreach($data_payroll_non_staff as $key => $dp)
        {
            $payroll_array[] = $dp->{'Nip'};
            $this->storeLocalData($dp);
        }

        $local_array = [];
        $data_local_non_staff = Karyawan::select('nik', DB::raw('nama as name'))->where('active', 'Y')->get();

        foreach($data_local_non_staff as $dl)
        {
            $local_array[] = $dl->nik;
        }

        $difference = array_diff($local_array, $payroll_array);

        // Change the differences to status from active Y to N
        foreach($difference as $nik)
        {
            Karyawan::where('nik', $nik)->update(['active' => 'N']);
        }

        return response()->json(['success' => 1, 'message' => 'Syncronize data succeed']);
    }

    private function storeLocalData($user)
    {
        // Check if record does not alredy exist
        $cek_record = Karyawan::where('nik', $user->{'Nip'})->first();
        if($cek_record == null) {
            $karyawan = new Karyawan;
            $karyawan->nik                      = $user->{'Nip'};
            $karyawan->nama                     = $user->{'Nama'};
            $karyawan->agama                    = $user->{'Agama'};
            $karyawan->jenis_kelamin            = $user->{'Jenis Kelamin'};
            $karyawan->tempat_lahir             = $user->{'Tempat Lahir'};
            $karyawan->tanggal_lahir            = $user->{'Tgl Lahir'};
            $karyawan->tanggal_masuk            = $user->{'Tgl Masuk'};
            $karyawan->status_perdata           = $user->{'Status Nikah'};
            $karyawan->nama_pasangan            = $user->{'Nama Pasangan'};
            // $karyawan->tempat_pernikahan        = '';
            // $karyawan->tanggal_pernikahan       = '';
            // $karyawan->tempat_lahir_pasangan    = '';
            // $karyawan->tanggal_lahir_pasangan   = '';
            // $karyawan->pekerjaan_pasangan       = '';
            // $karyawan->tempat_pasangan_bekerja  = '';
            $karyawan->nama_ayah                = $user->{'Nama Ayah'};
            $karyawan->tempat_lahir_ayah        = $user->{'Tempat Lahir Ayah'};
            $karyawan->tanggal_lahir_ayah       = $user->{'Tanggal Lahir Ayah'};
            $karyawan->nama_ibu                 = $user->{'Nama Ibu'};
            $karyawan->tempat_lahir_ibu         = $user->{'Tempat Lahir Ibu'};
            $karyawan->tanggal_lahir_ibu        = $user->{'Tanggal Lahir Ibu'};
            // $karyawan->nama_ayah_mertua         = '';
            // $karyawan->tempat_lahir_ayah_mertua     = '';
            // $karyawan->tanggal_lahir_ayah_mertua    = '';
            // $karyawan->nama_ibu_mertua              = '';
            // $karyawan->tempat_lahir_ibu_mertua      = '';
            // $karyawan->tanggal_lahir_ibu_mertua     = '';
            $karyawan->nama_kontak_darurat          = '';
            $karyawan->hubungan_kontak_darurat      = '';
            $karyawan->no_telepon_kontak_darurat    = '';
            $karyawan->nomor_rekening_bank                  = $user->{'No Rekening'};
            // $karyawan->nomor_kartu_bpjs_ketenagakerjaan     = '';
            $karyawan->keterangan_kartu_bpjs_ketenagakerjaan    = '';
            // $karyawan->nomor_kartu_bpjs_kesehatan           = '';
            $karyawan->keterangan_kartu_bpjs_kesehatan          = '';
            $karyawan->nik_ktp          = $user->{'No KTP'};
            // $karyawan->no_kk            = '';
            $karyawan->no_npwp          = $user->{'NPWP'};
            $karyawan->level            = $user->{'Level Karyawan'};
            $karyawan->pendidikan       = $user->{'Pendidikan'};
            $karyawan->nomor_hp         = $user->{'No Telp'};
            $karyawan->email            = $user->{'Email'};
            // $karyawan->nama_sekolah     = '';
            // $karyawan->jurusan          = '';
            // $karyawan->kursus           = '';
            // $karyawan->golongan_darah   = '';
            $karyawan->kode_divisi      = $user->{'Kode Divisi'};
            $karyawan->kode_bagian      = $user->{'Kode Bagian'};
            $karyawan->kode_group       = $user->{'Kode Group'};
            $karyawan->kode_jabatan     = $user->{'Kode Jabatan'};
            $karyawan->kode_admin       = $user->{'Kode Admin'};
            $karyawan->kode_periode     = $user->{'Kode Periode'};
            $karyawan->kode_kontrak     = $user->{'Kode Kontrak'};
            $karyawan->status_pph21     = $user->{'Status PPh21'};
            $karyawan->journal_group    = $user->{'Journal Group'};
            $karyawan->save();
        }
    }

    public function createUpdateBatch(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|max:250'
        ]);

        // Count karyawan
        $karyawan = Karyawan::all();

        $updateBatch = new KaryawanUpdateBatch;
        $updateBatch->keterangan = $request->keterangan;
        $updateBatch->all_count = count($karyawan);
        $updateBatch->updated_count = 0;
        $updateBatch->created_by = auth()->user()->name;
        $updateBatch->save();

        return response()->json(['success' => 1, 'message' => 'Karyawan update batch create succeed']);
    }

    public function generateUpdate(Request $request)
    {
        
    }

    public function getUpdate($batchId)
    {
        $data = KaryawanUpdate::where('batch_id', $batchId)->get();

        return response()->json(['success' => 1, 'data' => $data]);
    }

    public function getUpdateBatch()
    {
        $updateBatch = KaryawanUpdateBatch::all();
        return response()->json(['success' => 1, 'data' => $updateBatch]);
    }
}
