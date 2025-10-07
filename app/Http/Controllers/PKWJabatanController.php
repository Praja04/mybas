<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PKWJabatan;

class PKWJabatanController extends Controller
{
    public function getByBagian($bagianId)
    {
    	$jabatans = PKWJabatan::where('id_bagian', $bagianId)->get();
    	return response()->json(['jabatans' => $jabatans]);
    }
}
