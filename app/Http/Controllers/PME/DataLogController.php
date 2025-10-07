<?php

namespace App\Http\Controllers\PME;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class DataLogController extends Controller
{
    public function index()
    {
        $where = [];
        $where2 = [];
        if(isset($_GET['filter_date']) && $_GET['filter_date'] != ''){
            $date = $_GET['filter_date'];
        }else{
            $date = date('Y-m-d');
        }

        if(isset($_GET['filter_shift']) && $_GET['filter_shift'] == 'ns3')
        {
            $date = date_create($date);
            date_sub($date, date_interval_create_from_date_string('1 days'));
            $date = date_format($date, 'Y-m-d');
        }

        $where[] = ['date', $date];
        $where2[] = ['date', $date];
        
        if(isset($_GET['filter_shift']) && $_GET['filter_shift'] != '') {
            $where[] = ['shift', str_replace('ss','', str_replace('ns', '', $_GET['filter_shift']))];
            $where2[] = ['pme_datalog2.shift', $_GET['filter_shift']];
        }else{
            $where[] = ['shift', '1'];
            $where2[] = ['pme_datalog2.shift', 'ns1'];
        }

        $data_logs = DB::connection('pme')->table('pme_datalog')->where($where)->orderBy('dept', 'asc')->get();
        $data_logs2_listrik = DB::connection('pme')
            ->table('pme_datalog2')
            ->join('pme_shift', 'pme_datalog2.dept', 'pme_shift.dept')
            ->where('pme_shift.shift', 'ns1')
            ->where('pme_shift.jenis', 'listrik')
            ->where($where2)
            ->orderBy('pme_datalog2.dept', 'asc')->get();

        $data_logs2_steam_incoming = DB::connection('pme')
            ->table('pme_datalog2')
            ->join('pme_shift', 'pme_datalog2.dept', 'pme_shift.dept')
            ->where('pme_shift.shift', 'ns1')
            ->where('pme_shift.jenis', 'steam')
            ->where('pme_shift.jalur', 'incoming')
            ->where($where2)
            ->orderBy('pme_datalog2.dept', 'asc')->get();
        $data_logs2_steam_outgoing = DB::connection('pme')
            ->table('pme_datalog2')
            ->join('pme_shift', 'pme_datalog2.dept', 'pme_shift.dept')
            ->where('pme_shift.shift', 'ns1')
            ->where('pme_shift.jenis', 'steam')
            ->where('pme_shift.jalur', 'outgoing')
            ->where($where2)
            ->orderBy('pme_datalog2.dept', 'asc')->get();

        $all_count_listrik = 0;
        $all_count_listrik_proporsional = 0;
        foreach($data_logs2_listrik as $data)
        {
            if($data->dept == 'ALL') {
                $all_count_listrik_proporsional = $data->Value;
            }else{
                $all_count_listrik = $all_count_listrik+$data->Value;
            }
        }

        $all_count_steam = 0;
        $all_count_steam_proporsional = 0;
        foreach($data_logs2_steam_incoming as $data)
        {
            $all_count_steam_proporsional = $all_count_steam_proporsional + $data->Value;
        }

        foreach($data_logs2_steam_outgoing as $data)
        {
            
            $all_count_steam = $all_count_steam+$data->Value;
        }


        return view('pme.data-log', [
            'data_logs' => $data_logs,
            'data_logs2_listrik' => $data_logs2_listrik,
            'all_count_listrik' => $all_count_listrik,
            'all_count_listrik_proporsional' => $all_count_listrik_proporsional,
            'data_logs2_steam_incoming' => $data_logs2_steam_incoming,
            'data_logs2_steam_outgoing' => $data_logs2_steam_outgoing,
            'all_count_steam' => $all_count_steam,
            'all_count_steam_proporsional' => $all_count_steam_proporsional
        ]);
    }

    public function generate($tanggal)
    {
        Artisan::call('pme:getdata '.$tanggal);
        return 'Generate data succeed';
    }

    public function generate2($tanggal, $shift)
    {
        Artisan::call('pme:getdatalog2 '.$shift.' '.$tanggal);
        return 'Generate data succeed for '.$tanggal.' '.$shift;
    }

    public function dataLog2()
    {
        if(isset($_GET['filter_date']) && $_GET['filter_date'] != ''){
            $date = $_GET['filter_date'];
        }else{
            $date = date('Y-m-d');
        }

        $where[] = ['date', $date];

        $data_logs = DB::connection('pme')
        ->table('pme_datalog')
        ->where($where)
        // ->orWhere('date', Carbon::parse($date)->subDays(1)->format('Y-m-d'))
        ->orWhere(function ($query) use ($date) {
            // dd($date);
            return $query
            ->where('date', Carbon::parse($date)->subDays(1)->format('Y-m-d'))
            ->where('shift', '3');
        })
        ->orderBy('dept', 'asc')
        ->orderBy('name', 'asc')
        ->orderBy('date', 'asc')
        ->orderBy('shift', 'asc')
        ->get();

        // dd($data_logs);

        return view('pme.data-log-2', [
            'data_logs' => $data_logs,
            'date' => $date
        ]);
    }
}
