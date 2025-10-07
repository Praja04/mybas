<?php

namespace App\Http\Controllers\PengecekanBoiler;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class SpeedMeterController extends Controller
{

    public function speedMeterLevelFeedWater(){
        $query = "
            SELECT LevelFeedWater, waktu
            FROM readsensor
            WHERE LevelFeedWater IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        // Specify the connection here
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data LevelFeedWater tidak ditemukan'], 404);
        }
    }
    

    public function speedMeterStreamPressure(){
        $query = "
            SELECT PVSteam, waktu
            FROM readsensor
            WHERE PVSteam IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data PVSteam tidak ditemukan'], 404);
        }
    }

    public function speedMeterLHTemp(){
        $query = "
            SELECT LHTemp, waktu
            FROM readsensor
            WHERE LHTemp IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data LHTemp tidak ditemukan'], 404);
        }
    }
    
    public function speedMeterRHTemp(){
        $query = "
            SELECT RHTemp, waktu
            FROM readsensor
            WHERE RHTemp IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data RHTemp tidak ditemukan'], 404);
        }
    }
    

    public function speedMeterLHFDFan(){
        $query = "
            SELECT LHFDFan, waktu
            FROM readsensor
            WHERE LHFDFan IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data LHFDFan tidak ditemukan'], 404);
        }
    }
    

    public function speedMeterRHFDFan(){
        $query = "
            SELECT RHFDFan, waktu
            FROM readsensor
            WHERE RHFDFan IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data RHFDFan tidak ditemukan'], 404);
        }
    }
    

    // proses implementasi ke speedmeter

    public function speedMeterIDFan(){
        $query = "
            SELECT IDFan, waktu
            FROM readsensor
            WHERE IDFan IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data IDFan tidak ditemukan'], 404);
        }
    }

    public function speedMeterLHStocker(){
        $query = "
            SELECT LHStoker, waktu
            FROM readsensor
            WHERE LHStoker IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data LHStoker tidak ditemukan'], 404);
        }
    }
    
    public function speedMeterRHStocker(){
        $query = "
            SELECT RHStoker, waktu
            FROM readsensor
            WHERE RHStoker IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data RHStoker tidak ditemukan'], 404);
        }
    }


    public function speedMeterLHGuiloutine(){
        $query = "
            SELECT LHGuiloutine, waktu
            FROM readsensor
            WHERE LHGuiloutine IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 1
        ";
    
        $latestData = DB::connection('boilerdb')->select($query);
    
        if ($latestData) {
            return response()->json($latestData[0]);
        } else {
            return response()->json(['message' => 'Data LHGuiloutine tidak ditemukan'], 404);
        }
    }
}
