<?php

namespace App\Http\Controllers\MEN;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterNotifController extends Controller
{
    public function index()
    {
        return view('men.master-notif');
    }
}
