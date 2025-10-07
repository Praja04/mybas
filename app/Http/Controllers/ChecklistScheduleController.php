<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\ChecklistPeriode;
use App\ChecklistSchedule;
use App\Asset;
use App\JenisAsset;
use App\TChecklistKaca;
use App\TChecklistApar;
use App\TChecklistFlyCatcher;
use App\Department;

class ChecklistScheduleController extends Controller
{
    public function index()
    {
        $schedules = ChecklistSchedule::orderBy('jenis_asset_id', 'desc')->orderBy('year', 'desc')->orderBy('month', 'desc')->orderBy('week', 'desc')->orderBy('dept_id', 'asc')->get();
        $jenis_assets = JenisAsset::all();
        $departments = Department::orderBy('name', 'asc')->get();
        return view('master.checklist-schedule', [
            'schedules' => $schedules,
            'jenis_assets' => $jenis_assets,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        // Hitung jumlah asset berdasarkan jenis asset dan department

        $assets = Asset::where('dept_id', $request->department)->where('jenis_asset_id', $request->jenis_asset)->get();


        $schedule = new ChecklistSchedule;
        $schedule->jenis_asset_id = $request->jenis_asset;
        $schedule->dept_id = $request->department;
        $schedule->year = $request->tahun ? $request->tahun : date('Y');
        $schedule->month = $request->bulan ? $request->bulan : date('m');
        $schedule->week = $request->minggu;
        $schedule->asset_count = count($assets);
        if($schedule->save())
        {
            return response()->json(['success' => 1], 200);
        }
    }

    public function create(Request $request)
    {
        $schedule = new ChecklistSchedule;
        $schedule->jenis_asset_id = $request->jenis_asset_id;
        $schedule->year = $request->year ? $request->year : date('Y');
        $schedule->month = $request->month ? $request->month : date('m');
        if($schedule->save())
        {
            return response()->json(['success' => 1], 200);
        }
    }

    public function report()
    {
        $monthly = [];
        $periode_monthly = ChecklistPeriode::where('periode', 'monthly')->get();
        foreach ($periode_monthly as $data) {
            $schedule = ChecklistSchedule::where('jenis_asset_id', $data['jenis_asset_id'])
            ->where('year', date('Y'))
            ->where('month', date('m'))
            ->first();
            if($schedule == null)
            {
                $monthly[] = $data;
            }else{
                $monthly[] = $schedule;
            }
        }
        return view('checklist.report', ['monthly' => $monthly]);
    }

    public function get_one_schedule($schedule_id)
    {
        $data = [];
        $checked = [];
        $items = [];
        if($schedule_id == '0')
        {
            $data['schedule_id'] = '';
            $data['checked'] = 0;
            $data['asset_count'] = 0;
            $data['month'] = '';
            $data['year'] = 0;
            $data['jenis_asset_name'] = '';
            $data['items'] = $items;
        }else{
            $schedule = ChecklistSchedule::where('id', $schedule_id)->first();
            $all_asset = Asset::where('jenis_asset_id', $schedule['jenis_asset_id'])->orderBy('dept_id')->get();
            $jenis_asset = JenisAsset::where('id', $schedule['jenis_asset_id'])->first();

            if($jenis_asset['name'] == 'KACA') {
                $checklists = TChecklistKaca::where('checklist_schedule_id', $schedule['id'])->get();
            }elseif($jenis_asset['name'] == 'APAR') {
                $checklists = TChecklistApar::where('checklist_schedule_id', $schedule['id'])->get();
            }

            $data['schedule_id'] = $schedule['id'];
            $data['checked'] = count($checklists);
            $data['asset_count'] = count($all_asset);
            $data['month'] = $schedule['month'];
            $data['year'] = $schedule['year'];
            $data['jenis_asset_name'] = $jenis_asset['name'];

            foreach($checklists as $checklist)
            {
                array_push($checked, $checklist['asset_id']);
            }
            // dd($all_asset);
            foreach($all_asset as $asset)
            {
                $item = [];
                $item['asset_id'] = $asset['id'];
                $item['asset_name'] = $asset['name'];
                $item['asset_location'] = $asset['location'];
                $item['department_location'] = $asset->department->name;
                if(in_array($asset['id'],$checked))
                {
                    // Jika sudah di cek
                    $item['checked'] = '1';
                }else{
                    $item['checked'] = '0';
                }
                $items[] = $item;
            }
            $data['items'] = $items;
        }
        return response()->json(['data' => $data]);
    }

    public function android_get_all_schedule()
    {
        $periode = ChecklistPeriode::all();
        $data = [];
        
        foreach($periode as $_periode)
        {
            $_data = [];
            $_data['asset'] = $_periode->jenis_asset->name;
            $_data['periode'] = $_periode->periode;
            $_data['day_no'] = $_periode->day_no;

            $schedule = ChecklistSchedule::where('jenis_asset_id', $_periode['jenis_asset_id'])
            ->first();
            $all_asset = Asset::where('jenis_asset_id', $_periode['jenis_asset_id'])->get();
            $jumlah_asset = count($all_asset);
            if($schedule != null)
            {
                // Kalau sudah ada schedule
                // $_data['checked'] = 0;
                if($_periode->jenis_asset->name == 'KACA') {
                    $checklist_kaca = TChecklistKaca::where('checklist_schedule_id', $schedule['id'])->get();
                    $_data['checked'] = count($checklist_kaca);
                }elseif($_periode->jenis_asset->name == 'APAR') {
                    $checklist_kaca = TChecklistApar::where('checklist_schedule_id', $schedule['id'])->get();
                    $_data['checked'] = count($checklist_kaca);
                }
                $_data['schedule_id'] = $schedule['id'];
                $_data['asset_count'] = $jumlah_asset;
            }else{
                // Kalau belum ada schedule
                $_data['checked'] = 0;
                $_data['asset_count'] = $jumlah_asset;
                $_data['schedule_id'] = 0;
            }

            $data[] = $_data;
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'display all schedule',
                'data' => $data
                // 'data' => [
                //     [
                //     'asset' => 'Pengecekan Kaca',
                //     'periode' => 'Monthly',
                //     'day_no' => '28',
                //     'checked' => 20,
                //     'assetCount' => 40
                //     ]
                // ]
            ]
        );
    }

    public function get_schedule_kaca()
    {
        $jenis_asset = JenisAsset::where('name', 'KACA')->first();
        $periode = JenisAsset::find($jenis_asset->id)->periode()->first();
        $schedule = ChecklistSchedule::where('jenis_asset_id', $jenis_asset->id)->orderBy('created_at','desc')->get();

        $all_asset = Asset::where('jenis_asset_id', $jenis_asset->id)->get();
        $jumlah_asset = count($all_asset);

        $data = [];

        foreach ($schedule as $_schedule) {
            $_data = [];
            $_data['schedule_id'] = $_schedule->id;
            $_data['month'] = $_schedule->month;
            $_data['year'] = $_schedule->year;
            $_data['created_at'] = explode(' ', $_schedule->created_at)[0];
            $checklist_kaca = TChecklistKaca::where('checklist_schedule_id', $_schedule->id)->get();
            $_data['checked'] = count($checklist_kaca);
            // $_data['checked'] = 10;
            $_data['asset_count'] = $jumlah_asset;
            $data[] = $_data; 
        }

        return response()->json([
            'data' => [
                'periode' => $periode,
                'schedule' => $data
            ]
        ]);
    }

    public function apiGetByJenisAsset($jenis_asset_id)
    {
        //Jenis asset
        $jenis_asset = JenisAsset::find($jenis_asset_id);

        $schedule = [];
        $_schedule = ChecklistSchedule::where('jenis_asset_id', $jenis_asset_id)->orderBy('created_at','desc')->get();

        foreach ($_schedule as $data) {
            if ($jenis_asset->slug == "fly-catcher") {
                // Get checklist fly catcher
                $checklist = TChecklistFlyCatcher::where('checklist_schedule_id', $data['id'])->get();
            }elseif ($jenis_asset->slug == "kaca") {
                $checklist = TChecklistKaca::where('checklist_schedule_id', $data['id'])->get();
                // $checklist = 5;
            }elseif ($jenis_asset->slug == "apar") {
                $checklist = TChecklistApar::where('checklist_schedule_id', $data['id'])->get();
            }else{
                $checklist = [];
            }

            $schedule[] = [
                'id'            => $data['id'],
                'jenis_asset_id'=> $data['jenis_asset_id'],
                'year'          => $data['year'],
                'month'         => $data['month'],
                'week'          => $data['week'].' - '.$data->department->name,
                'checked'       => count($checklist),
                'jumlah_asset'  => $data['asset_count'],
                'start_time'    => Carbon::parse($data['created_at'])->format('d/m/Y H:i:s')
            ];
        }
        return response()->json([
            'success' => 1,
            'data' => $schedule
        ]);
    }

    public function apiGetChecklist($schedule_id)
    {
        $data = [];
        $checked = [];
        $items = [];
        if($schedule_id == '0')
        {
            $data['schedule_id'] = '';
            $data['checked'] = 0;
            $data['asset_count'] = 0;
            $data['month'] = '';
            $data['year'] = 0;
            $data['jenis_asset_name'] = '';
            $data['items'] = $items;
        }else{
            $schedule = ChecklistSchedule::where('id', $schedule_id)->first();
            $all_asset = Asset::where('jenis_asset_id', $schedule['jenis_asset_id'])
                            ->where('dept_id', $schedule['dept_id'])
                            ->orderBy('dept_id')
                            ->orderBy('jenis_asset_id')
                            ->get();
            $jenis_asset = JenisAsset::where('id', $schedule['jenis_asset_id'])->first();

            if($jenis_asset['slug'] == 'kaca') {
                $checklists = TChecklistKaca::where('checklist_schedule_id', $schedule['id'])->get();
            }elseif($jenis_asset['slug'] == 'apar') {
                $checklists = TChecklistApar::where('checklist_schedule_id', $schedule['id'])->get();
            }elseif($jenis_asset['slug'] == 'fly-catcher') {
                $checklists = TChecklistFlyCatcher::where('checklist_schedule_id', $schedule['id'])->get();
            }

            $data['schedule_id'] = $schedule['id'];
            $data['checked'] = count($checklists);
            $data['asset_count'] = count($all_asset);
            $data['month'] = $schedule['month'];
            $data['week'] = $schedule['week'].' - '.$schedule['dept_id'];
            $data['year'] = $schedule['year'];
            $data['jenis_asset_name'] = $jenis_asset['name'];

            foreach($checklists as $checklist)
            {
                array_push($checked, $checklist['asset_id']);
            }
            // dd($all_asset);
            foreach($all_asset as $asset)
            {
                $item = [];
                $item['asset_id'] = $asset['id'];
                $item['asset_name'] = $asset['name'];
                $item['asset_location'] = $asset['location'];
                $item['department_location'] = $asset->department->name;
                if(in_array($asset['id'],$checked))
                {
                    // Jika sudah di cek
                    $item['checked'] = '1';
                }else{
                    $item['checked'] = '0';
                }
                $items[] = $item;
            }
            // $data['checked'] = $checked;
            $data['items'] = $items;
        }
        return response()->json([
            'success' => 1,
            'data' => $data
        ]);
    }
}
