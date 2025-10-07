<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Alert;
use Illuminate\Support\Facades\Auth;
use Session;
use App\LoggingMachine;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DowntimeExport;
use Carbon\Carbon;
use App\Imports\ProductionOrderImport;


class ReportLoggingMachineController extends Controller
{
    public function index(){
        $list = LoggingMachine::all();
        $count = LoggingMachine::count();
        // dd($count);
        return view('logging_machine.admin_produksi.report.index', compact('list', 'count'));
    }
 
    public function index_downtime(){
        
        $grafik = DB::table('downtime')->select(\DB::raw("COUNT(*) as count"))
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereYear('downtime.tgl_pengisian', date('Y'))
        ->groupBy(\DB::raw("Month(downtime.tgl_pengisian)"))
        ->pluck('count');

        $bulan = DB::table('downtime')->select(\DB::raw("Month(tgl_pengisian) as month"))
        ->whereYear('tgl_pengisian', date('Y'))
        ->groupBy(\DB::raw("Month(tgl_pengisian)"))
        ->pluck('month');


        $data = array(0,0,0,0,0,0,0,0,0,0,0,0,0);

        foreach($bulan as $index => $value){
            $data[$value] = $grafik[$index];
        }
        // dd($data);
        $list = DB::table('downtime')
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.status', '=', 0)
        ->get();

        return view('logging_machine.admin_produksi.report.downtime.index', compact(
            'grafik',
            'list',
            'data'
        ));
    }
    
    public function result_downtime(Request $request){

        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $result = DB::table('downtime')
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->join('logging_machine_no_prod', 'logging_machine_no_prod.no_mesin', '=', 'downtime.no_mesin')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->where('downtime.status', '=', 0)
        ->where('logging_machine_no_prod.tgl_pengisian',  date('Y-m-d'))
        ->get();

        $count = DB::table('downtime')->count();
        return view('logging_machine.admin_produksi.report.downtime.result', compact('result', 'count', 'tgl_mulai', 'tgl_selesai'));
    }

    public function export_excel_downtime(Request $request){
        return Excel::download(new DowntimeExport($request->tgl_mulai, $request->tgl_selesai), 'Report-Downtime-' . time() . '.xls');
    }

