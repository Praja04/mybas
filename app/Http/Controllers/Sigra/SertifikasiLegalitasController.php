<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sigra\SertifikasiLegalitas;

class SertifikasiLegalitasController extends Controller
{
    public function get($id)
    {
        $sertifikasi = SertifikasiLegalitas::find($id);
        return response()->json([
            'success' => 1,
            'data' => $sertifikasi,
            'message' => 'Get sertifikasi succeed'
        ]);
    }

    public function delete($id)
    {
        $data = SertifikasiLegalitas::find($id);
        $data->status = 'deleted';
        $data->save();
        return response()->json(['success' => 1, 'message' => 'Delete data succeed']);
    }
}
