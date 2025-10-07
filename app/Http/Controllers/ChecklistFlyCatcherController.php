<?php

namespace App\Http\Controllers;

use App\Asset;
use App\JenisAsset;
use App\ChecklistSchedule;
use App\TChecklistFlyCatcher;
use App\Department;
use Illuminate\Http\Request;

class ChecklistFlyCatcherController extends Controller
{
    public function report()
    {
        $departments = Department::all();
        return view('checklist.fly-catcher.report', [
            'departments' => $departments
        ]);
    }

    public function reportData($year,$month,$week,$dept='')
    {
        if($dept != '') {
            $assets = Asset::where('dept_id', $dept)->get();
        }else{
            $assets = Asset::all();
        }

        $asset_id = [];

        foreach ($assets as $asset) {
            $asset_id[] = $asset->id;
        }

        $jenis_asset = JenisAsset::where('slug', 'fly-catcher')->first();

        if($week == 0) {
            $week = '';
        }

        $schedule = ChecklistSchedule::where('month', $month)
                                        ->where('jenis_asset_id', $jenis_asset->id)
                                        ->where('year', $year)
                                        ->where('week', 'like', '%'.$week)
                                        ->get();
        $where = [];
        $where[] = ['jenis_asset_id' , $jenis_asset->id];
        $where[] = ['year' , $year];

        $where_schedule_id = [];
        foreach($schedule as $_schedule)
        {
            $where_schedule_id[] = $_schedule->id;
        }
        $data = [];
        // dd($where_schedule_id);
        if($schedule == null)
        {
            // Belum ada data
            return response()->json(['data' => $data], 200);
        }
        $fly_catcher = TChecklistFlyCatcher::whereIn('checklist_schedule_id',$where_schedule_id)
        ->whereIn('asset_id', $asset_id)
        ->get();
        // dd($fly_catcher);
        foreach($fly_catcher as $_fly_catcher)
        {
            $data[] = [
                $_fly_catcher->asset->department->name,
                $_fly_catcher->asset_id,
                $_fly_catcher->schedule->week,
                $_fly_catcher->asset->name,
                $_fly_catcher->kondisi,
                $_fly_catcher->pest_count,
                $_fly_catcher->keterangan,
                $_fly_catcher->check_by,
                $_fly_catcher->check_time
            ];
        }
        return response()->json(['data' => $data], 200);
    }

    public function chartData($year, $month, $dept = '')
    {
        $data = [];
        $jenis_asset = JenisAsset::where('slug', 'fly-catcher')->first();
        $where = [];

        if($dept != '') {
            $assets = Asset::where('dept_id', $dept)->where('jenis_asset_id', $jenis_asset->id)->get();
            $where[] = ['dept_id', $dept];
        }else{
            $assets = Asset::where('jenis_asset_id', $jenis_asset->id)->get();
        }
        $asset_id = [];

        foreach ($assets as $asset) {
            $asset_id[] = $asset->id;
        }

        $schedule = ChecklistSchedule::where('month', $month)
                                        ->where('jenis_asset_id', $jenis_asset->id)
                                        ->where('year', $year)
                                        ->where($where)
                                        ->orderBy('week', 'asc')
                                        ->get();
        foreach ($schedule as $_schedule) {
            $checklist_data = [];
            $fly_catcher_checklists = TChecklistFlyCatcher::where('checklist_schedule_id',$_schedule->id)
            ->whereIn('asset_id', $asset_id)
            ->get();
            foreach ($fly_catcher_checklists as $checklist) {
                $checklist_data[] = [
                    'x' => $checklist->asset_id,
                    'y' => $checklist->pest_count
                ];
            }
            // dd($pest_count);
            $data[] = [
                'name' => 'Week '.$_schedule->week,
                'data' => $checklist_data
            ];
        };
        return response()->json(['data' => $data, 'assets' => $assets], 200);
    }
}
