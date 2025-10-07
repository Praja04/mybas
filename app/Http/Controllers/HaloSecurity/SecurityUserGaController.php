<?php

namespace App\Http\Controllers\HaloSecurity;

use App\Exports\HaloSecurity\HsSecurityUserGa;
use App\HaloSecurity\SecurityUserGA;
use App\Http\Controllers\Controller;
use App\PI\PiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Excel;
// use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class SecurityUserGaController extends Controller
{
    public function generateID($prefix, $tableName, $columnName, $aditionalWhere = null)
     {
         $prefixLength = strlen($prefix);
 
         $numberBefore = DB::table($tableName)
         ->selectRaw('SUBSTR('.$columnName.', '.($prefixLength+1).') as code')
         ->orderByRaw('CAST(SUBSTR('.$columnName.', '.($prefixLength+1).') AS SIGNED) desc')
         ->whereRaw('SUBSTR('.$columnName.',1, '.($prefixLength).') = \''.$prefix.'\'');
         
         if($aditionalWhere != null) {
             $numberBefore = $numberBefore->whereRaw($aditionalWhere);
         }
 
         $numberBefore = $numberBefore->first();
 
         if($numberBefore == null) {
             return $prefix.'00001';
         }
 
         $currentNumber = (int)$numberBefore->code+1;
         
         switch ($currentNumber) {
             case $currentNumber < 10:
                 $currentCode = $prefix.'0000'.$currentNumber;
                 break;
             case $currentNumber < 100:
                 $currentCode = $prefix.'000'.$currentNumber;
                 break;
             case $currentNumber < 1000:
                 $currentCode = $prefix.'00'.$currentNumber;
                 break;
             case $currentNumber < 10000:
                 $currentCode = $prefix.'0'.$currentNumber;
                 break;
             default:
                 $currentCode = $prefix.$currentNumber;
                 break;
         }
 
         return $currentCode;
     }
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->permission('hs_listsug');

        $data = SecurityUserGA::query();

        $securitys = $data->orderBy('created_at', 'DESC')->get();

        return view('pages.halo-security.list-security-ga', compact('securitys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->permission('hs_listsug');

        return view('pages.halo-security.create-security-user-ga');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
        ],[
            'nik.required' => 'Data Nomor NIK wajib di isi',
            'nama.required' => 'Data Nama wajib di isi',
        ]);

        $user_id = $this->generateID('SUG', 'security_user_ga', 'user_id');

        $template = new SecurityUserGA([
                'user_id' => $user_id,
                'nik' => $request->get('nik'),
                'nama' => $request->get('nama'),
                'keterangan' => $request->get('keterangan'),
        ]);
    
        $template->save();

        return redirect()->route('security')->with(['success'=>'Data Security User GA berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SecurityUserGA $security, $user_id)
    {
        // $this->permission('hs_edit_gapetugas');
        
        $security = SecurityUserGA::find($user_id);

        return view('pages.halo-security.edit-security-user-ga', ['security' => $security]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {
        $data = $request->except('_method','_token','submit');
  
        $validator = Validator::make($request->all(), [
            'nik' => 'required',
            'nama' => 'required',
        ],[
            'nik.required' => 'Data Nomor NIK wajib di isi',
            'nama.required' => 'Data Nama wajib di isi',
        ]);
  
        if ($validator->fails()) {
           return redirect()->Back()->withInput()->withErrors($validator);
        }

        $security = SecurityUserGA::find($user_id);
  
        if($security->update($data)){
            return redirect()->route('security')->with(['success'=>'Data Security User GA berhasil diubah']);
        }else{
            return redirect()->route('edit-security')->with(['success'=>'Data Security User GA gagal diubah']);
        }
  
        return Back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id)
    {
        $item = SecurityUserGA::findOrFail($user_id);
        $item->delete();

        return redirect()->route('security')->with(['success'=>'Data Security User GA berhasil dihapus']);
    }

    public function exportexcelsecurity(Request $request)
    {
        return Excel::download(new HsSecurityUserGa(),'Halo Security Security User GA.xlsx');
    }

    public function proses_scan(Request $request)
    {		
		$id_card = (int)$request->rfid;

		// Get data dari secure access berdasarkan nomor kartu
		$user = DB::connection('192.168.154.44')
		->table('MSIDCARD')
		->select('NIK','EMPNM','FOTOBLOB','CREATEDON', 'CARDNODEVICE')
		->where(['CARDNODEVICE' => $id_card ])
		->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
        ->first();

        if($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $nik = $user->NIK;

        $user->FOTOBLOB = base64_encode($user->FOTOBLOB);

        $scan = PiKaryawan::where("nik_karyawan", $nik)
        ->where('status', 3)
        ->with('hr_karyawan')
        ->with('jenis')
        ->with('gm')
        ->with('dh')
        ->get();

        return response()->json(['success' => 1, 'message' => 'Data ditemukan', 'data_karyawan' => $user, 'data_perizinan' => $scan]);
    }

    public function getByNIK($nik)
    {
        // Get data dari secure access berdasarkan nomor kartu
		$user = DB::connection('192.168.154.44')
		->table('MSIDCARD')
		->select('NIK','EMPNM','FOTOBLOB','CREATEDON', 'CARDNODEVICE')
		->where(['NIK' => $nik ])
		->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
        ->first();

        if($user == null) {
            return response()->json(['success' => 0, 'message' => 'Data not found']);
        }

        $nik = $user->NIK;

        $user->FOTOBLOB = base64_encode($user->FOTOBLOB);

        // Get data Karyawan
        $data = PiKaryawan::where('nik_karyawan', $nik)
        ->where('status', 3)
        ->with('hr_karyawan')
        ->with('jenis')
        ->with('gm')
        ->with('dh')
        ->first();

        return response()->json(['success' => 1, 'message' => 'Data ditemukan', 'data' => $data, 'data_karyawan' => $user]);
    }

    public function cek_pengajuan_izin()
    {
        return view('pages.halo-security.cek_pengajuan_izin');
    }

    public function getKaryawan(Request $request)
    {
        if(request()->ajax())
        {
            if(!empty($request->nik))
            {

            $data = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK','EMPNM')
            ->where(['NIK' => $request->nik ])
            ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc');

            }
            else
            {
                
            $data = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK','EMPNM')
            ->where(['NIK' => '12345678' ])
            ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc');

            }
            return datatables()->of($data)->make(true);
        }
    }
}
