<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PMEGetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pme:getdata {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data PME';

    protected $select = 'min(Value) as min_value, max(Value) as max_value, max(Value)-min(Value) as all_counts, Source.id as source_id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */

    private function getDataShift1($power_meter)
    {
        if($this->argument('date') == '') {
            $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '00:00')");
            $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '08:00')");
        }else{
            $start_date = $this->argument('date').' 00:00';
            $end_date = $this->argument('date').' 08:00';
        }
        return DB::connection('pme2')
        ->table('DataLog2')
        ->join('Source', 'DataLog2.SourceID', '=', 'Source.id')
        ->select(DB::raw($this->select))
        ->whereNotNull('DataLog2.Value')
        ->where('Source.Name', $power_meter->name)
        ->where('DataLog2.quantityid', '=', $power_meter->quantity_id)
        ->where('DataLog2.TimestampUTC', '>=', $start_date)
        ->where('DataLog2.TimestampUTC', '<=', $end_date)
        ->groupBy('Source.ID')
        ->first();
    }

    private function getDataShift2($power_meter)
    {
        if($this->argument('date') == '') {
            $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '08:00')");
            $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '16:00')");
        }else{
            $start_date = $this->argument('date').' 08:00';
            $end_date = $this->argument('date').' 16:00';
        }
        return DB::connection('pme2')
        ->table('DataLog2')
        ->select(DB::raw($this->select))
        ->join('Source', 'DataLog2.SourceID', '=', 'Source.id')
        ->whereNotNull('Value')
        ->where('Source.Name', $power_meter->name)
        ->where('quantityid', '=', $power_meter->quantity_id)
        ->where('TimestampUTC', '>=', $start_date)
        ->where('TimestampUTC', '<=', $end_date)
        ->groupBy('Source.ID')
        ->first();
    }

    private function getDataShift3($power_meter)
    {
        if($this->argument('date') == '') {
            $start_date = DB::raw("DATEADD(DAY, -1, DATEADD(MINUTE, (16*60) , DATEDIFF(DAY, 0, CURRENT_TIMESTAMP)))");
            $end_date = DB::raw("DATEADD(DAY, 0, DATEADD(MINUTE, 0 , DATEDIFF(DAY, 0, CURRENT_TIMESTAMP)))");
        }else{
            $date = date_create($this->argument('date'));
            date_sub($date, date_interval_create_from_date_string('1 days'));
            $start_date = date_format($date, 'Y-m-d').' 16:00';
            $end_date = $this->argument('date').' 00:00';
        }
        return DB::connection('pme2')
        ->table('DataLog2')
        ->join('Source', 'DataLog2.SourceID', '=', 'Source.id')
        ->select(DB::raw($this->select))
        ->whereNotNull('Value')
        ->where('Source.Name', $power_meter->name)
        ->where('quantityid', '=', $power_meter->quantity_id)
        ->where('TimestampUTC', '>=', $start_date)
        ->where('TimestampUTC', '<=', $end_date)
        ->groupBy('Source.ID')
        ->first();
    }

    private function getDataLog($power_meter, $date, $shift)
    {
        return DB::connection('pme')
        ->table('pme_datalog')
        ->where('name', $power_meter->name)
        ->where('quantity_id', $power_meter->quantity_id)
        ->where('dept', $power_meter->dept)
        ->where('date', $date)
        ->where('shift', $shift)
        ->first();
    }

    private function insertDataLog($power_meter, $date, $shift, $data)
    {
        $this->info('Storing data by inserting it..');
        DB::connection('pme')->table('pme_datalog')->insert([
            'name' => $power_meter->name,
            'source_id' => $data->source_id,
            'quantity_id' => $power_meter->quantity_id,
            'dept' => $power_meter->dept,
            'date' => $date,
            'shift' => $shift,
            'min_value' => $data->min_value,
            'max_value' => $data->max_value,
            'value' => $data->all_counts,
            'created_at' => \Carbon\Carbon::now()
        ]);
        $this->info('Store data succeed..');
    }

    private function updateDataLog($power_meter, $date, $shift, $data)
    {
        $this->info('Storing data by update previosly..');
        DB::connection('pme')
        ->table('pme_datalog')
        ->where('source_id', $data->source_id)
        ->where('quantity_id', $power_meter->quantity_id)
        ->where('dept', $power_meter->dept)
        ->where('date', $date)
        ->where('shift', $shift)
        ->update([
            'min_value' => $data->min_value,
            'max_value' => $data->max_value,
            'value' => $data->all_counts,
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $this->info('Store data succeed..');
    }

    private function processData($power_meter, $data, $shift)
    {
        if($this->argument('date') == '') {
            $date = date('Y-m-d');
            if($shift == '3') {
                $date = date('Y-m-d',strtotime("-1 days"));
            }
        }else{
            $date = $this->argument('date');
            if($shift == '3') {
                $date = date_create($this->argument('date'));
                date_sub($date, date_interval_create_from_date_string('1 days'));
                $date = date_format($date, 'Y-m-d');
            }
        }
        
        if($data != null) {
            // Ini berarti data sudah ada
            $this->info('Shift '.$shift.' data found.. '.json_encode($data->all_counts));
            // Cek data di databse.
            $this->info('Checking data shift '.$shift.' on database');
            $datalog = $this->getDataLog($power_meter, $date, $shift);
            if($datalog != null) {
                // berarti data ada
                $this->info('Data shift '.$shift.' alredy exist on database');
                // Update data yang ada
                $this->updateDataLog($power_meter, $date, $shift, $data);
            }else{
                $this->info('Data shift '.$shift.' not exist on database');
                // Buat data baru
                $this->insertDataLog($power_meter, $date, $shift, $data);
            }
        }else{
            $this->info('Shift '.$shift.' data not found..');
            // Do not do anything
        }
    }

    public function handle()
    {
        $this->info('Start getting data.. '. $this->argument('date'));
        try {
            $power_meters = DB::connection('pme')->table('pme_master_power_meter')->where('active', 'Y')->get();
            foreach($power_meters as $key => $power_meter) {
                $this->info($power_meter->name.' - '.$power_meter->source_id.' - '.$power_meter->quantity_id.'--------------------------------');

                // Shift 1 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $this->info('Shift 1 checking..');
                $data_shift1 = $this->getDataShift1($power_meter);
                $this->processData($power_meter, $data_shift1, '1');

                // Shift 2 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $this->info('Shift 2 checking..');
                $data_shift2 = $this->getDataShift2($power_meter);
                $this->processData($power_meter, $data_shift2, '2');

                // Shift 3 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $this->info('Shift 3 checking..');
                $data_shift3 = $this->getDataShift3($power_meter);
                $this->processData($power_meter, $data_shift3, '3');

            }
        }catch(Exception $e) {
            $this->info('Getting data error..');
        }
    }
        
}
