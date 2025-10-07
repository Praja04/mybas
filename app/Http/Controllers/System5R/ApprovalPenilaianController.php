<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\DepartmentComittee;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterGroupJuriDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApprovalPenilaianController extends Controller
{
    public function index()
    {
        // Get committee info
        $committeeInfo = DepartmentComittee::where('nik_committee', auth()->user()->username)
        ->with('groups')
        ->first();

        if($committeeInfo == null) {
            return redirect()->route('5r-system.dashboard');
        }

        $groups = $committeeInfo->groups->pluck('id_group')->toArray();

        // Get the transactions
        $data = JawabanGroup::whereIn('id_group', $groups)
        ->with('periode')
        ->with('periode.jadwal')
        ->get();

        return view('system5r.approval-penilaian.index', compact('data'));
    }

    public function view($id_jawaban_group)
    {
        $data = JawabanGroup::where('id_jawaban_group', decrypt($id_jawaban_group))
        ->with('group')
        ->with('group.department')
        ->with('periode')
        ->with('periode.jadwal')
        ->first();

        // dd($data);

        $id_department = $data->group->department->id_department;
        $id_jadwal = $data->periode->jadwal->id_jadwal;
        $id_periode = $data->periode->id_periode;
        $id_group = $data->group->id_group;

        $jawabanGroup = $data;

        $pertanyaan = MasterGroup::where('id_department', $id_department)
        ->where('id_group', $id_group)
        ->get();

        return view('system5r.approval-penilaian.view', compact('data', 'jawabanGroup', 'pertanyaan', 'id_jawaban_group'));
    }

    public function submit(Request $request)
    {
        // dd($request->all());
        try{
            DB::beginTransaction();

            $attachment = $request->attachment;

            $isComplained = false;

            if($request->approve) {
                foreach($request->approve as $id_jawaban => $approve) {
                    $jawaban = Jawaban::find($id_jawaban);
                    
                    $jawaban->approval = $approve;
                    
                    $jawaban->nilai_before = $jawaban->nilai;

                    if($approve != 'TERIMA') {
                        $jawaban->alasan_komplain = $request->komentar[$id_jawaban];
                        
                        // Check if $attachment exists
                        if(isset($attachment[$id_jawaban])) {
                            $imageName = time() . '.' . $attachment[$id_jawaban]->extension();
                            \File::put('images/5r/attachment_complain/' . $imageName, $attachment[$id_jawaban]->get());
                            $jawaban->attachment_komplain = $imageName;
                        }

                        $isComplained = true;
                    }

                    $jawaban->save();
                }
            }

            $jawabanGroup = JawabanGroup::find($request->id_jawaban_group);
            $jawabanGroup->status = $isComplained ? 'complaining' : 'approved';

            if($isComplained) {
                $jawabanGroup->complained_at = now();
                $jawabanGroup->nik_complainer = auth()->user()->username;

                // Send email to juri utama
                $mapping = MasterGroupJuriDepartment::where('id_department', $jawabanGroup->group->id_department)
                ->where('id_periode', $jawabanGroup->id_periode)
                ->first();

                if($mapping != null) {
                    $id_group_juri = $mapping->id_group_juri;
                    $groupJuri = GroupJuriAnggota::where('id_group_juri', $id_group_juri)->where('juri_utama', 'Y')->first();

                    if($groupJuri != null) {
                        $email = $groupJuri->user->email;

                        if($email != null && $email != '') {
                            Mail::send('system5r.email.notifikasi', [
                                'title' => 'Protes Hasil Penilaian',
                                'description' => 'Ada protes nilai dari committee department (department ' . $mapping->department->nama_department . ' bagian ?) yang harus dilakukan follow up.
                                <br />
                                <br />
                                <div style="border: 1px solid #FFA800;">
                                    <span>Mohon untuk memberikan nilai yang sama seperti sebelumnya jika protes ditolak dan nilai sudah sesuai.</span>
                                </div>
                                <br />
                                ',
                                'link' => url('5r-system/penilaian/komplain')
                            ], function($message) use ($email) {
                                $message->to($email)->subject('5R FU Protes Hasil Penilaian');
                            });
                        }
                    }
                }
            }else{
                $jawabanGroup->approved_at = now();
                $jawabanGroup->nik_approver = auth()->user()->username;
            }

            $jawabanGroup->save();

            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menyimpan data');
        }catch(\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
