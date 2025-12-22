<?php

namespace App\Http\Controllers\System5R;

use App\Http\Controllers\Controller;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Jawaban;
use App\Models\System5R\JawabanDraft;
use App\Models\System5R\MasterGroup;
use App\Models\System5R\MasterPertanyaan;
use App\Models\System5R\JawabanGroup;
use App\Models\System5R\MasterArea;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterGroupJuriDepartment;
use App\Models\System5R\Periode;
use App\Models\System5R\Temuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            ->whereHas('group', function ($query) {
                $query->where('is_active', 'Y');
            })
            ->get();
        $isJuri = $anggota ? true : false;

        // dd($anggota);

        if (!$isJuri) {
            return view('system5r.penilaian.index', compact('isJuri'));
        }

        $groupJuri = $anggota->pluck('group.id_group_juri')->toArray();

        $department = MasterGroupJuriDepartment::whereIn('id_group_juri', $groupJuri)
            ->with('department')
            ->get();

        if (isset($_GET['filter_periode'])) {
            $jawabanGroup = JawabanGroup::where('id_periode', $_GET['filter_periode'])->get();
            $pertanyaan = MasterGroup::where('id_department', $_GET['filter_department'])
                ->where('id_group', $id_group)
                ->where('is_active', 'Y')
                ->get();

            $groups = MasterGroup::where('id_department', $_GET['filter_department'])
                ->where('is_active', 'Y')
                ->get();

            $periode = Periode::where('id_jadwal', $_GET['filter_jadwal'])->get();
        } else {
            $jawabanGroup = JawabanGroup::where('id_periode', '-----')->get();
            $pertanyaan = MasterGroup::where('id_department', '-----')
                ->where('is_active', 'Y')
                ->get();
            $periode = Periode::where('id_jadwal', '-----')->get();

            $groups = MasterGroup::where('id_department', '-----')
                ->where('is_active', 'Y')
                ->get();
        }

        $area = MasterArea::where('id_department', $department->pluck('id_department')->toArray())
            ->where('is_active', 'Y')
            ->get();

        $current_id_group = $id_group;

        return view('system5r.penilaian.index', compact('isJuri', 'groupJuri', 'pertanyaan', 'jawabanGroup', 'department', 'area', 'jadwal', 'periode', 'current_id_group', 'groups'));
    }

    public function doSubmit(Request $request)
    {
        $id_group = $request->id_group;
        $id_periode = $request->id_periode;
        $submit_by = auth()->user()->name;
        $nik_juri = auth()->user()->username;

        try {
            DB::beginTransaction();

            $jawabanGroup = JawabanGroup::create([
                'id_jawaban_group' => 'JG' . uniqid(),
                'id_group' => $id_group,
                'id_periode' => $id_periode,
                'submit_by' => $submit_by,
                'komplain_deadline' => date('Y-m-d', strtotime('+1 day'))
            ]);

            foreach ($request->nilai as $id_pertanyaan => $nilai) {
                // Ambil foto dari tabel temuan berdasarkan id_pertanyaan dan id_periode
                $temuanList = Temuan::where('id_pertanyaan', $id_pertanyaan)
                    ->where('id_periode', $id_periode)
                    ->whereNotNull('foto')
                    ->pluck('foto')
                    ->toArray();

                // Foto dari upload langsung (jika ada)
                $fotoPenilaian = [];
                if ($request->image != null && array_key_exists($id_pertanyaan, $request->image)) {
                    foreach ($request->image[$id_pertanyaan] as $image) {
                        $image = str_replace('data:image/jpeg;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = uniqid() . '.jpg';
                        $fotoPenilaian[] = $imageName;

                        \File::put('images/5r/' . $imageName, base64_decode($image));
                    }
                }

                // Gabungkan foto dari temuan dan foto upload langsung
                $allFotos = array_merge($temuanList, $fotoPenilaian);
                $imageNames = count($allFotos) > 0 ? implode(',', $allFotos) : null;

                // Create jawaban record
                $jawaban = Jawaban::create([
                    'id_jawaban_group' => $jawabanGroup->id_jawaban_group,
                    'id_pertanyaan' => $id_pertanyaan,
                    'nilai' => $nilai,
                    'foto' => $imageNames,
                    'keterangan' => $request->keterangan[$id_pertanyaan]
                ]);

                Temuan::where('id_pertanyaan', $id_pertanyaan)
                    ->where('id_periode', $id_periode)
                    ->whereNull('id_jawaban')
                    ->update(['id_jawaban' => $jawaban->id]);
            }

            DB::commit();

            JawabanDraft::where([
                'id_group' => $id_group,
                'id_periode' => $id_periode,
                'nik_juri' => $nik_juri
            ])->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveTemuan(Request $request)
    {
        try {
            DB::beginTransaction();

            $fotoArray = $request->foto;

            if (empty($fotoArray) || !is_array($fotoArray)) {
                throw new \Exception('Foto tidak valid');
            }

            $base64Image = $fotoArray[0];

            if (strpos($base64Image, 'data:image') !== false) {
                $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            }

            $base64Image = str_replace(' ', '+', $base64Image);

            $imageName = 'temuan_' . uniqid() . '_' . time() . '.jpg';

            if (!file_exists(public_path('images/5r/temuan'))) {
                mkdir(public_path('images/5r/temuan'), 0755, true);
            }

            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                throw new \Exception('Gagal decode base64 image');
            }

            \File::put(public_path('images/5r/temuan/' . $imageName), $imageData);

            // Create temuan record
            $temuan = Temuan::create([
                'id_temuan' => 'TM' . uniqid() . time(),
                'id_pertanyaan' => $request->id_pertanyaan,
                'id_periode' => $request->id_periode,
                'id_area' => $request->area,
                'foto' => $imageName,
                'deskripsi' => $request->deskripsi_temuan,
                'created_by' => auth()->user()->name
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Temuan berhasil disimpan',
                'data' => [
                    'id_temuan' => $temuan->id_temuan,
                    'foto_path' => asset('images/5r/temuan/' . $imageName)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan temuan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getListTemuan(Request $request)
    {
        try {
            $id_pertanyaan = $request->id_pertanyaan;
            $id_periode = $request->id_periode;

            $query = Temuan::where('id_pertanyaan', $id_pertanyaan)
                ->with(['area', 'pertanyaan', 'periode', 'jawaban'])
                ->orderBy('created_at', 'desc');

            if ($id_periode) {
                $query->where('id_periode', $id_periode);
            }

            $temuan = $query->get();

            return response()->json([
                'status' => 'success',
                'data' => $temuan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTemuan(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_temuan = $request->id_temuan;

            $temuan = Temuan::find($id_temuan);

            if (!$temuan) {
                throw new \Exception('Temuan tidak ditemukan');
            }

            // Hapus foto dari storage jika ada
            if ($temuan->foto) {
                $fotoPath = public_path('images/5r/temuan/' . $temuan->foto);
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            // Hapus data temuan dari database
            $temuan->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Temuan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus temuan: ' . $e->getMessage()
            ], 500);
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

            $isCommittee = DB::table('5r_department_committee')
                ->where('id_department', $id_department)
                ->where('nik_committee', $username)->exists();

            if ($isCommittee) {
                return response()->json(['success' => true, 'message' => 'Anda bukan komite di department ini'], Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'Username bukan merupakan anggota komite'], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['error' => 'Tidak ada anggota komite untuk department ini'], Response::HTTP_NOT_FOUND);
        }
    }

    public function saveDraft(Request $request)
    {
        try {
            $nikJuri   = auth()->user()->username;
            $idGroup   = $request->id_group;
            $idPeriode = $request->id_periode;

            // Ambil data dari request
            $draftData = [
                'nilai' => $request->nilai ?? [],
                'keterangan' => $request->keterangan ?? []
            ];

            // Jangan simpan kalau benar-benar kosong
            if (empty($draftData['nilai']) && empty($draftData['keterangan'])) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Tidak ada data untuk disimpan'
                ]);
            }

            $draft = JawabanDraft::updateOrCreate(
                [
                    'id_group'   => $idGroup,
                    'id_periode' => $idPeriode,
                    'nik_juri'   => $nikJuri
                ],
                [
                    'draft_data' => $draftData
                ]
            );

            return response()->json([
                'status'     => 'success',
                'message'    => 'Draft berhasil disimpan',
                'last_saved' => $draft->updated_at->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function loadDraft(Request $request)
    {
        try {
            $nikJuri = auth()->user()->username;
            $idGroup = $request->id_group;
            $idPeriode = $request->id_periode;

            $draft = JawabanDraft::where([
                'id_group' => $idGroup,
                'id_periode' => $idPeriode,
                'nik_juri' => $nikJuri
            ])->first();

            if ($draft) {
                return response()->json([
                    'status' => 'success',
                    'data' => $draft->draft_data,
                    'last_saved' => $draft->updated_at->format('d M Y H:i:s')
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Tidak ada draft tersimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteDraft(Request $request)
    {
        try {
            $nikJuri = auth()->user()->username;
            $idGroup = $request->id_group;
            $idPeriode = $request->id_periode;

            JawabanDraft::where([
                'id_group' => $idGroup,
                'id_periode' => $idPeriode,
                'nik_juri' => $nikJuri
            ])->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Draft berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
