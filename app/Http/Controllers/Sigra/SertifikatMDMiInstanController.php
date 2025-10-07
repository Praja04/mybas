<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\SertifikatMDMiInstan;

class SertifikatMDMiInstanController extends Controller
{
    public function get($id)
    {
        $sertifikat = SertifikatMDMiInstan::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikat,
            'message' => 'Get sertifikat succeed'
        ]);
    }

    public function delete($id)
    {
        $data = SertifikatMDMiInstan::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }
}
