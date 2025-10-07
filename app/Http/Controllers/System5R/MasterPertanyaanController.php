<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\Jawaban;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterPertanyaan;
use Illuminate\Http\Request;
use App\Imports\System5r\ImportMasterPertanyaan5r;
use Maatwebsite\Excel\Facades\Excel;

class MasterPertanyaanController extends Controller
{
    public function index()
    {
        $department = MasterDepartment::where('is_active', 'Y')->get();
        $group = MasterGroup::orderBy('id_department')->get();
        return view('system5r.master-pertanyaan.index', compact('department', 'group'));
    }

    public function data()
    {
        $id_group = $_GET['group'];
        $data = MasterPertanyaan::where('id_group', $id_group)
        ->where('archive_status', 'N')
        ->get();

        return response()
        ->json([
            'data' => $data
        ]);
    }

    protected function createIdPertanyaan()
    {
        // Get last record
        $lastRecord = MasterPertanyaan::
        // Order by substring id_pertanyaan
        orderByRaw('CAST(SUBSTRING(id_pertanyaan, 2) AS SIGNED) DESC')
        ->first();

        if($lastRecord == null) {
            $id_pertanyaan = 0;
        } else {
            // Substing Q001 -> 001
            $id_pertanyaan = substr($lastRecord->id_pertanyaan, 1);
        }

        // Make new id_pertanyaan
        $id_pertanyaan = 'Q' . (intval($id_pertanyaan) + 1);

        return $id_pertanyaan;
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'id_department' => 'required',
            'id_group' => 'required',
            'jenis' => 'required',
            'item_periksa' => 'required',
            'keterangan' => 'required',
        ]);

        if(!$validation) {
            return response()
            ->json([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        $id_pertanyaan = $this->createIdPertanyaan();
        $pertanyaan = new MasterPertanyaan;
        $pertanyaan->id_pertanyaan = $id_pertanyaan;
        $pertanyaan->id_group = $request->id_group;
        $pertanyaan->jenis = $request->jenis;
        $pertanyaan->item_periksa = $request->item_periksa;
        $pertanyaan->keterangan = $request->keterangan;
        $pertanyaan->save();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function delete(Request $request)
    {
        // Cek apakah pertanyaan sudah digunakan di jawaban
        $cekDiJawaban = Jawaban::where('id_pertanyaan', $request->id_pertanyaan)->first();

        if($cekDiJawaban != null) {
            return response()
            ->json([
                'status' => 'error',
                'message' => 'Data tidak dapat dihapus karena sudah digunakan di jawaban'
            ]);
        }

        $id_pertanyaan = $request->id_pertanyaan;
        $pertanyaan = MasterPertanyaan::where('id_pertanyaan', $id_pertanyaan)->first();
        $pertanyaan->delete();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function get($idPertanyaan)
    {
        $pertanyaan = MasterPertanyaan::where('id_pertanyaan', $idPertanyaan)->first();

        return response()
        ->json([
            'status' => 'success',
            'data' => $pertanyaan
        ]);
    }

    public function update(Request $request)
    {
        $validation = $request->validate([
            'id_pertanyaan' => 'required',
            'jenis' => 'required',
            'item_periksa' => 'required',
            'keterangan' => 'required',
        ]);

        if(!$validation) {
            return response()
            ->json([
                'status' => 'error',
                'message' => 'Data tidak valid'
            ]);
        }

        // Cek apakah pertanyaan sudah digunakan di jawaban
        $cekDiJawaban = Jawaban::where('id_pertanyaan', $request->id_pertanyaan)->first();

        // if($cekDiJawaban != null) {
        //     return response()
        //     ->json([
        //         'status' => 'error',
        //         'message' => 'Data tidak dapat diubah karena sudah digunakan di jawaban'
        //     ]);
        // }

        $pertanyaan = MasterPertanyaan::where('id_pertanyaan', $request->id_pertanyaan)->first();
        $pertanyaan->jenis = $request->jenis;
        $pertanyaan->item_periksa = $request->item_periksa;
        $pertanyaan->keterangan = $request->keterangan;
        $pertanyaan->save();

        return response()
        ->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate'
        ]);
    }

    public function clone(Request $request)
    {
        $id_group_target = $request->id_group_target;
        $id_department = $request->id_department;
        $id_group = $request->id_group;

        // Get pertanyaan by id_group
        $pertanyaanTarget = MasterPertanyaan::where('id_group', $id_group_target)->get();

        // Get pertanyaan by id_group
        $pertanyaan = MasterPertanyaan::where('id_group', $id_group)
        ->where('archive_status', 'N')
        ->count();

        if($pertanyaan > 0) {
            return response()
            ->json([
                'status' => 'error',
                'message' => 'Data tidak dapat di clone karena sudah ada pertanyaan di group ini'
            ]);
        }

        foreach($pertanyaanTarget as $p) {
            $pertanyaan = new MasterPertanyaan;
            $pertanyaan->id_pertanyaan = $this->createIdPertanyaan();
            $pertanyaan->id_group = $id_group;
            $pertanyaan->jenis = $p->jenis;
            $pertanyaan->item_periksa = $p->item_periksa;
            $pertanyaan->keterangan = $p->keterangan;
            $pertanyaan->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil di clone'
        ]);
    }

     // archive data pertanyaan
    public function archiveDataPertanyaan(Request $request){
        $pertanyaan = MasterPertanyaan::where('id_pertanyaan', $request->id_pertanyaan)->first();
    
        if ($pertanyaan) {
            $pertanyaan->archive_status = 'Y';
            $pertanyaan->save();
    
            return response()->json([
                'status' => 1, 
                'message' => 'Data berhasil di archive'
            ]);
        } else {
            return response()->json([
                'status' => 0, 
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function archiveAllDataPertanyaan(Request $request)
    {
        try {
            $id_group = $request->id_group;
    
            $pertanyaanUpdate = MasterPertanyaan::where('id_group', $id_group)
                ->update(['archive_status' => 'Y']);
    
            if ($pertanyaanUpdate) {
                return response()->json(['status' => 1, 'message' => 'Mantap semua data pertanyaan berhasil di archive']);
            } else {
                return response()->json(['status' => 2, 'message' => 'Gagal melakukan archive data pertanyaan']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 2, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
    

    public function importMasterPertanyaan(Request $request)
    {
            try {
                $id_group = $request->input('id_group_import'); 
                $id_pertanyaan = $this->createIdPertanyaan();

                Excel::import(new ImportMasterPertanyaan5r($id_group, $id_pertanyaan), $request->file('excel'), null, \Maatwebsite\Excel\Excel::XLSX);

                return response()->json(['success' => true, 'message' => 'Import berhasil dilakukan']);
            } catch (\Exception $e) {
                return response()->json(['error' => false, 'message' => 'Import gagal: ' . $e->getMessage()]);
            }
    }
}
