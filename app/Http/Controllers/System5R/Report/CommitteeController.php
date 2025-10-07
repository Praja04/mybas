<?php

namespace App\Http\Controllers\System5R\Report;

use App\Http\Controllers\Controller;
use App\Models\System5R\Jadwal;
use App\Models\System5R\DepartmentComittee;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterWorkspace;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CommitteeController extends Controller
{
    public function index()
    {
        $myDepartment = DepartmentComittee::where('nik_committee', auth()->user()->username)->get();

        if($myDepartment->count() == 0) {
            $workspace = null;
        }else{
            // $data = MasterDepartment::whereIn('id_department', 
            // $myDepartment->pluck('id_department')->toArray()
            // )->get();


            $workspace = MasterWorkspace::whereHas('departments', function ($query) use ($myDepartment) {
                $query->whereIn('id_department', $myDepartment->pluck('id_department')->toArray());
            })->get();

            // Filter departments
            $workspace = $workspace->map(function ($_item) use ($myDepartment) {
                $_item->departments = $_item->departments->filter(function ($item) use ($myDepartment) {
                    return $myDepartment->where('id_department', $item->id_department)->count() > 0;
                });

                return $_item;
            });

            if(isset($_GET['filter_jadwal'])) {
                $workspace = $workspace->map(function ($_item) {
                    $_data = $_item->departments->map(function ($item) {
                        $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
                        $id_department = $item->id_department;
        
                        $periode = $periode->map(function ($_item, $key) use ($id_department) {
                            $__group = MasterGroup::where('id_department', $id_department)->get();
        
                            $id_periode = $_item->id_periode;
        
                            $_group = $__group->map(function ($__item, $key) use ($id_periode) {
                                $__item->jawabanGroup = JawabanGroup::where('id_group', $__item->id_group)
                                ->where('id_periode', $id_periode)
                                ->first();
        
                                $totalNilai = 0;
                                
                                if($__item->jawabanGroup != null) {
                                    $jawaban = Jawaban::where('id_jawaban_group', $__item->jawabanGroup->id_jawaban_group)->get();
                                    
                                    $totalNilai = $jawaban->sum('nilai')*($__item->persentase/100);
                                }
        
                                $__item->totalNilai = $totalNilai;
        
                                return $__item;
                            });
        
                            $juri_periode = MasterGroupJuriDepartment::where('id_department', $id_department)
                            ->where('id_periode', $id_periode)
                            ->with('group.anggota')
                            ->first();
        
                            // dd($juri_periode->group->anggota);
        
                            if($juri_periode != null) {
                                $juri_array = $juri_periode->group->anggota->map(function ($item, $key) {
                                    return $item->nama_juri;
                                })->toArray();
                                
                                $_item->juri = $juri_array;
                            }else{
                                $_item->juri = [];
                            }
        
        
                            $_group = $_group->filter(function ($__item, $key) {
                                return $__item->jawabanGroup != null && $__item->jawabanGroup->status == 'approved';
                            });
        
                            $_item->group = $_group;
                            $_item->totalNilai = $_group->sum('totalNilai');
        
                            return $_item;
                        });
        
                        $item->periode = $periode;
        
                        if($item->periode->where('totalNilai', '!=', '0')->count() == 0) {
                            $__total = 0;
                        }else{
                            $__total = $item->periode->sum('totalNilai')/$item->periode->where('totalNilai', '!=', '0')->count();
                        }
        
                        $item->__total = $__total;
        
                        return $item;
                    });

                    $_item->departments = $_data;
                    return $_item;
                });
            }

        }

        $allJadwal = Jadwal::all();

        return view('system5r.report.management.index', compact('workspace', 'allJadwal'));
    }
    

    public function masterCommitte(){
        $department = MasterDepartment::where('is_active', 'Y')->get();
        $group = MasterGroup::orderBy('id_department')->get();
        return view('system5r.master-comitte.index', compact('department', 'group'));
    }


    public function getDataComittee()
    {
        $dataComittees = DepartmentComittee::orderBy('created_at', 'desc')->get();
    
        $totalDataComittee = [];
    
        foreach ($dataComittees as $dataComittee) {
            $comittes = [
                'id' => $dataComittee->id,
                'id_department' => $dataComittee->id_department,
                'nik_committee' => $dataComittee->nik_committee,
                'nama_committee' => $dataComittee->nama_committee,
                'is_active' => $dataComittee->is_active,
                'committee_utama' => $dataComittee->committee_utama,
            ];
    
            $totalDataComittee[] = $comittes;
        }
    
        $response = [   
            'data' => $totalDataComittee,
            'status' => 'success',
            'code' => 200,
        ];
    
        return response()->json($response);
    }
    

    public function storeDataComittee(Request $request)
    {
        try {
            $comitte = new DepartmentComittee();
            $comitte->id_department = $request->department;
            $comitte->nik_committee = $request->nik_committee;
            $comitte->nama_committee = $request->nama_committee;
            $comitte->is_active = 'Y';
            $comitte->committee_utama = 'N';
    
            $comitte->save();
    
            return response()->json([
                'status' => 1,
                'message' => 'Berhasil menambahkan user comittee'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 2,
                'message' => 'Ada kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteComittee(Request $request)
    {
        $committee = DepartmentComittee::where('id', $request->id)->first();

        if ($committee) {
            $committee->delete();
            return response()->json(['message' => 'Committee berhasil dihapus', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'Committee tidak ditemukan', 'status' => 'error'], 404);
        }
    }

    public function editDataComitte(Request $request)
    {
        $committee = DepartmentComittee::where('id', $request->id)->first();
    
        if ($committee) {
            $committee->id_department = $request->department;
            $committee->nik_committee = $request->nik_committee;
            $committee->nama_committee = $request->nama_committee;
            $committee->save(); 

            return response()->json(['message' => 'Data committee berhasil diupdate', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'Data committee tidak ditemukan', 'status' => 'error'], 404);
        }
    }

    public function ubahStatusComittee(Request $request){
        $request->validate([
            'id' => 'required|exists:5r_department_committee,id',
            'is_active' => 'required|in:Y,N',
        ]);
    
        $id_comittee = $request->id;
        $newStatus = $request->is_active;
    
        try {
            DB::table('5r_department_committee')
                ->where('id', $id_comittee)
                ->update(['is_active' => $newStatus]);
    
            return response()->json(['message' => 'Status berhasil diperbarui'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengubah status', 'error' => $e->getMessage()], 500);
        }
    }
    
    

}
