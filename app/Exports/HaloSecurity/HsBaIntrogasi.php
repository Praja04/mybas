<?php

namespace App\Exports\HaloSecurity;

use App\HaloSecurity\BaIntrogasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class HsBaIntrogasi implements FromView
{
    use Exportable;

    public function view(): View
    {
        $startDate = request()->input('startDate') ;
        $endDate   = request()->input('endDate') ;
        return view('pages.halo-security.exportbaintrogasi', [
            'baintrogasi' => BaIntrogasi::
                    whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'DESC')
                    ->get()
        ]);
    }
}
