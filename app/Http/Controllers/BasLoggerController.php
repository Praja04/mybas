<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Alert;
use App\Change_UploadInner;
use Session;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Image;
use App\Imports\ParameterPengecekanImport;
use App\Imports\StandarSampelVarianImport;


class BasLoggerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('bas.login.login');
    }
    
    public function bas_login(Request $request)
    {
        $validator = $request->validate([
            'nik' => 'required|max:12',
            'password' => 'required|max:255'
        ]);

        $parsing = User::where('username', $request->nik)->first();

        if (Auth::attempt([
            'username' => $request['nik'],
            'password' => $request['password'],
            'status' => '1'
        ], true)) {
            return response()->json(
                [
                    'success' => '1',
                    'data' => $request->all(),
                    'parsing' => $parsing,
                ], 200
            );
        } else {
            return response()->json(['success' => '0'], 401);
        }

    }
   
    //------------------------------------------ OPERATOR -----------------------------------------------  //
    public function index_operator()
    {
        return view('bas.logger.produksi.index');
        // dd('aa');
    }
    
    public function batch_identity()
    {
        $data = DB::table('bas_sampel_varian')->get();

        // dd($sampel);

        return view('bas.logger.produksi.batch_identity', compact('data'));
    }
   
    public function detail_batch_identity($id)
    {
        $detail = DB::table('bas_batch_identity')->where('id', $id)->first();
        // dd($detail);

        return view('bas.logger.produksi.detail_batch_identity', compact('detail'));
    }
    public function destroy_batch_identity($id)
    {
        $data = DB::table('bas_batch_identity')->where('id', $id)->delete();

        Session::flash('success', 'Batch Berhasil Di Hapus..');
        return back();
    }


    public function post_batch_identity(Request $request)
    {
       
        $result = DB::table('bas_batch_identity')->insert([
            'jenis_sampel' => $request->jenis_sampel,            
            'jenis_varian' => $request->jenis_varian,            
            'tgl_pasteurisasi' => $request->tgl_pasteurisasi,            
            'group' => $request->group,            
            'main_blending' => $request->main_blending,            
            'main_batch' => $request->main_batch,            
            'tgl_produksi' => $request->tgl_produksi,            
            'production_order' => $request->production_order,            
            'storage' => $request->storage,            
            'notes' => $request->notes,            
            'nama_pengisi' => Auth::user()->name,            
            'nik' => Auth::user()->username,            
            'tgl_pengisian' => date('Y-m-d'),            
            'jam_pengisian' => date('H:i:s'),            
            'tgl_edit' => date('Y-m-d H:i:s')            
        ]);

        Session::flash('success', 'Batch Berhasil Di Buat..');
        return back();

    }
    public function batch_history()
    {
        $data = DB::table('bas_batch_identity')->where('nik', Auth::user()->username)->get();

        return view('bas.logger.produksi.batch_history', compact('data'));
    }
