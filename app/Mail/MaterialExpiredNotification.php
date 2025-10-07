<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialExpiredNotificationExport;

class MaterialExpiredNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $grouped_materials;
    protected $status;
    protected $materials;

    public function __construct($grouped_materials, $status, $materials)
    {
        $this->grouped_materials    = $grouped_materials;
        $this->status       = $status;        
        $this->materials    = $materials;
    }

    protected function formatTanggal($date) {
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];
        $date = explode('-', $date)[2];
        return $date . '.' . $month . '.' . $year;
    }

    public function build()
    {
        $materials = $this->materials->map(function ($value, $key) {
            return [
                'plant'                 => $value->plant,
                'sloc'                  => $value->sloc,
                'material'              => $value->material,
                'material_description'  => $value->material_description,
                'batch'                 => $value->batch,
                'BUn'                   => $value->uom,
                'qty'                   => $value->qty,
                'production_date'       => $this->formatTanggal($value->production_date),
                'expired_date'          => $this->formatTanggal($value->expired_date),
                'shelf_life'            => $value->shelf_life
            ];
        });

        $materials->prepend([
            'Plant',
            'Sloc',
            'Material',
            'Material Description',
            'Batch',
            'BUn',
            'QTY',
            'Production Date',
            'Expired Date',
            'Shelf Life'
        ]);

        // dd($materials);

        // dd($grouped_materials);

        $fileName = 'MATERIAL-EXPIRED-NOTIF-'.date('d-m-yyyy').'.xlsx';

        return $this->view('mail.material-expired-monitoring', [
            'materials'     => $this->grouped_materials,
            'status'        => $this->status,
            'grand_total'    => collect($this->grouped_materials)->sum('sum')
        ])
        ->attach(Excel::download(
            new MaterialExpiredNotificationExport($materials),
            $fileName
            )->getFile(), ['as' => $fileName]
        );
    }
}
