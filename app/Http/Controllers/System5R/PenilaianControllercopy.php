<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterPertanyaan;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class PenilaianController extends Controller
{
    public function index($id_group = '--')
    {
        
        $jadwal = Jadwal::all();
        
        // First find if Heri Lesmana 25749 is a part of juri
        $anggota = GroupJuriAnggota::where('nik_juri', auth()->user()->username)
        ->with('group')
        // Where group is active
        ->whereHas('group', function($query) {
            $query->where('is_active', 'Y');
        })
        ->get();
        $isJuri = $anggota ? true : false;
        
        if(!$isJuri) {
            return view('system5r.penilaian.index', compact('isJuri'));
        }
        
        $groupJuri = $anggota->pluck('group.id_group_juri')->toArray();

        $department = MasterGroupJuriDepartment::whereIn('id_group_juri', $groupJuri)
        ->with('department')
        ->get();

        if(isset($_GET['filter_periode'])) {
            $jawabanGroup = JawabanGroup::where('id_periode', $_GET['filter_periode'])->get();
            $pertanyaan = MasterGroup::where('id_department', $_GET['filter_department'])
            ->where('id_group', $id_group)
            ->where('is_active', 'Y')
            ->get();

            $groups = MasterGroup::where('id_department', $_GET['filter_department'])
            ->where('is_active', 'Y')
            ->get();

            $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
        }else{
            $jawabanGroup = JawabanGroup::where('id_periode', '-----')->get();
            $pertanyaan = MasterGroup::where('id_department', '-----')
            ->where('is_active', 'Y')
            ->get();
            $periode = Periode::where('id_jadwal', '-----')->get();

            $groups = MasterGroup::where('id_department', '-----')
            ->where('is_active', 'Y')
            ->get();
        }

        $current_id_group = $id_group;

        return view('system5r.penilaian.index', compact('isJuri', 'groupJuri', 'pertanyaan', 'jawabanGroup', 'department', 'jadwal', 'periode', 'current_id_group', 'groups'));
    }

    public function doSubmit(Request $request)
    {
        $id_group = $request->id_group;
        $id_periode = $request->id_periode;
        $submit_by = auth()->user()->name;

        try {
            DB::beginTransaction();

            $jawabanGroup = JawabanGroup::create([
                'id_jawaban_group' => 'JG' . uniqid(),
                'id_group' => $id_group,
                'id_periode' => $id_periode,
                'submit_by' => $submit_by,
                'komplain_deadline' => date('Y-m-d', strtotime('+1 day'))
            ]);

            foreach($request->nilai as $id_pertanyaan => $nilai) {
                // If image is not null
                $imageNames = [];
                if($request->image != null && array_key_exists($id_pertanyaan, $request->image)) {
                    // Save base64 image to storage
                    foreach($request->image[$id_pertanyaan] as $image) {
                        $image = str_replace('data:image/jpeg;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = uniqid() . '.jpg';
                        $imageNames[] = $imageName;
                        
                        // Store to public folder
                        \File::put('images/5r/' . $imageName, base64_decode($image));
                        // Save to storage pas_nas
                        // Storage::put('images/5r/' . $imageName, base64_decode($image));
                    }

                }

                $imageNames = count($imageNames) > 0 ? implode(',', $imageNames) : null;

                $arrayData = [
                    'id_jawaban_group' => $jawabanGroup->id_jawaban_group,
                    'id_pertanyaan' => $id_pertanyaan,
                    'nilai' => $nilai,
                    'foto' => $imageNames,
                    'keterangan' => $request->keterangan[$id_pertanyaan]
                ];

                Jawaban::create($arrayData);
            }

            DB::commit();

            // Send email ke departemen komite utama
            // $group = MasterGroup::find($id_group);
            // $department = MasterDepartment::find($group->id_department);
            // $commitee_utama = $department->committee->where('committee_utama', 'Y')->first();

            // if($commitee_utama != null && $commitee_utama->user != null && $commitee_utama->user->email != null && $commitee_utama->user->email != '') {
            //     $email = $commitee_utama->user->email;

            //     Mail::send('system5r.email.notifikasi', [
            //         'title' => 'Approval Penilaian',
            //         'description' => 'Penilaian 5R di department ' . $department->nama_department . ' bagian ' . $group->nama_group . ' telah selesai dilakukan. Silahkan melakukan approval penilaian.
            //         <br />
            //         <br />
            //         Deadline protes nilai : ' . date('d M Y', strtotime($jawabanGroup->komplain_deadline)) . ' (H+1),
            //         <span style="color: #F64E60 !important">Protes tidak bisa dilakukan jika melebihi tanggal tersebut!.</span>
            //         <br />
            //         <br />
            //         <div style="border: 1px solid #FFA800;">
            //             <span>Mohon melakukan protes secara bijak, karena proses penilaian sudah dilakukan dengan pertimbangan yang matang oleh juri yang kompeten.</span>
            //         </div>
            //         <br />
            //         ',
            //         'link' => url('5r-system/penilaian/approval')
            //     ], function($message) use ($email) {
            //         $message->to($email)->subject('5R Approval Penilaian');
            //     });
            // }

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPeriode($idJadwal)
    {
        $periode = Periode::where('id_jadwal', $idJadwal)->get();

        return response()->json([
            'status' => 'success',
            'data' => $periode
        ]);
    }

    public function validateCredentials(Request $request, $id_group)
    {
        $id_department = DB::table('5r_master_group')
                            ->where('id_group', $id_group)
                            ->value('id_department');
    
        $committeeCount = DB::table('5r_department_committee')
                            ->where('id_department', $id_department)
                            ->count();
    
        if ($committeeCount > 0) {
            $username = $request->input('username');
            $password = $request->input('password');
    
            $user = DB::table('users')->where('username', $username)->first();
    
            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['error' => 'Username atau password salah'], Response::HTTP_UNAUTHORIZED);
            }
    
            $isCommittee = DB::table('5r_department_committee')->where('nik_committee', $username)->exists();
    
            if ($isCommittee) {
                return response()->json(['success' => true, 'message' => 'Username dan password sesuai'], Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'Username bukan merupakan anggota komite'], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['error' => 'Tidak ada anggota komite untuk department ini'], Response::HTTP_NOT_FOUND);
        }
    }
}
