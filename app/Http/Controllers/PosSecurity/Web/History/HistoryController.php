<?php

namespace App\Http\Controllers\PosSecurity\Web\History;

use App\Http\Controllers\Controller;

class HistoryController extends Controller
{
    // public function supplier_smu()
    // {
    //     return view("ga.module.sistem-tracking.pages.history.supplier-smu");
    // }

    // public function vendor_smu()
    // {
    //     return view("ga.module.sistem-tracking.pages.history.vendor-smu");
    // }

    public function supplier()
    {
        // return view("ga.module.sistem-tracking.pages.history.supplier-pas");
        return view("pos-security.history-supplier.index");
    }

    public function vendor()
    {
        return view("pos-security.history-tamu.index");
    }
}
