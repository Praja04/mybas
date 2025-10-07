<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PKWBagian;

class PKWBagianController extends Controller
{
    public function getByDivisi($divisiId)
    {
    	$bagians = PKWBagian::where('id_divisi', $divisiId)->get();
    	return response()->json(['bagians' => $bagians]);
    }
}