    public function index_mesin(){

        $list = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_mulai_maintenance',
            'downtime.jam_selesai_maintenance',
            'downtime.progress',
            'downtime.status',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.status', '=', 0)
        ->whereNotNull('downtime.no_mesin')
        ->get();

        $mesin = DB::table('downtime')
        ->select(
            'downtime.no_mesin',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->groupBy(\DB::raw("downtime.no_mesin"))
        ->where('downtime.status', '=', 0)
        ->whereNotNull('downtime.no_mesin')
        ->get();
        // dd($mesin);

        $nama_mesin = [];
        $total = [];
        
        foreach($mesin as $val){
            $nama_mesin[] = $val->no_mesin;
            $total[] = DB::table('downtime')->where('no_mesin', $val->no_mesin)->count();
        }
        // dd($total);
      
        return view('logging_machine.admin_produksi.report.mesin.index', compact(
            'list', 
            'nama_mesin',
            'total'
        ));
    }

    public function result_mesin(Request $request){

        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $result = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_mulai_maintenance',
            'downtime.jam_selesai_maintenance',
            'downtime.progress',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->where('downtime.status', '=', 0)
        ->get();

        // dd($result);

        $mesin = DB::table('downtime')
        ->select(
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.status',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->groupBy(\DB::raw("downtime.no_mesin"))
        ->where('downtime.status', '=', 0)
        ->get();
        // dd($mesin);
        

        $nama_mesin = [];
        $total = [];
        
        foreach($mesin as $val){
            $nama_mesin[] = $val->no_mesin;
            $total[] = DB::table('downtime')
            ->where('no_mesin', $val->no_mesin)
            ->groupBy('no_mesin')
            ->whereBetween('tgl_pengisian', [$tgl_mulai, $tgl_selesai])
            ->count();
        }
      
        return view('logging_machine.admin_produksi.report.mesin.result', compact(
            'result', 
            'nama_mesin',
            'total',
            'tgl_mulai',
            'tgl_selesai'
        ));
    }

    public function index_operator(){

        $list = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_mulai_maintenance',
            'downtime.jam_selesai_maintenance',
            'downtime.jam_pengisian',
            'downtime.progress',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.status', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Operator')
        ->get();

        $maintenance = DB::table('downtime')
        ->select(
            'downtime.approval_maintenance_nama',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->groupBy(\DB::raw("downtime.approval_maintenance_nama"))
        ->where('downtime.status', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Operator')
        ->get();
        
        // dd($maintenance);

        $nama_maintenance = [];
        $total = [];
        
        foreach($maintenance as $val){
            $nama_maintenance[] = $val->approval_maintenance_nama;
            $total[] = DB::table('downtime')
            ->where('approval_maintenance_nama', $val->approval_maintenance_nama)
            ->where('jenis_maintenance', '=', 'Operator')
            ->where('status', '=', 0)
            ->count();
        }
      
        return view('logging_machine.admin_produksi.report.operator.index', compact(
            'list', 
            'nama_maintenance',
            'total'
        ));
    }
    
   public function result_operator(Request $request){
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $result = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_pengisian',
            'downtime.jam_selesai_maintenance',
            'downtime.progress',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->where('status', '=', 0)
        ->where('jenis_maintenance', '=', 'Operator')
        ->get();

        $maintenance = DB::table('downtime')
        ->select(
            'downtime.approval_maintenance_nama',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->groupBy(\DB::raw("downtime.approval_maintenance_nama"))
        ->where('status', '=', 0)
        ->where('jenis_maintenance', '=', 'Operator')
        ->get();
        $nama_maintenance = [];
        $total = [];
        
        foreach($maintenance as $val){
            $nama_maintenance[] = $val->approval_maintenance_nama;
            $total[] = DB::table('downtime')
            ->where('approval_maintenance_nama', $val->approval_maintenance_nama)
            ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
            ->where('jenis_maintenance', '=', 'Operator')
            ->count();
        }
      
        return view('logging_machine.admin_produksi.report.operator.result', compact(
            'result', 
            'nama_maintenance',
            'total',
            'tgl_mulai',
            'tgl_selesai'
        ));
    }
    
    public function index_maintenance(){

        $list = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_mulai_maintenance',
            'downtime.jam_selesai_maintenance',
            'downtime.progress',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->where('downtime.status', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Engineering')
        ->get();

        $maintenance = DB::table('downtime')
        ->select(
            'downtime.approval_maintenance_nama',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->groupBy(\DB::raw("downtime.approval_maintenance_nama"))
        ->where('downtime.approval_maintenance', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Engineering')
        ->get();
        

        $nama_maintenance = [];
        $total = [];
        
        foreach($maintenance as $val){
            $nama_maintenance[] = $val->approval_maintenance_nama;
            $total[] = DB::table('downtime')
            ->where('approval_maintenance_nama', $val->approval_maintenance_nama)
            ->where('jenis_maintenance', '=', 'Engineering')
            ->where('status', '=', 0)
            ->count();
        }
      
        return view('logging_machine.admin_produksi.report.maintenance.index', compact(
            'list', 
            'nama_maintenance',
            'total'
        ));
    }
    
    public function result_maintenance(Request $request){
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $result = DB::table('downtime')
        ->select(
            'logging_machine.nama',
            'downtime.no_mesin',
            'downtime.tgl_pengisian',
            'downtime.kerusakan',
            'downtime.approval_maintenance_nama',
            'downtime.jenis_maintenance',
            'downtime.jam_mulai_maintenance',
            'downtime.jam_selesai_maintenance',
            'downtime.progress',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->where('downtime.status', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Engineering')
        ->get();

        $maintenance = DB::table('downtime')
        ->select(
            'downtime.approval_maintenance_nama',
            )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->groupBy(\DB::raw("downtime.approval_maintenance_nama"))
        ->where('downtime.approval_maintenance', '=', 0)
        ->where('downtime.jenis_maintenance', '=', 'Engineering')
        ->get();
        $nama_maintenance = [];
        $total = [];
        
        foreach($maintenance as $val){
            $nama_maintenance[] = $val->approval_maintenance_nama;
            $total[] = DB::table('downtime')
            ->where('approval_maintenance_nama', $val->approval_maintenance_nama)
            ->whereBetween('downtime.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
            ->where('downtime..jenis_maintenance', '=', 'Engineering')
            ->where('downtime.approval_maintenance', '=', 0)
            ->count();
        }
      
        return view('logging_machine.admin_produksi.report.maintenance.result', compact(
            'result', 
            'nama_maintenance',
            'total'
        ));
    }

    public function packing_harian(){
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->get();
        // dd($data);
        $pencarian_rasa = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('rasa')
        ->get();
        
        $pencarian_nama = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('nama')
        ->get();
       
        $pencarian_shift = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('shift_group')
        ->get();

        return view('logging_machine.admin_produksi.report.packing.report_harian', compact('data', 'pencarian_rasa', 'pencarian_nama', 'pencarian_shift'));
    }
   
    public function packing_harian_result(Request $request, $kategori)
    {
        $tgl_sekarang = date('Y-m-d');

        $pencarian_rasa = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('rasa')
        ->get();

        $pencarian_nama = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('nama')
        ->get();

        $pencarian_shift = DB::table('logging_machine')
        ->where('tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('shift_group')
        ->get();

        if($kategori == 'varian')
        {
            $result = DB::table('logging_machine')
            ->where('rasa', $request->varian)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->whereNotNull('rasa')
            ->whereNotNull('no_mesin')
            ->get();
            return view('logging_machine.admin_produksi.report.packing.report_harian_result', compact('result', 'pencarian_rasa', 'pencarian_nama', 'pencarian_shift'));
        }

        if($kategori == 'nama')
        {
            $result = DB::table('logging_machine')
            ->where('nama', $request->nama)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->whereNotNull('rasa')
            ->whereNotNull('no_mesin')
            ->get();
            return view('logging_machine.admin_produksi.report.packing.report_harian_result', compact('result', 'pencarian_rasa', 'pencarian_nama', 'pencarian_shift'));
        }
       
        if($kategori == 'shift')
        {
            $result = DB::table('logging_machine')
            ->where('shift_group', $request->shift_group)
            ->where('tgl_pengisian', $tgl_sekarang)
            ->whereNotNull('rasa')
            ->whereNotNull('no_mesin')
            ->get();
            return view('logging_machine.admin_produksi.report.packing.report_harian_result', compact('result', 'pencarian_rasa', 'pencarian_nama', 'pencarian_shift'));
        }
    }
 
    public function packing_all_list(){
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine')
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->orderBy('tgl_pengisian', 'DESC')
        ->get();

         $pencarian_rasa = DB::table('logging_machine')
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('rasa')
        ->get();
        
        $pencarian_nama = DB::table('logging_machine')
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('nama')
        ->get();

        return view('logging_machine.admin_produksi.report.packing.report_all_list ', compact('data', 'pencarian_rasa', 'pencarian_nama'));
    }
    public function packing_all_list_result(Request $request, $kategori){

         $pencarian_rasa = DB::table('logging_machine')
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('rasa')
        ->orderBy('tgl_pengisian', 'DESC')
        ->get();

        $pencarian_nama = DB::table('logging_machine')
        ->whereNotNull('rasa')
        ->whereNotNull('no_mesin')
        ->groupBy('nama')
        ->orderBy('tgl_pengisian', 'DESC')
        ->get();
        if($kategori == 'varian')
        {
            $result = DB::table('logging_machine')
            ->where('rasa', $request->varian)
            ->whereNotNull('rasa')
            ->whereNotNull('no_mesin')
            ->orderBy('tgl_pengisian', 'DESC')
            ->get();
            return view('logging_machine.admin_produksi.report.packing.report_all_list_result', compact('result', 'pencarian_rasa', 'pencarian_nama'));
        }

        if($kategori == 'nama')
        {
            $result = DB::table('logging_machine')
            ->where('nama', $request->nama)
            ->whereNotNull('rasa')
            ->whereNotNull('no_mesin')
            ->orderBy('tgl_pengisian', 'DESC')
            ->get();
            return view('logging_machine.admin_produksi.report.packing.report_all_list_result', compact('result', 'pencarian_rasa', 'pencarian_nama'));
        }
       
        if($kategori == 'tgl')
        {
                $tgl_mulai = $request->tgl_mulai;
                $tgl_selesai = $request->tgl_selesai;
                $result = DB::table('logging_machine')
                ->whereNotNull('rasa')
                ->whereNotNull('no_mesin')
                ->orderBy('tgl_pengisian', 'DESC')
                ->whereBetween('tgl_pengisian',[$tgl_mulai, $tgl_selesai])
                ->get();
            return view('logging_machine.admin_produksi.report.packing.report_all_list_result', compact('result', 'pencarian_rasa', 'pencarian_nama'));
        }
    }

    public function show_packing_harian($rasa, $id){
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

        return view('logging_machine.admin_produksi.report.packing.report_harian_detail', compact('gramatur', 'jam_ke1', 'jam_ke2', 'jam_ke3',  'inner', 'downtime', 'wip', 'kebersihan', 'cek_hasil', 'varian', 'inner_foto', 'counter', 'total_box', 'total_pcs'));
    }

    public function report_varian(){
        $tgl_sekarang = date('Y-m-d');

        $list_varian = DB::table('master_planning_s2')->get();

        $group = DB::table('logging_machine')
        ->join('master_planning_s2', 'master_planning_s2.varian_rasa', 'logging_machine.rasa')
        ->where('logging_machine.tgl_pengisian', $tgl_sekarang)
        ->whereNotNull('logging_machine.total_produksi_box')
        ->whereNotNull('logging_machine.hasil_produksi_pcs')
        ->get();

        $rasa_varian = [];
        $planning_arr = [];
        $data_hasil_produksi = [];

        foreach($group as $val){
            
            $rasa_varian[] = $val->no_mesin;
            $planning_arr[] = $val->target_box;
            $data_hasil_produksi[] = $val->total_produksi_box;

        }

        return view('logging_machine.admin_produksi.report.varian.index', compact(
            'data_hasil_produksi',
            'rasa_varian',
            'planning_arr',
            'list_varian'
        ));
    }
    
    public function result_report_varian(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;
        $req_rasa = $request->varian_rasa;

        $list_varian = DB::table('master_planning_s2')->get();
        
         $group = DB::table('logging_machine')
         ->select(
             'logging_machine.rasa',
             'logging_machine.shift_group',
             'logging_machine.total_produksi_box',
             'logging_machine.no_mesin',
             'logging_machine.nama',
             'logging_machine.tgl_pengisian',
             'logging_machine.hasil_produksi_pcs',
             'master_planning_s2.target_pcs',
             'master_planning_s2.target_box',
             )
        ->join('master_planning_s2', 'master_planning_s2.varian_rasa', 'logging_machine.rasa')
        ->where('logging_machine.rasa', $req_rasa)
        ->whereNotNull('logging_machine.total_produksi_box')
        ->whereNotNull('logging_machine.hasil_produksi_pcs')
        ->whereBetween('logging_machine.tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        // ->groupBy('logging_machine.no_mesin')
        ->take(250)
        ->get();
        // dd($group);
        
        $rasa_varian = [];
        $planning_arr = [];
        $data_hasil_produksi = [];
        $data_target_pcs    = '';
        
        foreach($group as $val){
            $rasa_varian[] = $val->no_mesin;
            $planning_arr[] = $val->target_box;
            $data_target_pcs = $val->target_pcs;
            $data_hasil_produksi[] = $val->total_produksi_box;
        }

        $total_box   = array_sum($data_hasil_produksi);

        return view('logging_machine.admin_produksi.report.varian.result', compact(
            'data_target_pcs',
            'req_rasa',
            'tgl_mulai',
            'tgl_selesai',
            'data_hasil_produksi',
            'rasa_varian',
            'planning_arr',
            'group',
            'list_varian',
            'total_box'
        ));
    }

     public function upload_prod_order(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $excel = $request->file('file');
        $data =  Excel::import(new ProductionOrderImport, $excel);
       Session::flash('success', 'Data Berhasil Di Upload..');
        return back();
    }

    public function tracking_file()
    {
        $tgl_sekarang = date('Y-m-d');

        $data = DB::table('logging_machine_no_prod')->get();

        return view('logging_machine.admin_produksi.report.excel.index', compact(
            'data',
        ));
    }
   
    public function edit_file($id)
    {
        $detail = DB::table('logging_machine_no_prod')->where('id', $id)->first();

        return view('logging_machine.admin_produksi.report.excel.edit', compact(
            'detail',
        ));
    }
    
    public function update_file(Request $request, $id)
    {

        $detail = DB::table('logging_machine_no_prod')->where('id', $id)->update([
                    'prod_order' => $request->prod_order,
                    'varian' => $request->varian,
                    'shift' => $request->shift,
                    'group' => $request->group,
                    'jam_edit' => date('H:i'),
                    'tgl_edit' => date('Y-m-d'),
                ]);

            Session::flash('success', 'Data Berhasil Di Update..');
            return redirect('/logging_machine/adm_prod/tracking_file');
    }
   
    public function hapus_file($id)
    {

        $detail = DB::table('logging_machine_no_prod')->where('id', $id)->delete();

            Session::flash('success', 'Data Berhasil Di Hapus..');
            return redirect('/logging_machine/adm_prod/tracking_file');
    }
    
    public function cari_file(Request $request)
    {
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        $data = DB::table('logging_machine_no_prod')
        ->whereBetween('tgl_pengisian', [$tgl_mulai, $tgl_selesai])
        ->get();

        return view('logging_machine.admin_produksi.report.excel.result', compact('data'));
    }

}
