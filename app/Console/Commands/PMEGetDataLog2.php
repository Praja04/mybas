<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PMEGetDataLog2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pme:getdatalog2 {shift?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get datalog2 pme';

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

    private function dataShift($power_meter)
    {
        $shift = $this->argument('shift');
        $this->info('Getting data for '. $shift);
        
        if(!$this->argument('date'))
        {
            if($shift == 'ns1') {
                $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '00:00')");
                $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '08:00')");
            }elseif($shift == 'ns2') {
                $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '08:00')");
                $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '16:00')");
            }elseif($shift == 'ns3') {
                $start_date = DB::raw("DATEADD(DAY, -1, DATEADD(MINUTE, (16*60) , DATEDIFF(DAY, 0, CURRENT_TIMESTAMP)))");
                $end_date = DB::raw("DATEADD(DAY, 0, DATEADD(MINUTE, 0 , DATEDIFF(DAY, 0, CURRENT_TIMESTAMP)))");
            }elseif($shift == 'ss1') {
                $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '00:00')");
                $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '05:00')");
            }elseif($shift == 'ss2') {
                $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '05:00')");
                $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '10:00')");
            }elseif($shift == 'ss3') {
                $start_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '10:00')");
                $end_date = DB::raw("CONVERT(DATETIME, CONVERT(CHAR(9), CURRENT_TIMESTAMP, 112) + '15:00')");
            }
        }else{
            $date = $this->argument('date');
            if($shift == 'ns1') {
                $start_date = $date . ' 00:00';
                $end_date = $date . ' 08:00';
            }elseif($shift == 'ns2') {
                $start_date = $date . ' 08:00';
                $end_date = $date . ' 16:00';
            }elseif($shift == 'ns3') {
                $end_date = $date . ' 00:00';
                $date = date_create($date);
                date_sub($date, date_interval_create_from_date_string('1 days'));
                $start_date = date_format($date, 'Y-m-d').' 16:00';
            }elseif($shift == 'ss1') {
                $start_date = $date . ' 00:00';
                $end_date = $date . ' 05:00';
            }elseif($shift == 'ss2') {
                $start_date = $date . ' 05:00';
                $end_date = $date . ' 10:00';
            }elseif($shift == 'ss3') {
                $start_date = $date . ' 10:00';
                $end_date = $date . ' 15:00';
            }
        }


        return DB::connection('pme2')
        ->table('DataLog2')
        ->join('Source', 'DataLog2.SourceID', '=', 'Source.id')
        ->select(DB::raw('max(Value)-min(Value) as all_counts'), DB::raw('Source.id as source_id'))
        ->whereNotNull('DataLog2.Value')
        ->where('Source.Name', $power_meter->name)
        ->where('DataLog2.quantityid', '=', $power_meter->quantity_id)
        ->where('DataLog2.TimestampUTC', '>=', $start_date)
        ->where('DataLog2.TimestampUTC', '<=', $end_date)
        ->groupBy('Source.ID')
        ->first();
        
    }

    private function storeData($data)
    {
        $shift = $this->argument('shift');

        if(!$this->argument('date'))
        {
            $date = date('Y-m-d');
            $datetime = date('Y-m-d H:i:s');
        }else{
            $date = $this->argument('date');
            if($shift == 'ns1') {
                $datetime = $date.' 15:01:00';
            }elseif($shift == 'ns2') {
                $datetime = $date.' 23:01:00';
            }elseif($shift == 'ns3') {
                $datetime = $date.' 07:01:00';
            }elseif($shift == 'ss1') {
                $datetime = $date.' 12:01:00';
            }elseif($shift == 'ss2') {
                $datetime = $date.' 17:01:00';
            }elseif($shift == 'ss3') {
                $datetime = $date.' 22:01:00';
            }
        }

        

        if($shift == 'ns3') {
            $date = date_create($date);
            date_sub($date, date_interval_create_from_date_string('1 days'));
            $date = date_format($date, 'Y-m-d');
        }

        // Cek apakah data sudah ada ( untuk menghindari duplikasi data )

        $cek = DB::connection('pme')
        ->table('pme_datalog2')
        ->where('date', $date)
        ->where('shift', $shift)
        ->where('dept', $data['dept'])
        ->first();

        $identity = $this->getIdentity($data['dept'], $shift);

        if($cek == null) {
            // kalo belum ada insert aja
            $this->info('Insert new record..');
            date_sub(new \DateTime($datetime), date_interval_create_from_date_string('8 hours'));
            DB::connection('pme')->table('pme_datalog2')->insert([
                'Value' => $data['value'],
                'SourceID' => $identity['source_id'],
                'QuantityID' => $identity['quantity_id'],
                'dept' => $data['dept'],
                'date' => $date,
                'shift' => $shift,
                'TimestampUTC' => $datetime
            ]);
        }else{
            date_sub(new \DateTime($datetime), date_interval_create_from_date_string('8 hours'));
            $this->info('Update existing record..');
            DB::connection('pme')
            ->table('pme_datalog2')
            ->where('date', $date)
            ->where('shift', $shift)
            ->where('dept', $data['dept'])
            ->update([
                'Value' => $data['value'],
                'TimestampUTC' => $datetime
            ]);
        }

    }

    public function handle()
    {
        $this->executeCommand();
        $this->executeCommand();
    }

    private function getIdentity($dept, $shift)
    {
        $data = collect([
            ['shift' => 'ns1', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10161'],
            ['shift' => 'ns1', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10216'],
            ['shift' => 'ns1', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10176'],
            ['shift' => 'ns1', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10198'],
            ['shift' => 'ns1', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10134'],
            ['shift' => 'ns1', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10182'],
            ['shift' => 'ns1', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10160'],
            ['shift' => 'ns1', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10121'],
            ['shift' => 'ns1', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10209'],
            ['shift' => 'ns1', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10168'],
            ['shift' => 'ns1', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10143'],
            ['shift' => 'ns1', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10129'],
            ['shift' => 'ns1', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10193'],
            ['shift' => 'ns1', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10206'],
            ['shift' => 'ns2', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10247'],
            ['shift' => 'ns2', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10241'],
            ['shift' => 'ns2', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10203'],
            ['shift' => 'ns2', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10225'],
            ['shift' => 'ns2', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10248'],
            ['shift' => 'ns2', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10224'],
            ['shift' => 'ns2', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10122'],
            ['shift' => 'ns2', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10147'],
            ['shift' => 'ns2', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10157'],
            ['shift' => 'ns2', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10195'],
            ['shift' => 'ns2', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10231'],
            ['shift' => 'ns2', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10218'],
            ['shift' => 'ns2', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10217'],
            ['shift' => 'ns2', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10154'],
            ['shift' => 'ns3', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10212'],
            ['shift' => 'ns3', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10141'],
            ['shift' => 'ns3', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10240'],
            ['shift' => 'ns3', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10249'],
            ['shift' => 'ns3', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10197'],
            ['shift' => 'ns3', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10246'],
            ['shift' => 'ns3', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10211'],
            ['shift' => 'ns3', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10159'],
            ['shift' => 'ns3', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10156'],
            ['shift' => 'ns3', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10208'],
            ['shift' => 'ns3', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10180'],
            ['shift' => 'ns3', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10167'],
            ['shift' => 'ns3', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10243'],
            ['shift' => 'ns3', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10115'],
            ['shift' => 'ss1', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10222'],
            ['shift' => 'ss1', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10219'],
            ['shift' => 'ss1', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10120'],
            ['shift' => 'ss1', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10223'],
            ['shift' => 'ss1', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10235'],
            ['shift' => 'ss1', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10220'],
            ['shift' => 'ss1', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10118'],
            ['shift' => 'ss1', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10170'],
            ['shift' => 'ss1', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10117'],
            ['shift' => 'ss1', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10232'],
            ['shift' => 'ss1', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10179'],
            ['shift' => 'ss1', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10116'],
            ['shift' => 'ss1', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10192'],
            ['shift' => 'ss1', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10205'],
            ['shift' => 'ss2', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10210'],
            ['shift' => 'ss2', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10158'],
            ['shift' => 'ss2', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10146'],
            ['shift' => 'ss2', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10171'],
            ['shift' => 'ss2', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10181'],
            ['shift' => 'ss2', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10169'],
            ['shift' => 'ss2', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10145'],
            ['shift' => 'ss2', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10196'],
            ['shift' => 'ss2', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10130'],
            ['shift' => 'ss2', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10244'],
            ['shift' => 'ss2', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10207'],
            ['shift' => 'ss2', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10142'],
            ['shift' => 'ss2', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10155'],
            ['shift' => 'ss2', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10165'],
            ['shift' => 'ss3', 'dept' => 'ENG', 'source_id' => '160', 'quantity_id' => '10234'],
            ['shift' => 'ss3', 'dept' => 'HRDGA', 'source_id' => '160', 'quantity_id' => '10245'],
            ['shift' => 'ss3', 'dept' => 'PRN1', 'source_id' => '160', 'quantity_id' => '10133'],
            ['shift' => 'ss3', 'dept' => 'PRS1', 'source_id' => '160', 'quantity_id' => '10119'],
            ['shift' => 'ss3', 'dept' => 'PRS3', 'source_id' => '160', 'quantity_id' => '10132'],
            ['shift' => 'ss3', 'dept' => 'QA', 'source_id' => '160', 'quantity_id' => '10131'],
            ['shift' => 'ss3', 'dept' => 'WRH1', 'source_id' => '160', 'quantity_id' => '10144'],
            ['shift' => 'ss3', 'dept' => 'WRH2', 'source_id' => '160', 'quantity_id' => '10221'],
            ['shift' => 'ss3', 'dept' => 'PRN2A', 'source_id' => '160', 'quantity_id' => '10370'],
            ['shift' => 'ss3', 'dept' => 'PRN2B', 'source_id' => '160', 'quantity_id' => '10194'],
            ['shift' => 'ss3', 'dept' => 'PRS2A', 'source_id' => '160', 'quantity_id' => '10230'],
            ['shift' => 'ss3', 'dept' => 'PRS2B', 'source_id' => '160', 'quantity_id' => '10166'],
            ['shift' => 'ss3', 'dept' => 'SAOS', 'source_id' => '160', 'quantity_id' => '10242'],
            ['shift' => 'ss3', 'dept' => 'ALL', 'source_id' => '160', 'quantity_id' => '10191'],
            ['shift' => 'ns1', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10189'],
            ['shift' => 'ns2', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10140'],
            ['shift' => 'ns3', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10229'],
            ['shift' => 'ss1', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10173'],
            ['shift' => 'ss2', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10185'],
            ['shift' => 'ss3', 'dept' => 'SPRN1', 'source_id' => '160', 'quantity_id' => '10214'],
            ['shift' => 'ns1', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10202'],
            ['shift' => 'ns2', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10153'],
            ['shift' => 'ns3', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10239'],
            ['shift' => 'ss1', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10163'],
            ['shift' => 'ss2', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10227'],
            ['shift' => 'ss3', 'dept' => 'SPRN2', 'source_id' => '160', 'quantity_id' => '10251'],
            ['shift' => 'ns1', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10188'],
            ['shift' => 'ns2', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10127'],
            ['shift' => 'ns3', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10152'],
            ['shift' => 'ss1', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10124'],
            ['shift' => 'ss2', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10150'],
            ['shift' => 'ss3', 'dept' => 'SPRS2', 'source_id' => '160', 'quantity_id' => '10213'],
            ['shift' => 'ns1', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10175'],
            ['shift' => 'ns2', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10201'],
            ['shift' => 'ns3', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10200'],
            ['shift' => 'ss1', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10184'],
            ['shift' => 'ss2', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10137'],
            ['shift' => 'ss3', 'dept' => 'BB1SALL', 'source_id' => '160', 'quantity_id' => '10226'],
            ['shift' => 'ns1', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10228'],
            ['shift' => 'ns2', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10252'],
            ['shift' => 'ns3', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10139'],
            ['shift' => 'ss1', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10162'],
            ['shift' => 'ss2', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10250'],
            ['shift' => 'ss3', 'dept' => 'BB2SALL', 'source_id' => '160', 'quantity_id' => '10199'],
            ['shift' => 'ns1', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10187'],
            ['shift' => 'ns2', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10138'],
            ['shift' => 'ns3', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10238'],
            ['shift' => 'ss1', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10136'],
            ['shift' => 'ss2', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10149'],
            ['shift' => 'ss3', 'dept' => 'BB3SALL', 'source_id' => '160', 'quantity_id' => '10237'],
            ['shift' => 'ns1', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10186'],
            ['shift' => 'ns2', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10126'],
            ['shift' => 'ns3', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10215'],
            ['shift' => 'ss1', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10183'],
            ['shift' => 'ss2', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10135'],
            ['shift' => 'ss3', 'dept' => 'BB4SALL', 'source_id' => '160', 'quantity_id' => '10236'],
            ['shift' => 'ns1', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10174'],
            ['shift' => 'ns2', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10125'],
            ['shift' => 'ns3', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10151'],
            ['shift' => 'ss1', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10123'],
            ['shift' => 'ss2', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10148'],
            ['shift' => 'ss3', 'dept' => 'BGASALL', 'source_id' => '160', 'quantity_id' => '10172'],

        ]);
        
        return $data->where('shift', $shift)->where('dept', $dept)->first();
    }

    private function executeCommand()
    {
        if(!$this->argument('shift'))
        {
            $this->info('Silahkan sertakan shift..');
            return;
        }

        $shift = $this->argument('shift');

        $dept = '';
        $data_counts = [];
        $count = [];

        try {
            $power_meters = DB::connection('pme')->table('pme_master_power_meter')->where('active', 'Y')->orderBy('dept', 'asc')->get();
            foreach($power_meters as $key => $power_meter) {
                $this->info($power_meter->name.'--------------------------------');

                // Cek apakah sudah pindah department
                if($dept != $power_meter->dept) {
                    if($key != 0) {
                        $data_counts[] = [
                            'dept' => $dept,
                            'source_id' => $source_id,
                            'quantity_id' => $quantity_id,
                            'value' => array_sum($count)
                        ];
                        $count = [];
                    }
                }

                // $depts[] = $power_meter->dept.'-'.$power_meter->name.'-'.$this->dataShift($power_meter)->all_counts;
                $count[] = $this->dataShift($power_meter) != null ? $this->dataShift($power_meter)->all_counts : 0;

                $dept = $power_meter->dept;
                $source_id = $power_meter->source_id;
                $quantity_id = $power_meter->quantity_id;
                if(count($power_meters) == $key+1) {
                    $data_counts[] = [
                        'dept' => $dept,
                        'source_id' => $source_id,
                        'quantity_id' => $quantity_id,
                        'value' => array_sum($count)
                    ];

                    foreach($data_counts as $data)
                    {
                        $this->storeData($data);
                    }
                }
            }
        }catch(Exception $e) {
            $this->info('Getting data error..');
        }
    }
}
