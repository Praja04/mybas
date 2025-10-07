<?php

namespace App\Http\Controllers;

use App\LoggingMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Alert;
use App\Change_UploadInner;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Crypt;
use App\MasterMesin;
use App\MasterRepair;
use App\User;
use App\MasterSamplingGramatur;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MasterMesinImport;
use App\Imports\MasterRepairImport;
use App\Imports\MasterVarianImport;
use App\Imports\MasterSamplingGramaturImport;
use Image;


class LoggingMachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function scan(Request $request)
    {
        $data = [];

        // Untuk antisipaso
        $rfid = (int)$request->rfid;

        $employee = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM')
            ->where(['CARDNODEVICE' => $rfid])
            // ->orderBy('CREATEDON', 'desc')
            // ->orderByRaw('SUBSTR(NIK, 8) desc')
            ->first();

        // Cek apakah ada data nya ada apa engga di server security
        if ($employee == null || $rfid == 0) {
            // Kalau ga ada maka langsung return error
            $data['image'] = 'default';
            $data['message'] = 'Kartu tidak terdaftar di system';
            return response()->json(['success' => 0, 'data' => $data]);
        }

        $user = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE', 'NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where(['NIK' => $employee->NIK, 'CARDNODEVICE' => $rfid])
            ->first();

        $data['idcard']  = $user->CARDNODEVICE;
        $data['nik']     = $user->NIK;
        $data['name']    = $user->EMPNM;
        $data['dept']    = $user->DEPTID;
        $data['image']   = base64_encode($user->FOTOBLOB);
        $data['auth_engineering'] = User::select('username')->where('auth_group_id', 58)->get();

        $data['message'] = 'Data Ditemukan';

        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }

    public function index($nik)
    {
        $employee = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM')
            ->where(['NIK' => $nik])
            ->orderByRaw('SUBSTR(NIK, 8) desc')
            ->first();

        $user = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE', 'NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where('NIK', $employee->NIK)
            ->first();

        $foto = base64_encode($user->FOTOBLOB);

          $sampling = DB::table('master_sampling_s2')->get();

            $jam_ke = [];
            $shift_ke = [];
            foreach ($sampling as $s) {
                $waktu_mulai = new \DateTime($s->waktu_mulai);
                $waktu_selesai = new \DateTime($s->waktu_selesai);
                $waktu_sekarang = new \DateTime();
                // jika waktu sekarang. diantara waktu mulai dan waktu selesai maka gunakan jam ke nya
                if($waktu_sekarang >= $waktu_mulai && $waktu_sekarang <= $waktu_selesai) {
                    $jam_ke[] = $s->jam_ke;
                    $shift_ke[] = $s->shift;
                }
            }
        
        $rasa = DB::table('master_varian_rasa_s2')->orderBy('rasa', 'ASC')->get();
        
        $rasa_after = DB::table('master_planning_s2')->orderBy('varian_rasa', 'ASC')->get();

        $no_mesin = DB::table('master_mesin_s2')->select('line','group')->orderBy('line', 'ASC')
        ->groupBy('line')
        ->groupBy('group')
        ->get();
        
        $tgl_sekarang = date('Y-m-d');
        
        $cek_logging_machine = DB::table('logging_machine')->where('nik', $nik)->where('tgl_pengisian', $tgl_sekarang)->first();

          $mesin_saya = LoggingMachine::select(
            'logging_machine.nik',
            'logging_machine.id',
            'logging_machine.nama',
            'logging_machine.varian',
            'logging_machine.rasa',
            'master_mesin_s2.line',
            'logging_machine.no_mesin',
            'logging_machine.pindah_varian',
            )->join('master_mesin_s2', 'master_mesin_s2.no_mesin', 'logging_machine.no_mesin')
            ->where('logging_machine.nik', $nik)->where('logging_machine.tgl_pengisian', $tgl_sekarang)
            ->orderBy('logging_machine.pindah_varian', 'ASC')
            ->get();
          
        $mesin_running = LoggingMachine::select(
            'logging_machine.nik',
            'logging_machine.id',
            'logging_machine.nama',
            'logging_machine.varian',
            'logging_machine.total_produksi_box',
            'logging_machine.rasa',
            'master_mesin_s2.line',
            'logging_machine.no_mesin',
            'logging_machine.pindah_varian',
            )->join('master_mesin_s2', 'master_mesin_s2.no_mesin', 'logging_machine.no_mesin')
            ->where('logging_machine.nik', $nik)
            ->where('logging_machine.tgl_pengisian', $tgl_sekarang)
            ->where('logging_machine.pindah_varian', '!=', 0)
            ->get();

            // dd($mesin_running);

            $hari_sabtu = date('Saturday');

        return view('logging_machine.index',  compact('user',  'foto',  'no_mesin', 'shift_ke', 'cek_logging_machine', 'mesin_saya', 'rasa_after', 'mesin_running', 'hari_sabtu'));
    }

        public function login_engineering()
        {
            return view ('logging_machine.maintenance.login');
            
        }
      
        public function scan_login(Request $request)
        {
        $data = [];

        // Untuk antisipaso
        $rfid = (int)$request->rfid;

        $employee = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM')
            ->where(['CARDNODEVICE' => $rfid])
            // ->orderBy('CREATEDON', 'desc')
            // ->orderByRaw('SUBSTR(NIK, 8) desc')
            ->first();

        // Cek apakah ada data nya ada apa engga di server security
        if ($employee == null || $rfid == 0) {
            // Kalau ga ada maka langsung return error
            $data['image'] = 'default';
            $data['message'] = 'Kartu tidak terdaftar di system';
            return response()->json(['success' => 0, 'data' => $data]);
        }

        $user = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('CARDNODEVICE', 'NIK', 'EMPNM', 'DEPTID', 'FOTOBLOB')
            ->where(['NIK' => $employee->NIK, 'CARDNODEVICE' => $rfid])
            ->first();

        $data['idcard']  = $user->CARDNODEVICE;
        $data['nik']     = $user->NIK;
        $data['name']    = $user->EMPNM;
        $data['dept']    = $user->DEPTID;
        $data['image']   = base64_encode($user->FOTOBLOB);
        // $data['substr_maintenance'] = substr($user->NIK, 7);
        $data['auth_engineering'] = User::select('username')->where('auth_group_id', 58)->get();

        // $data['password_engineering'] = User::select('password')->where('auth_group_id', 53)->get(); 

        $data['message'] = 'Data Ditemukan';

        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }

    public function dashboard_maintenance($nik)
    {
        $currentURL = url()->current();
        // dd($currentURL);
        $substr_nik = substr($currentURL, -12);
        
        $kategori = "engineering";
        
        $eng = DB::table('downtime')
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereNotNull('logging_machine.no_mesin')
        ->whereNotNull('logging_machine.rasa')
        ->where('status', 2)
        ->where('jenis_maintenance', 'Engineering')
        ->get();

        $notif_eng = count($eng);
        
        $out_eng = DB::table('downtime')
         ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereNotNull('logging_machine.no_mesin')
        ->whereNotNull('logging_machine.rasa')
        ->where('downtime.status', 1)
        ->where('downtime.jenis_maintenance', 'Engineering')
        ->where('downtime.approval_maintenance_nik', $nik)
        ->get();
        $notif_out_eng = count($out_eng);

        return view('logging_machine.maintenance.dashboard_maintenance', compact('notif_eng', 'notif_out_eng', 'kategori', 'nik'));
}

    public function downtime($status_varian, $no_mesin, $nik)
    {

        $nik = Crypt::decrypt($nik);
        $tgl_sekarang = date('Y-m-d');

        $user = DB::table('logging_machine')
        ->where('nik', $nik)
        ->where('no_mesin', $no_mesin)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('pindah_varian', $status_varian)
        ->first();

        $reason = DB::table('master_reason_s2')->get();

        return view('logging_machine.downtime.downtime',  compact('user', 'reason'));
    }

    public function history_downtime($no_mesin, $nik)
    {
        // $data = DB::table('downtimewhere('nik', $nik)->get();
        $nik = Crypt::decrypt($nik);

        $data = DB::table('downtime')
            ->select(
                'downtime.id',
                'downtime.id_logging_machine',
                'logging_machine.nama',
                'downtime.nik',
                'downtime.no_mesin',
                'downtime.tgl_pengisian',
                'downtime.kerusakan',
                'downtime.no_mesin',
                'downtime.status',
                'downtime.jam_pengisian',
                'downtime.close_operator',
            )
            ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
            ->where('downtime.nik', $nik)
            ->where('downtime.no_mesin', $no_mesin)
            ->groupBy('downtime.jam_pengisian')
            ->orderBy('tgl_pengisian', 'DESC')
            ->get();

        return view('logging_machine.downtime.history_downtime', compact('data'));
    }

        public function detail_downtime($id)
    {
        $id = Crypt::decrypt($id);
        
        $data = DB::table('downtime')->where('id', $id)->first();

        $data = DB::table('downtime')
            ->select('*')
            ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
            ->where(['downtime.id' => $id])
            ->first();
        return view('logging_machine.downtime.detail_downtime', compact('data'));
    }

    public function post_downtime(Request $request)
    {
        $nik = $request->nik;
        $no_mesin = $request->no_mesin;

        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('downtime')->insert([
            'id_logging_machine' => $request->id_logging_machine,
            'no_mesin' => $request->no_mesin,
            'nik' => $nik,
            'tgl_pengisian' => date('Y-m-d'),
            'jam_pengisian' => date('H:i'),
            'jam_mulai_maintenance' => date('H:i'),
            'kerusakan' => $request->kerusakan,
            'kode_sap' => $request->kode_reason,
            'jenis_maintenance' => $request->jenis_maintenance,
            'status' => 2,
            'progress' => 'Pending Maintenance'
        ]);

        Session::flash('success', 'Downtime Berhasil Dibuat');

        return redirect('/logging_machine/history_downtime/' . $no_mesin. '/'. Crypt::encrypt($nik));
    }

    public function edit_logging_master($status_varian, $no_mesin, $nik)
    {
        $nik = Crypt::decrypt($nik);

        $detail = LoggingMachine::where('nik', $nik)
            ->where('tgl_pengisian', date('Y-m-d'))
            ->where('no_mesin', $no_mesin)
            ->where('pindah_varian', $status_varian)
            ->first();

        $rasa = DB::table('master_varian_rasa_s2')->orderBy('rasa', 'ASC')->get();

        $no_mesin = DB::table('master_mesin_s2')->select('line','group')->orderBy('line', 'ASC')
        ->groupBy('line')
        ->groupBy('group')
        ->get();

            $hari_sabtu = date('Saturday');


          $sampling = DB::table('master_sampling_s2')->get();

            $jam_ke = [];
            $shift_ke = [];
            foreach ($sampling as $s) {
                $waktu_mulai = new \DateTime($s->waktu_mulai);
                $waktu_selesai = new \DateTime($s->waktu_selesai);
                $waktu_sekarang = new \DateTime();
                // jika waktu sekarang. diantara waktu mulai dan waktu selesai maka gunakan jam ke nya
                if($waktu_sekarang >= $waktu_mulai && $waktu_sekarang <= $waktu_selesai) {
                    $jam_ke[] = $s->jam_ke;
                    $shift_ke[] = $s->shift;
                }
            }
        

        return view('logging_machine.edit_master_logging', compact('detail', 'rasa', 'no_mesin', 'shift_ke', 'hari_sabtu'));
    }

    public function post_logging_master(Request $request)
    {
        $no_mesin = $request->no_mesin;

        $nik = $request->nik;
        
        $data = LoggingMachine::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'tgl_pengisian' => date('Y-m-d'),
            'varian' => $request->varian,
            'rasa' => $request->varian_rasa,
            'no_mesin' => $request->no_mesin,
            'shift_group' => $request->shift . "/". $request->group,
            'pindah_varian' => 1
            ]);

        Session::flash('success', 'Data Berhasil Dibuat');

        return back();
    }
    
    public function pindah_varian_logging_master(Request $request)
    {
        $no_mesin = $request->no_mesin;

        $nik = $request->nik;
        $id = $request->id;

         $data = DB::table('logging_machine')->where('id', $id)->update([
            'pindah_varian' => 0,
            ]);

        $data = LoggingMachine::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'tgl_pengisian' => date('Y-m-d'),
            'varian' => $request->varian,
            'rasa' => $request->varian_rasa,
            'no_mesin' => $request->no_mesin,
            'shift_group' => $request->shift . "/". $request->group,
            'pindah_varian' => $request->pindah_varian
            ]);

        Session::flash('success', 'Data Berhasil Dibuat');

        return back();
    }

    public function update_logging_master(Request $request)
    {
        $nik = $request->nik;
        $no_mesin = $request->no_mesin_before;
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')
        ->where('nik', $nik)
        ->where('no_mesin', $no_mesin)
        ->where('tgl_pengisian', $tgl_sekarang)
            ->update([
                'varian' => $request->varian,
                'rasa' => $request->varian_rasa,
                'no_mesin' => $request->no_mesin,
            ]);

        Session::flash('success', 'Data Berhasil Di Update..');

        return redirect('/logging_machine/index/' . $nik);
    }
    
    public function post_hasil_produksi(Request $request, $id)
    {
        $id = Crypt::decrypt($id);

        $data = DB::table('logging_machine')->where('id', $id)
            ->update([
                'total_produksi_box' => $request->hasil_produksi_box,
                'hasil_produksi_pcs' => $request->hasil_produksi_pcs,
                'kondisi_gear' => $request->kondisi_gear,
                'pindah_varian' => 0
            ]);

        Session::flash('success', 'Berhasil, Data Anda Berhasil Di Rekam Dalam Sistem..');

        return redirect('/display/logging_machine');
    }
    
    public function get_hasil_produksi(Request $request, $no_mesin, $nik, $status_varian)
    {
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')
        ->select('logging_machine.id',
                  'logging_machine.nama', 
                  'logging_machine.nik', 
                  'logging_machine.no_mesin', 
                  'logging_machine.shift_group', 
                  'logging_machine.total_produksi_box', 
                  'logging_machine.pindah_varian', 
                  'master_mesin_s2.NoSeq'
                 )
        ->join('master_mesin_s2', 'master_mesin_s2.no_mesin', '=', 'logging_machine.no_mesin')
        ->where('logging_machine.nik', $nik)
        ->where('logging_machine.no_mesin', $no_mesin)
        ->where('logging_machine.tgl_pengisian', $tgl_sekarang)
        ->where('logging_machine.pindah_varian', $status_varian)
        ->first();
        // dd($data);

        $cek_shift = substr($data->shift_group, 0, 1);
        $shift_saya = (int)$cek_shift;

        if($shift_saya == 1){
            $shift_1_start = date('Y-m-d 07:00:00' );
            $shift_1_end = date('Y-m-d 15:00:00' );

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            ->where('NoSeq', $data->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_1_start, $shift_1_end])
            ->orderBy('Tgljam', 'asc')
            ->get();

            $total_pcs = $counter
            ->sum('CntDev');

        }
        else if($shift_saya == 2){
            $shift_2_start = date('Y-m-d 15:00:00' );
            $shift_2_end = date('Y-m-d 23:00:00' );

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            // ->whereDate('Tgljam', $tgl_sekarang)
            ->where('NoSeq', $data->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_2_start, $shift_2_end])
            ->orderBy('Tgljam', 'asc')
            ->get();

            $total_pcs = $counter
            ->sum('CntDev');
        }
        else{
            $shift_3_start = date('Y-m-d 00:00:00' );
            $shift_3_end = date('Y-m-d 07:00:00');

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            ->where('NoSeq', $data->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_3_start, $shift_3_end])
            ->orderBy('Tgljam', 'asc')
            ->get();
            $total_pcs = $counter
            ->sum('CntDev');
            
        }

        return view('logging_machine.input_hasil_produksi', compact('data', 'counter', 'total_pcs'));
    }

    
    public function get_data_monitoring()
    {
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('downtime')
        ->where('status', 2)
        ->where('jenis_maintenance', 'Engineering')
        ->get();
        dd($data);
        
        return response()->json([
            'status' => 1,
            'data' => $data
            ]);
    }

    public function get_nama_login($nik)
    {

    $data = DB::connection('192.168.154.44')
        ->table('MSIDCARD')
        ->select('NIK', 'EMPNM')
        ->where('NIK', $nik)
        ->first();

        return response()->json([
            'status' => 1,
            'data' => $data
            ]);
    }
    
    public function get_modal_operator(Request $request, $nik)
    {
        $tgl_sekarang = date('Y-m-d');
        $nik = $request->nik;
        $data = DB::table('downtime')
        ->where('nik', $nik)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('close_operator', 1)
        ->where('jenis_maintenance', 'Operator')
        ->first();
        // dd($data);

        return json_encode(
            [
                'success' => 1,
                'data' => $data
            ]);
    }
    
    public function list_for_operator($nik)
    {

        $data = DB::table('downtime')
        ->select(
            // 'logging_machine.id',
            'downtime.no_mesin',
            'downtime.id',
            'downtime.tgl_pengisian',
            'downtime.jam_pengisian',
            'downtime.kerusakan',
            'downtime.close_operator',
            'logging_machine.nik',
            'logging_machine.nama',
            'logging_machine.shift_group'
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.nik', $nik)
        ->orderBy('downtime.tgl_pengisian', 'DESC')
        ->get();
        // dd($data);
        return view('logging_machine.maintenance.list_for_operator', compact ('data'));
    }

    public function close_from_operator(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $nik = $request->nik;
        $jam_pengisian = $request->jam_pengisian;

        $waktuawal = date_create($jam_pengisian); //waktu di setting
        $waktuakhir = date_create();
        
        $diff = date_diff($waktuawal, $waktuakhir);
        
        $data = DB::table('downtime')->where('id', $id)->update([
            'close_operator' => 0,
            'status' => 0,
            'jam_selesai_maintenance' => date('H:i'),
            'approval_maintenance_nama' => $request->nama,
            'approval_maintenance_nik' => $request->nik,
            'tgl_respon_maintenance' => date('Y-m-d'),
            'progress' => 'Close By Operator',
            'waktu_penyelesaian' => $diff->h . ' ' . 'JAM' . ' ' . $diff->i . ' ' . 'MENIT',
            'tgl_selesai_maintenance' => date('Y-m-d'),
            ]);

    Session::flash('success', 'Permintaan Berhasil Di Close..');
    return redirect('/logging_machine/index/'. $nik);
    }

    public function wip($status_varian, $id_logging_machine, $nik){
    $nik = Crypt::decrypt($nik);
    
    $tgl_sekarang = date('Y-m-d');

    $user = DB::table('logging_machine')
    ->where('id', $id_logging_machine)
    ->where('pindah_varian', $status_varian)
    ->where('nik', $nik)
    ->where('tgl_pengisian', $tgl_sekarang)
    ->first();
    // dd($user);
   
    $cek_wip = DB::table('logging_machine_wip')
    ->where('id_logging_machine', $id_logging_machine)
    ->where('nik', $nik)
    ->where('tgl_pengisian', $tgl_sekarang)
    ->first();

    return view('logging_machine.wip.index', compact('user', 'cek_wip'));
}

    public function get_edit_wip($id)
    {
        $detail = DB::table('logging_machine_wip')->where('id', $id)->first();
        return view('logging_machine.wip.edit', compact('detail'));
    }
   
    public function update_wip(Request $request, $id)
    {
        $nik = $request->nik;
        $id_logging_machine = $request->id_logging_machine;

        $detail = DB::table('logging_machine_wip')->where('id', $id)->update([
            'inner_reject' => $request->inner_reject,
            'sampah_inner' => $request->sampah_inner,
            'total_wip' => $request->total_wip,
            'sortir' => $request->sortir,
            'sobek' => $request->sobek,
        ]);

        Session::flash('success', 'Data Berhasil Diedit..');
        return redirect('/logging_machine/history_wip/' . $id_logging_machine . '/'.  Crypt::encrypt($nik));
    }
    
    public function post_wip(Request $request){

        $nik = $request->nik;
        $tgl_sekarang = date('Y-m-d');

        $user = DB::table('logging_machine')->where('nik', $nik)->where('tgl_pengisian', $tgl_sekarang)->first();


        $data = DB::table('logging_machine_wip')->insert([
            'id_logging_machine' => $request->id_logging_machine,
            'rasa' => $request->rasa,
            'nik' => $nik,
            'inner_reject' => $request->inner_reject,
            'sampah_inner' => $request->sampah_inner,
            'total_wip' => $request->total_wip,
            'sortir' => $request->sortir,
            'sobek' => $request->sobek,
            'tgl_pengisian' => date('Y-m-d'),
        ]);
        Session::flash('success', 'Data Berhasil Dibuat..');
        return redirect('/logging_machine/index/' . $nik);
    }

    public function history_wip($no_mesin, $nik)
    {
        $nik = Crypt::decrypt($nik);

        $data = DB::table('logging_machine_wip')
        ->select(
            'logging_machine_wip.tgl_pengisian',
            'logging_machine_wip.inner_reject',
            'logging_machine_wip.rasa',
            'logging_machine_wip.sampah_inner',
            'logging_machine_wip.total_wip',
            'logging_machine_wip.sortir',
            'logging_machine_wip.sobek',
            'logging_machine_wip.nik',
            'logging_machine_wip.id',
            'logging_machine.no_mesin',
            'logging_machine_wip.rasa',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'logging_machine_wip.id_logging_machine')
        ->where('logging_machine.no_mesin', $no_mesin)
        ->where('logging_machine.nik', $nik)
        ->get();
        // dd($data);
        return view('logging_machine.wip.history_wip', compact('data'));
    }

    public function kebersihan($status_varian, $id_logging_machine, $nik){
        $nik = Crypt::decrypt($nik);
        
        $tgl_sekarang = date('Y-m-d');

        $user = DB::table('logging_machine')
        ->where('id', $id_logging_machine)
        ->where('pindah_varian', $status_varian)
        ->where('nik', $nik)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();
        
        $cek = DB::table('logging_machine_kebersihan')
        ->where('id_logging_machine', $id_logging_machine)
        ->where('nik', $nik)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();

        return view('logging_machine.kebersihan.index', compact('user', 'cek'));
    }

    public function history_kebersihan($no_mesin, $nik)
    {
        $nik = Crypt::decrypt($nik);
        
        $data = DB::table('logging_machine_kebersihan')
            ->select('*')
            ->join('logging_machine', 'logging_machine.id', '=', 'logging_machine_kebersihan.id_logging_machine')
            ->where('logging_machine_kebersihan.nik' , $nik)
            ->where('logging_machine.no_mesin' , $no_mesin)
            ->get();

        return view('logging_machine.kebersihan.history_kebersihan', compact('data'));
    }

        public function post_kebersihan(Request $request){

            $nik = $request->nik;
            $tgl_sekarang = date('Y-m-d');
    
            $user = DB::table('logging_machine')
            ->where('nik', $nik)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->first();
    
            $data = DB::table('logging_machine_kebersihan')->insert([
                'id_logging_machine' => $request->id_logging_machine,
                'lantai' => $request->lantai,
                'bak' => $request->bak,
                'body_mesin' => $request->body_mesin,
                'sealer' => $request->sealer,
                'gayung' => $request->gayung,
                'sodokan' => $request->sodokan,
                'tutup_hopper' => $request->tutup_hopper,
                'serbet' => $request->serbet,
                'nik' => $request->nik,
                'tgl_pengisian' => $tgl_sekarang,
            ]);
            Session::flash('success', 'Data Berhasil Dibuat..');
            return redirect('/logging_machine/index/' . $nik);
        }

    public function inner($id_logging_machine, $nik)
    {
        $nik = Crypt::decrypt($nik);
        $tgl_sekarang = date('Y-m-d');

        $master_logging = LoggingMachine::where('nik', $nik)->where('tgl_pengisian', $tgl_sekarang)->where('id', $id_logging_machine)->first();

        $cek_inner = DB::table('inner_logging_machine')
        ->select(
                 'logging_machine.shift_group',
                'inner_logging_machine.tgl_pengisian',
                'inner_logging_machine.jam_pengisian',
                'inner_logging_machine.tgl_edit',
                'inner_logging_machine.jam_edit',
                'inner_logging_machine.foto',
                'inner_logging_machine.id',
            )
        ->join('logging_machine', 'logging_machine.id', '=' ,'inner_logging_machine.id_logging_machine')
        ->where('inner_logging_machine.nik', $nik)
        ->where('inner_logging_machine.tgl_pengisian', $tgl_sekarang)
        ->where('inner_logging_machine.id_logging_machine', $id_logging_machine)
        ->get();

        return view('logging_machine.inner.index', compact('master_logging', 'cek_inner'));
    }
   
    public function detail_inner_from_history($nik, $tgl_pengisian)
    {
        $tgl_pengisian = Crypt::decrypt($tgl_pengisian);

        $cek_inner = DB::table('inner_logging_machine')
        ->select(
                 'logging_machine.shift_group',
                 'logging_machine.nama',
                 'logging_machine.no_mesin',
                 'logging_machine.rasa',
                'inner_logging_machine.tgl_pengisian',
                'inner_logging_machine.jam_pengisian',
                'inner_logging_machine.tgl_edit',
                'inner_logging_machine.jam_edit',
                'inner_logging_machine.foto',
                'inner_logging_machine.id',
                'inner_logging_machine.nik',
            )
        ->join('logging_machine', 'logging_machine.id', '=' ,'inner_logging_machine.id_logging_machine')
        ->where('inner_logging_machine.tgl_pengisian', $tgl_pengisian)
        ->where('inner_logging_machine.nik', $nik)
        ->get();

        return view('logging_machine.inner.detail_inner_from_history', compact('cek_inner'));
    }

    public function post_inner(Request $request)
    {

        $data = Change_UploadInner::create([
            'id_logging_machine' => $request->id_logging_machine,
            'nik' => $request->nik,
            'tgl_pengisian' => date('Y-m-d'),
            'jam_pengisian' => date('H:i'),
            'tgl_edit' => date('Y-m-d'),
            'jam_edit' => date('H:i'),
        ]);

        if ($request->has('foto')) {
            $foto = $request->file('foto');
            $foto_name = 'foto-' . time() . uniqid() . '.jpg';
            Image::make($foto)->resize(1000, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })->save('dokumen_lot/' . $foto_name);
            $data->foto = $foto_name;
            $data->save();
            
        }
        Session::flash('success', 'Data Berhasil Dibuat..');
        return back();
    }

    public function update_inner(Request $request, $id)
    {
        $tgl_sekarang = date('Y-m-d');

        $id = Crypt::decrypt($id);

        $data = Change_UploadInner::find($id);
        $data->jam_edit = date('H:i');
        $data->tgl_edit = $tgl_sekarang;

        if ($request->has('foto')) {

            $foto = $request->file('foto');
            $foto_name = 'foto-' . time() . uniqid() . '.jpg';
            Image::make($foto)->resize(1000, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })->save('dokumen_lot/' . $foto_name);
            $data->foto = $foto_name;
            $data->save();
        }


        Session::flash('success', 'Data Berhasil Di Update..');

        return redirect('/logging_machine/inner/' . Crypt::encrypt($data->nik));
    }

    public function history_inner($nik)
    {
        $nik = Crypt::decrypt($nik);
        
        $data = Change_UploadInner::where('nik', $nik)->where('tgl_pengisian', date('Y-m-d'))->get();

        $join = DB::table('inner_logging_machine')
            ->select(
                'logging_machine.shift_group',
                'logging_machine.nik',
                'logging_machine.no_mesin',
                'inner_logging_machine.tgl_pengisian',
                'logging_machine.rasa',
                'logging_machine.varian',
                'inner_logging_machine.foto',
                'inner_logging_machine.id_logging_machine',
                )
            ->join('logging_machine', 'logging_machine.id', '=', 'inner_logging_machine.id_logging_machine')
            ->where(['inner_logging_machine.nik' => $nik])
            ->groupBy('inner_logging_machine.tgl_pengisian')
            ->get();

        return view('logging_machine.inner.history_inner', compact('data', 'join', 'nik'));
    }

    public function detail_inner(Request $request, $id)
    {
        $id = Crypt::decrypt($id);

        $detail = Change_UploadInner::where('id', $id)->first();
        // dd($id);

        return view('logging_machine.inner.detail_inner', compact('detail'));
    }

    public function inner_shift_lain($no_mesin)
    {
        $no_mesin = Crypt::decrypt($no_mesin);

        $join = DB::table('inner_logging_machine')
            ->select(
                'logging_machine.nama',
                'logging_machine.nik',
                'logging_machine.no_mesin',
                'logging_machine.varian',
                'logging_machine.rasa',
                'logging_machine.shift_group',
                'inner_logging_machine.tgl_pengisian',
                'inner_logging_machine.jam_edit',
                'inner_logging_machine.foto',
                'inner_logging_machine.id',
            )
            ->join('logging_machine', 'logging_machine.id', '=', 'inner_logging_machine.id_logging_machine')
            ->where(['logging_machine.no_mesin' => $no_mesin])->orderBy('inner_logging_machine.tgl_pengisian', 'DESC')
            ->get();
        // dd($join);
        return view('logging_machine.inner.inner_shift_lain', compact('join'));
    }

    public function detail_inner_shift_lain($id)
    {

        $id = Crypt::decrypt($id);

        $join = DB::table('inner_logging_machine')
            ->select(
                'logging_machine.nama',
                'logging_machine.nik',
                'logging_machine.no_mesin',
                'logging_machine.varian',
                'logging_machine.rasa',
                'logging_machine.shift_group',
                'inner_logging_machine.tgl_pengisian',
                'inner_logging_machine.jam_pengisian',
                'inner_logging_machine.jam_edit',
                'inner_logging_machine.tgl_edit',
                'inner_logging_machine.foto',
                'inner_logging_machine.id',
            )
            ->join('logging_machine', 'logging_machine.nik', '=', 'inner_logging_machine.nik')
            ->where(['inner_logging_machine.id' => $id])
            ->first();
        // dd($join->id);
        return view('logging_machine.inner.detail_inner_shift_lain', compact('join'));
    }

    public function all_list($nik)
    {
            $engineering = DB::table('downtime')
                ->select(
                    'logging_machine.nama',
                    'logging_machine.nik',
                    'logging_machine.no_mesin',
                    'logging_machine.varian',
                    'logging_machine.rasa',
                    'logging_machine.shift_group',
                    'downtime.approval_maintenance_remarks',
                    'downtime.tgl_pengisian',
                    'downtime.kerusakan',
                    'downtime.jenis_maintenance',
                    'downtime.status',
                    'downtime.jam_pengisian',
                    'downtime.id',
                )
                ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
                ->where('downtime.approval_maintenance_nik', '=', $nik)
                ->whereNotNull('logging_machine.no_mesin')
                ->whereNotNull('logging_machine.rasa')
                ->orderBy('downtime.tgl_pengisian', 'DESC')
                ->get();

            return view('logging_machine.maintenance.list_all_downtime', compact('engineering', 'nik'));
        }

    public function permintaan_baru($nik)
    {
            $engineering = DB::table('downtime')
                ->select(
                    'logging_machine.nama',
                    'logging_machine.nik',
                    'logging_machine.no_mesin',
                    'logging_machine.varian',
                    'logging_machine.rasa',
                    'logging_machine.shift_group',
                    'downtime.tgl_pengisian',
                    'downtime.kerusakan',
                    'downtime.jenis_maintenance',
                    'downtime.status',
                    'downtime.jam_pengisian',
                    'downtime.jam_mulai_maintenance',
                    'downtime.id',
                )
                ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
                ->where('downtime.jenis_maintenance' , '=', 'Engineering')
                ->where('downtime.status' , '=',  2)
                ->whereNotNull('logging_machine.no_mesin')
                ->whereNotNull('logging_machine.rasa')
                ->orderBy('downtime.tgl_pengisian', 'DESC')
                ->get();

                $maintenance = DB::table('users')
                ->select('name', 'username')
                ->where('username', $nik)
                ->first();

            return view('logging_machine.maintenance.permintaan_baru', compact('engineering', 'maintenance'));
        }

    public function respon_maintenance(Request $request, $id)
    {
        $id = Crypt::decrypt($id);

        $nik = $request->nik_maintenance;

            $data = DB::table('downtime')->where('id', $id)->where('jenis_maintenance', 'Engineering')->update([
                'approval_maintenance' => 0,
                'status' => 1,
                'approval_maintenance_nama' => $request->nama_maintenance,
                'approval_maintenance_nik' => $nik,
                'jam_mulai_maintenance' => date('H:i'),
                'tgl_respon_maintenance' => date('Y-m-d'),
                'progress' => 'Proses Perbaikan',
            ]);

            // dd($data);
            Session::flash('success', 'Permintaan Berhasil Di Proses..');
            return redirect('/logging_machine/maintenance/'. $nik);
    }

    public function get_close_request(Request $request, $id, $nik)
    {
        $id = Crypt::decrypt($id);
        
        $data = DB::table('downtime')
        ->select(
                'downtime.id',
                'downtime.id_logging_machine',
                'logging_machine.nama',
                'logging_machine.nik',
                'logging_machine.no_mesin',
                'logging_machine.shift_group',
                'logging_machine.varian',
                'downtime.tgl_pengisian',
                'downtime.kerusakan',
                'downtime.status',
                'downtime.jenis_maintenance',
                'downtime.approval_maintenance_nik',
                'downtime.jam_pengisian',
                'downtime.jam_mulai_maintenance',
                )
                ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
                ->where('downtime.id', '=', $id)
                ->first();

                $nama_maintenance = DB::table('users')
                ->select('name', 'username')
                ->where('username', $nik)
                ->first();

                $eng = DB::table('users')->where('auth_group_id', '=', 58)->get();

        return view('logging_machine.maintenance.close_request', compact('data', 'eng', 'nama_maintenance', 'nik'));
    }

    public function close_request(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $nik = $request->nik_maintenance;
        $team = $request->team;
        $hitung_team = count($team);

        $jam_mulai_maintenance = $request->jam_mulai_maintenance;

        $waktuawal = date_create($jam_mulai_maintenance); //waktu di setting
        
        $waktuakhir = date_create();
        
        $diff = date_diff($waktuawal, $waktuakhir);

        $data = DB::table('downtime')->where('id', $id)->first();

        if($hitung_team == 1) 
        {
           DB::table('downtime')->where('id', $id)->update([
                'status' => 0,
                'progress' => 'Close Maintenance',
                'jam_selesai_maintenance' => date('H:i'),
                'approval_maintenance_remarks' => $request->approval_maintenance_remarks,
                'progress' => 'Selesai Di Perbaiki',
                'close_operator' => 1,
                'waktu_penyelesaian' => $diff->h . ' ' . 'JAM' . ' ' . $diff->i . ' ' . 'MENIT',
                'tgl_selesai_maintenance' => date('Y-m-d'),

                ]);
            }
        else
        {
            $tes =  DB::table('downtime')->where('id', $id)->update([
            'status' => 0,
            'progress' => 'Close Maintenance',
            'jam_selesai_maintenance' => date('H:i'),
            'approval_maintenance_remarks' => $request->approval_maintenance_remarks,
            'progress' => 'Selesai Di Perbaiki',
            'close_operator' => 1,
            'waktu_penyelesaian' => $diff->h . ' ' . 'JAM' . ' ' . $diff->i . ' ' . 'MENIT',
            'tgl_selesai_maintenance' => date('Y-m-d'),
            ]);

            foreach($team as $list) {
                if($list != $data->approval_maintenance_nik ) 
                {
                    $add_team = DB::table('downtime')->insert([
                    'id_logging_machine' => $data->id_logging_machine,
                    'no_mesin' => $data->no_mesin,
                    'nik' => $data->nik,
                    'tgl_pengisian' => $data->tgl_pengisian,
                    'jam_pengisian' => $data->jam_pengisian,
                    'kode_sap' => $data->kode_sap,
                    'jam_mulai_maintenance' => $data->jam_mulai_maintenance,
                    'jenis_maintenance' => $data->jenis_maintenance,
                    'tgl_respon_maintenance' => $data->tgl_respon_maintenance,
                    'kerusakan' => $data->kerusakan,
                    'approval_maintenance' => $data->approval_maintenance,
                    'status' => 0,
                    'progress' => 'Selesai Di Perbaiki',
                    'jam_selesai_maintenance' => date('H:i'),
                    'approval_maintenance_nama' => $list,
                    'approval_maintenance_remarks' => $request->approval_maintenance_remarks,
                    'close_operator' => 1,
                    'waktu_penyelesaian' => $diff->h . ' ' . 'JAM' . ' ' . $diff->i . ' ' . 'MENIT',
                    'tgl_selesai_maintenance' => date('Y-m-d'),

                    ]);
                }
            }
        }
       
        Session::flash('success', 'Permintaan Berhasil Di Close..');
        
        return redirect('/logging_machine/maintenance/'. $nik);
    }
   
    public function close_operator(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $nik = $request->nik;

        $data = DB::table('downtime')->where('id_logging_machine', $id)->update([
            'close_operator' => 0,
            ]);
            
        Session::flash('success', 'Permintaan Berhasil Di Close..');
        return redirect('/logging_machine/index/'. $nik);
    }

    public function gramatur($status_varian, $no_mesin, $nik)
    {
        
        $nik = Crypt::decrypt($nik);

        $tgl_sekarang = date('Y-m-d');

        $jam = date('H:i:s');

        $data = DB::table('logging_machine')
        ->where('no_mesin', $no_mesin)
        ->where('nik', $nik)
        ->where('pindah_varian', $status_varian)
        ->where('tgl_pengisian', $tgl_sekarang)->first();

        $id_logging_machine = $data->id;

        $selesai_sampling = DB::table('logging_machine')
         ->select('gramatur_logging_machine.jam_ke')
         ->join('gramatur_logging_machine', 'gramatur_logging_machine.id_logging_machine', '=', 'logging_machine.id')
         ->where('gramatur_logging_machine.id_logging_machine', $id_logging_machine)
         ->where('gramatur_logging_machine.nik', $nik)
         ->where('logging_machine.pindah_varian', $status_varian)
         ->where('gramatur_logging_machine.tgl_pengisian', $tgl_sekarang)
         ->where('gramatur_logging_machine.jam_ke', 3)
         ->first();

        $cek_timbangan = DB::table('gramatur_logging_machine')
        ->where('id_logging_machine', $id_logging_machine)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();

        $jadwal = DB::table('master_sampling_s2')->get();

        return view('logging_machine.gramatur.index', compact('data', 'jadwal', 'cek_timbangan', 'selesai_sampling'));
    }
 
    public function post_gramatur(Request $request)
    {
        $tgl_sekarang = date('Y-m-d');
        
        $sampling = $request->sampling_ke;
        
        // dd($request->all());
        $id_logging_machine = $request->id_logging_machine;

        for($i = 0; $i < count($sampling); $i++){ 
            $data = DB::table('gramatur_logging_machine')->insert([
                'tgl_pengisian' => date('Y-m-d'),
                'nik' => $request->nik,
                'id_logging_machine' => $id_logging_machine,
                'jam_pengisian' => date('H:i'),
                'jam_ke' => $request->jam_ke,
                'sampling_ke' => $sampling[$i],
                ]);
            }

    Session::flash('success', 'Sampling Gramatur Berhasil Di Tambah..');

    return redirect('/logging_machine/detail_input/' . Crypt::encrypt($id_logging_machine));
}

    public function form_input_gramatur($status_varian, $no_mesin, $nik)
    {
        $nik = Crypt::decrypt($nik);
        
        $tgl_sekarang = date('Y-m-d');
        $sampling = DB::table('master_sampling_s2')->get();
            
            $jam_ke = [];
            $shift_ke = [];
            foreach ($sampling as $s) {
                $waktu_mulai = new \DateTime($s->waktu_mulai);
                $waktu_selesai = new \DateTime($s->waktu_selesai);
                $waktu_sekarang = new \DateTime();
                // jika waktu sekarang. diantara waktu mulai dan waktu selesai maka gunakan jam ke nya
                if($waktu_sekarang >= $waktu_mulai && $waktu_sekarang <= $waktu_selesai) {
                    $jam_ke[] = $s->jam_ke;
                    $shift_ke[] = $s->shift;
                }
            }
        
         $data = DB::table('logging_machine')
         ->where('no_mesin', $no_mesin)
         ->where('nik', $nik)
         ->where('pindah_varian', $status_varian)
         ->where('tgl_pengisian', $tgl_sekarang)
         ->first();
  
         $pindah_varian = DB::table('logging_machine')
         ->where('no_mesin', $no_mesin)
         ->where('nik', $nik)
         ->where('tgl_pengisian', $tgl_sekarang)
         ->where('pindah_varian', 2)
         ->first();
    
        $range = DB::table('master_range_sampling_s2')->where('varian', $data->rasa)->first();
        $minimum  = (float) $range->minimum;
        $maksimum = (float) $range->maksimum;

            $gramatur = DB::table('gramatur_logging_machine')->select('sampling_ke', 'jam_ke', 'jam_pengisian')->get();

            $validasi_sampling = DB::table('gramatur_logging_machine')
            ->select('jam_ke')
            ->where('nik', $nik)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->where('id_logging_machine',$data->id)
            ->get();

            $validasi_jam = DB::table('gramatur_logging_machine')
            ->select('jam_ke')
            ->where('nik', $nik)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->where('id_logging_machine',$data->id)
            ->first();
            
            $cek_jam = 0;
            if(count($validasi_sampling) != 0){
                $cek_jam = (int) $validasi_jam->jam_ke;
            }
            // dd($cek_jam);
            
            $sampling_arr = [];
            $jam_sampling = [];
            foreach ($gramatur as $val){
                $sampling_arr[] = $val->sampling_ke;
                $jam_sampling[] = $val->jam_ke;

            }


        return view('logging_machine.gramatur.form_input', compact('data', 'jam_ke', 'shift_ke', 'sampling_arr', 'minimum', 'maksimum', 'no_mesin', 'cek_jam'));
    }
    public function history_sampling($nik){
        
        $nik = Crypt::decrypt($nik);
        $tgl_sekarang = date('Y-m-d');

        $list = DB::table('gramatur_logging_machine')
        ->join('logging_machine', 'logging_machine.id', '=', 'gramatur_logging_machine.id_logging_machine')
        ->where('gramatur_logging_machine.tgl_pengisian', $tgl_sekarang)
        ->where('gramatur_logging_machine.nik', $nik)
        ->groupBy('gramatur_logging_machine.jam_ke')->get();
        // dd($list);
        
        return view('logging_machine.gramatur.history', compact('list'));
    }
 
    public function list_history($status_varian, $id_logging_machine, $nik){
        
        $nik = Crypt::decrypt($nik);

        $data = DB::table('gramatur_logging_machine')
        ->join('logging_machine', 'logging_machine.id', '=', 'gramatur_logging_machine.id_logging_machine' )
        ->where('gramatur_logging_machine.nik', $nik)
        ->where('gramatur_logging_machine.id_logging_machine', $id_logging_machine)
        ->get();
        // dd($data);

        return view('logging_machine.gramatur.list_history', compact('data'));
    }

    public function detail_input_gramatur($id_logging_machine)
    {
        $id_logging_machine = Crypt::decrypt($id_logging_machine);

        $tgl_sekarang = date('Y-m-d');

        $jam_ke1 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id_logging_machine)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 1)
        ->get();

        $jam_ke2 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id_logging_machine)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 2)
        ->get();

        $jam_ke3 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id_logging_machine)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 3)
        ->get();

        // dd($jam_ke3);
        $detail = LoggingMachine::find($id_logging_machine);

        return view('logging_machine.gramatur.detail_input', compact('jam_ke1','jam_ke2','jam_ke3', 'detail'));
    }

    public function spv_prod_index()
    {
        $tgl_sekarang = date('Y-m-d');
        
        $data = LoggingMachine::where('tgl_pengisian', $tgl_sekarang)->first();

        return view('logging_machine.spv_produksi.index', compact('data'));
    }
    
    public function spv_downtime(Request $request)
    {
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')->where('tgl_pengisian', $tgl_sekarang)->update([
            'jam_mulai_downtime' => date('H:i:s'),
            'approval_spv_downtime' => 0,
            'approval_spv_nama' => Auth::user()->name,
            'approval_spv_nik' => Auth::user()->nik
        ]);

        Session::flash('success', 'Permintaan Berhasil Di buat..');

        return back();
    }
    
    public function stop_downtime(Request $request)
    {
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')->where('tgl_pengisian', $tgl_sekarang)->update([
            'jam_selesai_downtime' => date('H:i:s'),
            'approval_spv_downtime' => 1,
            'approval_spv_nama' => Auth::user()->name,
            'approval_spv_nik' => Auth::user()->nik
        ]);

        Session::flash('success', 'Permintaan Berhasil Di STOP..');

        return back();
    }
    
    public function checklist_shift()
    {
        $tgl_sekarang = date('Y-m-d');
        $data = LoggingMachine::whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->whereNotNull('total_produksi_box')
        ->where('approval_spv', '=', NULL)
        ->get();

        return view('logging_machine.spv_produksi.checklist.index', compact('data'));
    }
    
    public function detail_shift($rasa, $id)
    {
         $tgl_sekarang = date('Y-m-d');

        $cek_hasil = LoggingMachine::find($id);

        $gramatur = DB::table('gramatur_logging_machine')
        ->join('logging_machine', 'logging_machine.id', '=', 'gramatur_logging_machine.id_logging_machine')
        ->join('master_varian_rasa_s2', 'master_varian_rasa_s2.rasa', '=', 'logging_machine.rasa')
        ->where('gramatur_logging_machine.id_logging_machine', $id)
        ->get();
        
        $varian = DB::table('gramatur_logging_machine')
        ->join('logging_machine', 'logging_machine.id', '=', 'gramatur_logging_machine.id_logging_machine')
        ->where('gramatur_logging_machine.id_logging_machine', $id)
        ->where('logging_machine.rasa', $rasa)
        ->first();

        $downtime = DB::table('downtime')
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.id_logging_machine', $id)
        ->where('downtime.tgl_pengisian', $tgl_sekarang)
        ->get();

        $inner = DB::table('inner_logging_machine')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();
       
        $inner_foto = DB::table('inner_logging_machine')
        ->where('id_logging_machine', $id)
        ->get();
        // dd($inner_foto);
        
        $wip = DB::table('logging_machine_wip')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();
        // dd($wip);

        $kebersihan = DB::table('logging_machine_kebersihan')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->first();

        $jam_ke1 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 1)
        ->get();

        $jam_ke2 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 2)
        ->get();

        $jam_ke3 = DB::table('gramatur_logging_machine')
        ->select('sampling_ke', 'jam_ke')
        ->where('id_logging_machine', $id)
        ->where('tgl_pengisian', $tgl_sekarang)
        ->where('jam_ke', 3)
        ->get();

        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );

         $cek_shift = substr($cek_hasil->shift_group, 0, 1);
         $shift_saya = (int)$cek_shift;

        $ctrpcs = DB::table('logging_machine')
        ->select('logging_machine.id',
                  'logging_machine.nama', 
                  'logging_machine.nik', 
                  'logging_machine.no_mesin', 
                  'logging_machine.shift_group', 
                  'master_mesin_s2.NoSeq'
                 )
        ->join('master_mesin_s2', 'master_mesin_s2.no_mesin', '=', 'logging_machine.no_mesin')
        ->where('logging_machine.nik', $cek_hasil->nik)
        ->where('logging_machine.no_mesin', $cek_hasil->no_mesin)
        ->first();

        if($shift_saya == 1){
            $shift_1_start = date('Y-m-d 07:00:00' );
            $shift_1_end = date('Y-m-d 15:00:00' );

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            ->where('NoSeq', $ctrpcs->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_1_start, $shift_1_end])
            ->orderBy('Tgljam', 'asc')
            ->get();

            $total_pcs = $counter
            ->sum('CntDev');
            $total_box = count($counter);
        }
        else if($shift_saya == 2){
            $shift_2_start = date('Y-m-d 15:00:00' );
            $shift_2_end = date('Y-m-d 23:00:00' );

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            // ->whereDate('Tgljam', $tgl_sekarang)
            ->where('NoSeq', $ctrpcs->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_2_start, $shift_2_end])
            ->orderBy('Tgljam', 'asc')
            ->get();

            $total_pcs = $counter
            ->sum('CntDev');
            $total_box = count($counter);

        }
        else{
            $shift_3_start = date('Y-m-d 00:00:00' );
            $shift_3_end = date('Y-m-d 07:00:00');

            $counter = DB::connection('172.21.5.92')
            ->table('set_point')
            ->where('NoSeq', $ctrpcs->NoSeq)
            ->where('BoxFlag', '=', 'B')
            ->whereBetween('TglJam', [$shift_3_start, $shift_3_end])
            ->orderBy('Tgljam', 'asc')
            ->get();
            $total_pcs = $counter
            ->sum('CntDev');
            $total_box = count($counter);
        }

        return view('logging_machine.spv_produksi.checklist.detail', compact('gramatur', 'jam_ke1', 'jam_ke2', 'jam_ke3',  'inner', 'downtime', 'wip', 'kebersihan', 'cek_hasil', 'varian', 'inner_foto', 'counter', 'total_box', 'total_pcs'));
    }
   
    public function spv_approve(Request $request)
    {
        $id = $request->id;
        foreach($id as $val){
            $data = DB::table('logging_machine')->where('id', $val)->update([
                'approval_spv' => 0,
                'approval_spv_nama' => Auth::user()->name,
                'approval_spv_nik' => Auth::user()->username,
                'approval_spv_date' => date('Y-m-d H:i:s'),
            ]);
        }
    Session::flash('success', 'Data Shift Berhasil Di Approve..');

    return back();
    }

     public function tracking_shift()
    {
        $data = LoggingMachine::whereNotNull('no_mesin')->whereNotNull('rasa')->get();

        return view('logging_machine.spv_produksi.tracking_checklist.index', compact('data'));
    }

    public function adm_prod_index()
    {
        return view('logging_machine.admin_produksi.index');
    }
   
    public function index_master_mesin()
    {
      $group = DB::table('master_mesin_s2')
                    ->groupBy('group')
                    ->get();
     
    $mesin = DB::table('master_mesin_s2')
    ->get();

        return view('logging_machine.admin_produksi.master_mesin.index', compact('group', 'mesin'));
    }
   
    public function post_master_mesin(Request $request)
    {
        $group = $request->group;

            $mesin = $request->no_mesin;
            $jenis_mesin = $request->jenis_mesin;
            $line = $request->line;
            $group = $request->group;
            $NoSeq = $request->NoSeq;
            $workcenter = $request->workcenter;

                $result = DB::table('master_mesin_s2')->insert(
                    [
                        'line' => $line,
                        'group' => $group,
                        'no_mesin' => $mesin,
                        'jenis_mesin' => $jenis_mesin,
                        'NoSeq' => $NoSeq,
                        'workcenter' => $workcenter
                    ]);
        Session::flash('success', 'Data Mesin Berhasil Di Tambah..');
        return back();
    }

    public function import_master_mesin(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $excel = $request->file('file');
        $data =  Excel::import(new MasterMesinImport, $excel);
       Session::flash('success', 'Data Mesin Berhasil Di Tambah..');
        return back();
    }
  
    public function get_master_mesin($id)
    {

        $data = DB::table('master_mesin_s2')
        ->where('id', $id)
        ->first();

        $line = DB::table('master_mesin_s2')
                    ->groupBy('line')
                    ->get();
      
                    $mesin = DB::table('master_mesin_s2')
                    ->get();

      $group = DB::table('master_mesin_s2')
                    ->groupBy('group')
                    ->get();
        
        return view('logging_machine.admin_produksi.master_mesin.edit', compact('line', 'mesin', 'group', 'data'));
    }
  
    public function update_master_mesin(Request $request, $id)
    {
        $data = DB::table('master_mesin_s2')->where('id', $id)->update(
            [
                'line' => $request->line,
                'group' => $request->group,
                'no_mesin' => $request->no_mesin,
                'jenis_mesin' => $request->jenis_mesin,
                'NoSeq' => $request->NoSeq,
            ]);
        // dd($data);
        Session::flash('success', 'Data Mesin Berhasil Di Update..');
        return redirect('/logging_machine/adm_prod/master_mesin/');        
    }
  
    public function delete_master_mesin($id)
    {
        $data = DB::table('master_mesin_s2')->where('id', $id)->delete();
        Session::flash('success', 'Data Mesin Berhasil Di Hapus..');
        return back();
    }

    public function index_master_repair()
    {
        $mesin = DB::table('master_mesin_s2')->get();
        $reason = DB::table('master_reason_s2')->get();
        $repair = DB::table('master_repair_mesin_s2')
        ->get();

        // dd($repair);
        return view('logging_machine.admin_produksi.master_repair.index', compact('mesin', 'repair', 'reason'));
    }

    public function post_master_mesin_repair(Request $request)
    {
            $mesin = $request->no_mesin;
            $jenis_mesin = $request->jenis_mesin;
            $repair = $request->repair;
            $kategori = $request->kategori;
            $reason = $request->reason;
             
                $result = DB::table('master_repair_mesin_s2')->insert(
                    [
                        'jenis_mesin' => $jenis_mesin,
                        'no_mesin' => $mesin,
                        'reason' => $reason,
                        'repair' => $repair,
                        'kategori' => $kategori,
                    ]);
        Session::flash('success', 'Data Repair Mesin Berhasil Di Tambah..');
        return back();
    }

    public function import_master_repair(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $excel = $request->file('file');
        $data =  Excel::import(new MasterRepairImport, $excel);
        // dd($data);
        
       Session::flash('success', 'Data Repair Mesin Berhasil Di Tambah..');
        return back();
    }

    public function get_master_mesin_repair($id, $no_mesin)
    {
        $varian = DB::table('master_varian_rasa_s2')->get();
        $reason = DB::table('master_reason_s2')->get();
        $mesin = DB::table('master_mesin_s2')->get();

        $detail = DB::table('master_repair_mesin_s2')
        ->select(
            'master_mesin_s2.no_mesin',
            'master_repair_mesin_s2.repair',
            'master_repair_mesin_s2.kategori',
            'master_repair_mesin_s2.id',
            'master_repair_mesin_s2.no_mesin',
            'master_repair_mesin_s2.jenis_mesin',
            'master_repair_mesin_s2.reason',
            )
        ->join('master_mesin_s2', 'master_mesin_s2.no_mesin', '=', 'master_repair_mesin_s2.no_mesin')
        ->where('master_mesin_s2.no_mesin', $no_mesin)
        ->first();
        // dd($detail);
        
        return view('logging_machine.admin_produksi.master_repair.edit', compact('mesin', 'varian', 'detail', 'reason'));
    }

    public function get_master_reason()
    {
        $data = DB::table('master_reason_s2')->get();   
        return view('logging_machine.admin_produksi.master_reason.index', compact('data'));
    }
 
    public function post_master_reason(Request $request)
    {
        $data = DB::table('master_reason_s2')->insert(
            [
                'reason' => $request->reason,
                'kode_reason' => $request->kode_reason,
                'kategori' => $request->kategori,
            ]);
            Session::flash('success', 'Data Master Reason Di Simpan..');
            return back(); 
    }
  
    public function delete_master_reason($id)
    {
        $data = DB::table('master_reason_s2')->where('id', $id)->delete();
            Session::flash('success', 'Data Master Reason Di Hapus..');
            return back(); 
    }

    public function update_master_mesin_repair(Request $request, $id)
    {
        $mesin = $request->no_mesin;
        $jenis_mesin = $request->jenis_mesin;
        $repair = $request->repair;
        $kategori = $request->kategori;
        $reason = $request->reason;

        $data = DB::table('master_repair_mesin_s2')->where('id', $id)->update(
            [
                'jenis_mesin' => $jenis_mesin,
                'no_mesin' => $mesin,
                'reason' => $reason,
                'repair' => $repair,
                'kategori' => $kategori,
            ]);
        // dd($data);
        Session::flash('success', 'Data Master Mesin Repair Di Update..');
        return redirect('/logging_machine/adm_prod/');        
    }

    public function delete_master_mesin_repair($id)
    {
        $data = DB::table('master_repair_mesin_s2')->where('id', $id)->delete();
        Session::flash('success', 'Data Repair Mesin Berhasil Di Hapus..');
        return back();
    }

    public function index_master_varian()
    {
        $varian = DB::table('master_varian_rasa_s2')->get();

        // dd($varian);
        return view('logging_machine.admin_produksi.master_varian_rasa.index', compact('varian'));
    }

    public function post_master_varian(Request $request)
    {
        $varian = $request->varian;

        // dd($request->all());
        for($i = 0; $i < count($varian); $i++){
            if($varian[$i] != NULL  ){
                $result = DB::table('master_varian_rasa_s2')->insert(
                    [
                        'rasa' => $varian[$i],
                    ]);
            }
        }
        Session::flash('success', 'Data Varian Rasa Berhasil Di Tambah..');
        return back();
    }

    public function get_master_varian($id)
    {

        $detail = DB::table('master_varian_rasa_s2')
        ->where('id', $id)
        ->first();
        // dd($detail);
        
        return view('logging_machine.admin_produksi.master_varian_rasa.edit', compact('detail'));
    }

    public function update_master_varian(Request $request, $id)
    {
        $data = DB::table('master_varian_rasa_s2')->where('id', $id)->update(
            [
                'rasa' => $request->varian,
            ]);
        // dd($data);
        Session::flash('success', 'Data Varian Rasa Berhasil Di Update..');
        return redirect('/logging_machine/adm_prod/master_varian/');        
    }

    public function delete_master_varian($id)
    {
        $data = DB::table('master_varian_rasa_s2')->where('id', $id)->delete();
        Session::flash('success', 'Data Varian Rasa Berhasil Di Hapus..');
        return back();
    }

    public function index_master_sampling()
    {
        $sampling = DB::table('master_sampling_s2')->get();

        return view('logging_machine.admin_produksi.master_sampling.index', compact('sampling'));
    }

    public function post_master_sampling(Request $request)
    {
        $waktu_mulai = $request->waktu_mulai;
        $jam_ke = $request->jam_ke;
        $waktu_selesai = $request->waktu_selesai;
                $result = DB::table('master_sampling_s2')->insert(
                    [
                        'shift' => $request->shift,
                        'jam_ke' => $jam_ke,
                        'waktu_mulai' => $waktu_mulai,
                        'waktu_selesai' => $waktu_selesai,
                        ]);
                // dd($result);
        Session::flash('success', 'Data Master Sampling Berhasil Di Tambah..');
        return back();
    }

    public function import_master_gramatur(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $excel = $request->file('file');
        $data =  Excel::import(new MasterSamplingGramaturImport, $excel);
        // dd($data);
        
       Session::flash('success', 'Data Jadwal Sampling Berhasil Di Tambah..');
        return back();
    }

    public function delete_master_sampling($id)
    {
        $data = DB::table('master_sampling_s2')->where('id', $id)->delete();
        Session::flash('success', 'Data Master Sampling Berhasil Di Hapus..');
        return back();
    }

    public function get_master_sampling($id)
    {

        $detail = DB::table('master_sampling_s2')
        ->where('id', $id)
        ->first();
        // dd($detail);
        
        return view('logging_machine.admin_produksi.master_sampling.edit', compact('detail'));
    }

    public function update_master_sampling(Request $request, $id)
    {
        $data = DB::table('master_sampling_s2')->where('id', $id)->update(
            [
                'jam_ke' => $request->jam_ke,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai
            ]);
        // dd($data);
        Session::flash('success', 'Data sampling Berhasil Di Update..');
        return redirect('/logging_machine/adm_prod/master_sampling/');        
    }

    public function get_mesin($group)
    {
        $data = DB::table('master_mesin_s2')
        ->where('group', $group)
        ->orderBy('no_mesin', 'DESC')
        ->get();
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
 
    public function get_rasa($group)
    {
        $data = DB::table('master_planning_s2')
        ->where('group', $group)
        ->orderBy('group', 'ASC')
        ->get();
        
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
   
    public function get_rasa_before($no_mesin)
    {
        $data = DB::table('logging_machine')
        ->where('no_mesin', $no_mesin)
        ->get();
        
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
   
    public function get_reason($kategori)
    {
        $data = DB::table('master_reason_s2')
        ->where('kategori', $kategori)
        ->orderBy('kategori', 'ASC')
        ->get();
        
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
   
    public function get_repair($no_mesin)
    {
        $data = DB::table('master_repair_mesin_s2')
        ->where('no_mesin', $no_mesin)
        ->get();
        return json_encode($data);
    }
  
    public function get_kategori($jenis_mesin)
    {
        $data = DB::table('master_mesin_s2')
        ->where('jenis_mesin', $jenis_mesin)
        ->get();

        return json_encode($data);
    }
   
    public function get_maintenance($no_mesin)
    {
        $data = DB::table('master_repair_mesin_s2')
        ->where('no_mesin', $no_mesin)
        ->get();

        return json_encode($data);
    }
    
    public function downtime_monitoring()
    {
        $data = [];
        $mesin = DB::table('master_mesin_s2')->where('group', '!=', '-' )->orderBy('no_mesin', 'DESC')->get();
        
        $tabel = DB::table('downtime')
        ->select('*')
        ->join('logging_machine', 'logging_machine.no_mesin', 'downtime.no_mesin')
        ->where('downtime.status', '!=', 0)->get();
        // dd($list);

        foreach($mesin as $key => $m)
        {
            $downtime = DB::table('downtime')
            ->join('logging_machine', 'logging_machine.no_mesin', '=', 'downtime.no_mesin')
            ->where('downtime.no_mesin', $m->no_mesin)
            ->where('status', '!=', 0)->first();
        
            if($downtime != null)
            {
                $data[] = ["downtime" => true, "id" => $downtime->id, "jenis_downtime" => $downtime->jenis_maintenance, "status" => $downtime->status, "jam_pengisian" => $downtime->jam_pengisian,  "mesin" => $m]; // Ijo ;
                // dd($data);
            }else{
                $data[] = ["id" => '_'.$key, "downtime" => false, "jenis_downtime" => "", "status" => "", "mesin" => $m]; // Ijo ;
            }
            // dd($data);
        }
        return view('logging_machine.downtime.monitoring', compact('downtime', 'data','tabel'));
    }
    public function ajax_monitoring()
    {
        $mesin = DB::table('downtime')
        ->join('logging_machine', 'logging_machine.no_mesin', '=', 'downtime.no_mesin')
        ->where('status', '!=', 0)
        ->orderBy('downtime.no_mesin', 'DESC')->get();

      return response()->json([
          'status' => 1,
          'data' => $mesin]);
    }

    public function logout_maintenance()
    {
        Auth::logout();
        return redirect('/');
    }
}
