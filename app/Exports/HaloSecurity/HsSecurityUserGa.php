<?php

namespace App\Exports\HaloSecurity;

use App\HaloSecurity\SecurityUserGA;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class HsSecurityUserGa implements FromView
{
    use Exportable;

    public function view(): View
    {
        $startDate = request()->input('startDate') ;
        $endDate   = request()->input('endDate') ;
        return view('pages.halo-security.exportsecuritys', [
            'securitys' => SecurityUserGA::
                    whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->get()
        ]);
    }
}
