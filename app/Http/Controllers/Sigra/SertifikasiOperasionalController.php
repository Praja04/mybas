<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\SertifikasiOperasional;

class SertifikasiOperasionalController extends Controller
{
    public function get($id)
    {
        $sertifikasi = SertifikasiOperasional::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikasi,
            'message' => 'Get sertifikasi succeed'
        ]);
    }

    public function delete($id)
    {
        $data = SertifikasiOperasional::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }
}
