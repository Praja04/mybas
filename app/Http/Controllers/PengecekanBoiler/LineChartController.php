<?php

namespace App\Http\Controllers\PengecekanBoiler;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LineChartController extends Controller
{
    public function linechartPvSteam(){
        $query = "
            SELECT PVSteam, waktu
            FROM readsensor
            WHERE PVSteam IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 8
        ";
    
        $datas = DB::connection('boilerdb')->select(DB::raw($query));
    
        return response()->json(array_reverse($datas));
    }
    
    public function linechartLevelFeedWater(){
        $query = "
            SELECT LevelFeedWater, waktu
            FROM readsensor
            WHERE LevelFeedWater IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 8
        ";
    
        $datas = DB::connection('boilerdb')->select(DB::raw($query));
    
        return response()->json(array_reverse($datas));
    }

    public function linechartLHTemp(){
        $query = "
            SELECT LHTemp, waktu
            FROM readsensor
            WHERE LHTemp IS NOT NULL
            ORDER BY waktu DESC
            LIMIT 8
        ";
    
        $datas = DB::connection('boilerdb')->select(DB::raw($query));
    
        return response()->json(array_reverse($datas));
    }

    public function linechartRHTemp(){
        $query = "
        SELECT RHTemp, waktu
        FROM readsensor
        WHERE RHTemp IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }

    public function linechartLHFDFan(){
        $query = "
        SELECT LHFDFan, waktu
        FROM readsensor
        WHERE LHFDFan IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }

    public function linechartRHFDFan(){
        $query = "
        SELECT RHFDFan, waktu
        FROM readsensor
        WHERE RHFDFan IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }

    public function linechartIDFan(){
        $query = "
        SELECT IDFan, waktu
        FROM readsensor
        WHERE IDFan IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }

    public function linechartLHStoker(){
        $query = "
        SELECT LHStoker, waktu
        FROM readsensor
        WHERE LHStoker IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }

    public function linechartRHStoker(){
        $query = "
        SELECT RHStoker, waktu
        FROM readsensor
        WHERE RHStoker IS NOT NULL
        ORDER BY waktu DESC
        LIMIT 8
    ";

    $datas = DB::connection('boilerdb')->select(DB::raw($query));

    return response()->json(array_reverse($datas));
    }    
}
