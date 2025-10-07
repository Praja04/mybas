<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\GroupJuri;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\Periode;
use Illuminate\Support\Facades\DB;

class MasterJuriController extends Controller
{

    public function index()
    {
        $data = GroupJuriAnggota::all();
        $departments = MasterDepartment::all();
        $periodes = Periode::orderBy('nama_periode')->get();

        return view('system5r.master-juri.index', compact('data', 'departments', 'periodes'));
    }

    protected function createGroupJuri()
    {
        $lastRecord = GroupJuri::latest('id_group_juri')->first();

        if ($lastRecord == null) {
            $id_group_juri = 0;
        } else {
            $id_group_juri = intval(substr($lastRecord->id_group_juri, 1));
        }

        $id_group_juri++;
        $id_group_juri = 'G' . sprintf("%03d", $id_group_juri);

        return $id_group_juri;
    }


    public function storeGroupJuri(Request $request)
    {
        $id_group_juri = $this->createGroupJuri();
        $GroupJuri = new GroupJuri;
        $GroupJuri->id_group_juri = $id_group_juri;
        $GroupJuri->nama_group = $request->nama_group;
        $GroupJuri->keterangan = $request->keterangan;
        $GroupJuri->is_active = $request->is_active;
        // dd($GroupJuri);
        $GroupJuri->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat group Juri']);
    }

    public function storeGroupAnggota(Request $request)
    {
        $anggotaJuri = new GroupJuriAnggota;
        $anggotaJuri->id_group_juri  = $request->id_group_juri;
        $anggotaJuri->nik_juri = $request->nik_juri;
        $anggotaJuri->nama_juri = $request->nama_juri;
        $anggotaJuri->is_active = $request->is_active;
        $anggotaJuri->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat anggota group juri']);
    }

    public function storeGroupJuriDepartment(Request $request)
    {
        $deptJuri = new MasterGroupJuriDepartment;
        $deptJuri->id_group_juri  = $request->id_group_juri;
        $deptJuri->id_department = $request->id_department;
        $deptJuri->id_periode = $request->id_periode;
        $deptJuri->save();
        return response()->json(['success' => 1, 'message' => 'Berhasil membuat group department juri']);
    }

    public function dataJuri()
    {
        $juriData = GroupJuri::all();

        $combinedData = [];

        foreach ($juriData as $juri) {
            $departments = MasterGroupJuriDepartment::where('id_group_juri', $juri->id_group_juri)->pluck('id_department');

            $namaJuri = GroupJuriAnggota::where('id_group_juri', $juri->id_group_juri)->pluck('nama_juri')->implode(', ');

            $combinedData[] = [
                'id_group_juri' => $juri->id_group_juri,
                'id_department' => $departments,
                'nama_group' => $juri->nama_group,
                'keterangan' => $juri->keterangan,
                'is_active' => $juri->is_active,
                'nama_juri' => $namaJuri,
            ];
        }

        $response = [
            'data' => $combinedData,
            'status' => 'success',
            'code' => 200,
        ];

        return response()->json($response);
    }


    //kode data juri sebelum nya

    // public function dataJuri()
    // {
    //     $juriData = GroupJuri::all();

    //     $combinedData = [];

    //     foreach ($juriData as $juri) {
    //         $departments = GroupJuriAnggota::where('id_group_juri', $juri->id_group_juri)->get();

    //         $namaJuri = $departments->implode('nama_juri', ', ');

    //         $combinedData[] = [
    //             'id_group_juri' => $juri->id_group_juri,
    //             'nama_group' => $juri->nama_group,
    //             'keterangan' => $juri->keterangan,
    //             'is_active' => $juri->is_active,
    //             'nama_juri' => $namaJuri,
    //         ];
    //     }

    //     $response = [
    //         'data' => $combinedData,
    //         'status' => 'success',
    //         'code' => 200,
    //     ];

    //     return response()->json($response);
    // }

    //kode data juri sebelum nya end


    public function setStatus(Request $request)
    {
        $id_group_juri = $request->id;
        $newStatus = $request->is_active;

        DB::table('5r_master_group_juri')
            ->where('id_group_juri', $id_group_juri)
            ->update(['is_active' => $newStatus]);

        return response()->json(['success' => 1, 'message' => 'Status Berhasil Dirubah']);
    }


    public function getJuri($id_group_juri)
    {

        // $juriData = GroupJuri::all();

        $combinedData = [];
        $juriAnggotaData = GroupJuriAnggota::where('id_group_juri', $id_group_juri)->get();
        foreach ($juriAnggotaData as $anggota) {
            $combinedData[] = [
                'id_group_juri' => $id_group_juri,
                'nik_juri' => $anggota->nik_juri,
                'nama_juri' => $anggota->nama_juri,
                'is_active' => $anggota->is_active,
            ];
        }
        return response()->json($combinedData);
    }


    public function updateJuri(Request $request)
    {
        $data = GroupJuriAnggota::find($request->id);

        if (!$data) {
            return response()->json(['error' => 'data gak ketemu'], 404);
        }

        // $data->id_perusahaan = $request->perusahaan;
        // $data->nama_perizinan = $request->nama_perizinan;
        // $data->nama_karyawan = $request->nama_karyawan;
        // $data->nik_karyawan = $request->nik_karyawan;
        $data->save();

        return response()->json(['success' => 1, 'message' => 'Data Berhasil di update']);
    }

    public function deleteJuri(Request $request)
    {

        // dd($request->all());
        // Temukan data berdasarkan id_group_juri
        GroupJuriAnggota::where('id_group_juri', $request->id_group_juri)
            ->where('nik_juri', $request->nik)
            ->delete();

        return response()->json(['success' => 1,  'message' => 'Data berhasil dihapus']);
    }
}
