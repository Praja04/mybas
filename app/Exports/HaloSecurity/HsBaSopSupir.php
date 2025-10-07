<?php

namespace App\Exports\HaloSecurity;

use App\HaloSecurity\BaSopSupir;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class HsBaSopSupir implements FromView
{
    use Exportable;

    public function view(): View
    {
        $startDate = request()->input('startDate') ;
        $endDate   = request()->input('endDate') ;
        return view('pages.halo-security.exportbasopsupir', [
            'basopsupir' => BaSopSupir::
                    whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'DESC')
                    ->get()
        ]);
    }
}