//------------------------------------------ BATAS  OPERATOR -----------------------------------------------  //


    public function index_analis()
    {
        return view('bas.logger.analis.index');
        // dd(Auth::user()->username);
    }
  
    //------------------------------------------ QC -----------------------------------------------  //
    public function index_qc()
    {
        return view('bas.logger.qc.index');
        // dd(Auth::user()->username);
    }
    //------------------------------------------ BATAS QC -----------------------------------------------  //
    


    //------------------------------------------ SPV -----------------------------------------------  //
    public function index_spv()
    {
        return view('bas.logger.spv.index');
        // dd(Auth::user()->username);
    }
    
    public function sampel_varian()
    {
        $data = DB::table('bas_sampel_varian')->get();

        return view('bas.logger.spv.sampel_varian', compact('data'));
        // dd(Auth::user()->username);
    }
    
    public function destroy_sampel_varian(Request $request)
    {
        $id = $request->id;

        // dd($id);
        
        foreach($id as $val){
                $data = DB::table('bas_sampel_varian')->where('id', $val);
                $data->delete();
        }
        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }
   
    public function post_sampel_varian(Request $request)
    {
         $tgl_sekarang = date('Y-m-d');

         $varian_other = $request->jenis_varian_other;
         $sampel_other = $request->jenis_sampel_other;

            $data = DB::table('bas_sampel_varian')->insert([
                'no_standar'  => $request->no_standar. ' '. $tgl_sekarang,
                'jenis_sampel' => $request->jenis_sampel . $sampel_other,
                'jenis_varian' => $request->jenis_varian . $varian_other,
                'nik' => Auth::user()->username,
                'tgl_pengisian' => date('Y-m-d'),
                'tgl_edit' => date('Y-m-d'),
                'jam_pengisian' => date('H:i:s'),
                ]);
                // dd($data);
        Session::flash('success', 'Data berhasil Dibuat');
        return back();
        // dd($data);
    }

    public function import_sampel_varian(Request $request)
    {
         $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $excel = $request->file('file');
        $data =  Excel::import(new StandarSampelVarianImport, $excel);
        Session::flash('success', 'Data Berhasil Di Tambah..');
        return back();
    }
  
    public function edit_sampel_varian($id)
    {
        $detail = DB::table('bas_sampel_varian')->where('id', $id)->first();
        return view('bas.logger.spv.edit_sampel_varian', compact('detail'));
    }
    
 
    public function update_sampel_varian(Request $request, $id)
    {

         $varian_other = $request->jenis_varian_other;
         $sampel_other = $request->jenis_sampel_other;

        $data = DB::table('bas_sampel_varian')->where('id', $id)
            ->update([
                'no_standar'  => $request->no_standar,
                'jenis_sampel' => $request->jenis_sampel. $sampel_other ,
                'jenis_varian' => $request->jenis_varian. $varian_other,
                'nik' => Auth::user()->username,
                'tgl_edit' => date('Y-m-d'),
                'jam_pengisian' => date('H:i:s'),
            ]);

        Session::flash('success', 'Data Berhasil Di Update..');

        return redirect('/bas_logger/spv/sampel_varian');
    }
   
    public function parameter_pengecekan()
    {
        $no_standar = DB::table('bas_sampel_varian')->get();

        $data = DB::table('bas_parameter_pengecekan')
        ->select(
            'bas_sampel_varian.jenis_sampel',
            'bas_sampel_varian.no_standar',
            'bas_sampel_varian.jenis_varian',
            'bas_parameter_pengecekan.parameter',
            'bas_parameter_pengecekan.satuan_parameter',
            'bas_parameter_pengecekan.nilai',
            'bas_parameter_pengecekan.id',
            )
        ->join('bas_sampel_varian', 'bas_sampel_varian.no_standar', '=','bas_parameter_pengecekan.no_standar')
        ->get();

        $parameter = DB::table('bas_master_parameter')
        ->get();

        return view('bas.logger.spv.parameter_pengecekan', compact('no_standar', 'data', 'parameter'));
    }
   
    public function post_parameter_pengecekan(Request $request)
    {

        
        $min = $request->min;
        $max = $request->max;
        $nilai_standar = $request->nilai_standar;
        $kode_warna = $request->kode_warna;
        // dd($request->all());

        $data = DB::table('bas_parameter_pengecekan')->insert([
            'no_standar' => $request->no_standar,
            'parameter' => $request->parameter,
            'satuan_parameter' => $request->satuan_parameter,
            'tgl_pengisian' => date('Y-m-d'),
            'tgl_edit' => date('Y-m-d'),
            'jam_pengisian' => date('H:i:s'),
        ]);

        if($request->minmax_min != NULL){
          $tes = DB::table('bas_parameter_pengecekan')->where('no_standar', $request->no_standar)
            ->update([
                    'nilai' => $request->minmax_min. " ".  "-". " " .$request->minmax_max,
            ]);
        }
        else if($min != NULL){
            DB::table('bas_parameter_pengecekan')->where('no_standar', $request->no_standar)
            ->update([
                    'nilai' => $min,
            ]);
        }
        else if($max != NULL){
            DB::table('bas_parameter_pengecekan')->where('no_standar', $request->no_standar)
            ->update([
                    'nilai' => $max,
            ]);
        }
        else if($kode_warna != NULL){
            DB::table('bas_parameter_pengecekan')->where('no_standar', $request->no_standar)
            ->update([
                    'nilai' => $kode_warna,
            ]);
        }
        else{
            DB::table('bas_parameter_pengecekan')->where('no_standar', $request->no_standar)
            ->update([
                    'nilai' => 'Standar',
            ]);
        }

        Session::flash('success', 'Data Berhasil Di Tambahkan..');
        return redirect('/bas_logger/spv/parameter_pengecekan');
    }

    public function import_parameter_pengecekan(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $excel = $request->file('file');
        $data =  Excel::import(new ParameterPengecekanImport, $excel);
        Session::flash('success', 'Data Berhasil Di Tambah..');
        return back();
    }

     public function edit_parameter_pengecekan($id)
    {
        $detail = DB::table('bas_parameter_pengecekan')
        ->select(
            'bas_sampel_varian.jenis_sampel',
            'bas_sampel_varian.no_standar',
            'bas_sampel_varian.jenis_varian',
            'bas_parameter_pengecekan.parameter',
            'bas_parameter_pengecekan.satuan_parameter',
            'bas_parameter_pengecekan.nilai',
            'bas_parameter_pengecekan.id',
            )
        ->join('bas_sampel_varian', 'bas_sampel_varian.no_standar', '=', 'bas_parameter_pengecekan.no_standar')
        ->where('bas_parameter_pengecekan.id', $id)
        ->first();
        // dd($detail);
        $parameter = DB::table('bas_master_parameter')->get();

        $no_standar = DB::table('bas_sampel_varian')->get();

        return view('bas.logger.spv.edit_parameter_pengecekan', compact('detail', 'no_standar', 'parameter'));
    }

    public function update_parameter_pengecekan(Request $request, $id)
    {
        $data = DB::table('bas_parameter_pengecekan')->where('id', $id)
            ->update([
                'no_standar'  => $request->no_standar,
                'parameter'  => $request->parameter,
                'satuan_parameter'  => $request->satuan_parameter,
                'nilai'  => $request->nilai,
                'tgl_edit' => date('Y-m-d'),
            ]);
            // dd($id);

        Session::flash('success', 'Data Berhasil Di Update..');

        return redirect('/bas_logger/spv/parameter_pengecekan');
    }

        public function destroy_parameter_pengecekan(Request $request)
    {
        $id = $request->id;
        
        foreach($id as $val){
            $data = DB::table('bas_parameter_pengecekan')->where('id', $val);
            $data->delete();
        }
        Session::flash('success', 'Data Berhasil Di Hapus..');
        return back();
    }
 
    public function get_sampel_parameter($no_standar)
    {
        $data = DB::table('bas_sampel_varian')
        ->join('bas_master_parameter', 'bas_master_parameter.jenis_sampel', '=', 'bas_sampel_varian.jenis_sampel')
        ->where('bas_sampel_varian.no_standar', $no_standar)
        ->get();

        return response()->json([
            'success' =>  1,
            'data'   => $data
        ]);
    }
 
    public function get_kode_warna($no_standar)
    {
        $data = DB::table('bas_master_kodewarna')
        ->join('bas_sampel_varian', 'bas_sampel_varian.jenis_sampel', '=', 'bas_master_kodewarna.jenis_sampel')
        ->where('bas_sampel_varian.no_standar', $no_standar)
        ->orderBy('bas_master_kodewarna.kode_warna', 'ASC')
        ->get();
        // dd($data);

        return response()->json([
            'success' =>  1,
            'data'   => $data
        ]);
    }
    //------------------------------------------ BATAS SPV -----------------------------------------------  //


    public function bas_logout(){
    {
            Auth::logout();
            Session::flash('success', 'Berhasil Logout');
        return redirect('/bas_login');
    }
    }
}
