<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DowntimeExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $tgl_mulai;
    protected $tgl_selesai;

    function __construct($tgl_mulai, $tgl_selesai ) {
           $this->tgl_mulai = $tgl_mulai;
           $this->tgl_selesai = $tgl_selesai;
    }

    public function collection()
    {
         $data = DB::table('downtime')
         ->select(
             'logging_machine_no_prod.prod_order',
             'master_mesin_s2.group',
             'logging_machine.shift_group',
             'downtime.numb',
             'downtime.kode_sap',
             'master_reason_s2.reason',
             'downtime.tgl_pengisian',
             'master_mesin_s2.workcenter',
             'downtime.tgl_respon_maintenance',
             'downtime.jam_mulai_maintenance',
             'downtime.tgl_selesai_maintenance',
             'downtime.jam_selesai_maintenance',
             'downtime.waktu_penyelesaian',
             )
        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
        ->join('logging_machine_no_prod', 'logging_machine_no_prod.no_mesin', '=', 'downtime.no_mesin')
        ->join('master_mesin_s2', 'master_mesin_s2.no_mesin', '=', 'downtime.no_mesin')
        ->join('master_reason_s2', 'master_reason_s2.kode_reason', '=', 'downtime.kode_sap')
        ->whereBetween('downtime.tgl_pengisian', [$this->tgl_mulai, $this->tgl_selesai])
        ->get();

        return collect($data);
    }


    public function headings(): array
    {
        return [
            'Prod Ord',
            'Variant',
            'Shift_group',
            'Numb',
            'Reason',
            'Keterangan',
            'PostDate',
            'WorkCenter',
            'Processing Start Date',
            'Processing Start Time',
            'Tear Down Date',
            'Processing End Time',
            'Time',
        ];
     }
}
   