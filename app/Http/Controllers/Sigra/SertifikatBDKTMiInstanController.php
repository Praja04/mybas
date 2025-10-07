<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\SertifikatBDKTMiInstan;

class SertifikatBDKTMiInstanController extends Controller
{
    public function get($id)
    {
        $sertifikat = SertifikatBDKTMiInstan::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikat,
            'message' => 'Get sertifikat succeed'
        ]);
    }

    public function delete($id)
    {
        $data = SertifikatBDKTMiInstan::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }
}
