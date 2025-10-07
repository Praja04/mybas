<?php

namespace App\Http\Controllers\PengecekanBoiler;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // $perPage = 10; 
        // $dataperjam = Cache::remember('dashboard_data', 60, function () use ($perPage) {
        //     return ReadSensors::orderBy('waktu', 'desc')
        //                     ->paginate($perPage);
        // });
        return view('pengecekan-boiler.pages.dashboard');
        //     'data' => $dataperjam
        // ]);
    }

    public function manageDataSensorByDate($startDate, $endDate) {
        $start = Carbon::createFromFormat('Y-m-d', $startDate);
        $end = Carbon::createFromFormat('Y-m-d', $endDate);
    
        $data = DB::connection('boilerdb')->table('readsensor')
                    ->whereBetween('waktu', [$start, $end])
                    ->orderBy('waktu', 'desc')
                    ->select('PVSteam', 'FeedPressure', 'LHGuiloutine', 'RHGuiloutine', 'LHTemp', 'RHTemp', 
                                'IDFan', 'LHFDFan', 'RHFDFan', 'LHStoker', 'RHStoker', 'WaterPump1', 
                                'WaterPump2', 'InletWaterFlow', 'OutletSteamFlow')
                    ->get();
    
        return response()->json($data);
    }
}
