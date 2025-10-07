<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\SertifikatSNIMiInstan;

class SertifikatSNIMiInstanController extends Controller
{
    public function get($id)
    {
        $sertifikat = SertifikatSNIMiInstan::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikat,
            'message' => 'Get sertifikat succeed'
        ]);
    }

    public function delete($id)
    {
        $data = SertifikatSNIMiInstan::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }
}
