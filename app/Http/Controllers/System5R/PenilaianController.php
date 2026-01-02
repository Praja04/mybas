<?php

namespace App\Http\Controllers\System5R;

use PHPUnit\Util\Json;
use Illuminate\Http\Request;
use App\Models\System5R\Jadwal;
use App\Models\System5R\Temuan;
use App\Models\System5R\Jawaban;
use App\Models\System5R\Periode;
use Illuminate\Support\Facades\DB;
use App\Models\System5R\MasterArea;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\System5R\MasterGroup;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\System5R\JawabanDraft;
use App\Models\System5R\JawabanGroup;
use Illuminate\Support\Facades\Storage;
use App\Models\System5R\MasterIncrement;
use App\Models\System5R\GroupJuriAnggota;
use App\Models\System5R\MasterDepartment;
use App\Models\System5R\MasterPertanyaan;
use Symfony\Component\HttpFoundation\Response;
use App\Models\System5R\MasterGroupJuriDepartment;

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

        // $area = MasterArea::where('id_department', $department->pluck('id_department')->toArray())
        //     ->where('is_active', 'Y')
        //     ->get();

        if (request()->filled('filter_department')) {
            $area = MasterArea::where('id_department', request('filter_department'))
                ->where('is_active', 'Y')
                ->get();
        } else {
            $area = collect(); // kosongkan kalau belum pilih department
        }

        $current_id_group = $id_group;

        // Nilai Increment
        $activePeriodeId = request('filter_periode');
        $activeDepartment = request('filter_department');
        $activeJadwal = request('filter_jadwal');


        $incrementTerakhir = null;

        if ($activePeriodeId && $activeDepartment && $activeJadwal) {

            // Ambil created_at periode aktif
            $periodeAktif = Periode::where('id_periode', $activePeriodeId)->first();

            if ($periodeAktif) {
                $incrementTerakhir = MasterIncrement::where('id_department', $activeDepartment)
                    ->where('id_jadwal', $activeJadwal)
                    ->where('created_at', '<', $periodeAktif->created_at) // INI KUNCI NYA
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        // dd($incrementTerakhir, $activePeriodeId, $activeDepartment, $activeJadwal);

        // dd($area);
        return view('system5r.penilaian.index', compact(
            'isJuri',
            'groupJuri',
            'pertanyaan',
            'jawabanGroup',
            'department',
            'area',
            'jadwal',
            'periode',
            'current_id_group',
            'groups',
            'incrementTerakhir'
        ));
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
                // Ambil SEMUA foto dari tabel temuan berdasarkan id_pertanyaan dan id_periode
                $temuanList = Temuan::where('id_pertanyaan', $id_pertanyaan)
                    ->where('id_periode', $id_periode)
                    ->whereNotNull('foto')
                    ->get();

                // Kumpulkan semua foto dari temuan (bisa multiple per temuan)
                $fotoFromTemuan = [];
                foreach ($temuanList as $temuan) {
                    if ($temuan->foto) {
                        // Split foto jika ada koma (multiple photos)
                        $photos = explode(',', $temuan->foto);
                        foreach ($photos as $photo) {
                            $photo = trim($photo);
                            if (!empty($photo)) {
                                $fotoFromTemuan[] = $photo;
                            }
                        }
                    }
                }

                // Foto dari upload langsung via form (jika ada)
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

                // Gabungkan semua foto: dari temuan + dari upload langsung
                $allFotos = array_merge($fotoFromTemuan, $fotoPenilaian);

                // Hilangkan duplikat (jika ada)
                $allFotos = array_unique($allFotos);

                // Gabungkan dengan koma untuk disimpan di database
                $imageNames = count($allFotos) > 0 ? implode(',', $allFotos) : null;

                // Create jawaban record
                $jawaban = Jawaban::create([
                    'id_jawaban_group' => $jawabanGroup->id_jawaban_group,
                    'id_pertanyaan' => $id_pertanyaan,
                    'nilai' => $nilai,
                    'foto' => $imageNames, // Format: foto1.jpg,foto2.jpg,foto3.jpg
                    'keterangan' => $request->keterangan[$id_pertanyaan]
                ]);

                // Update semua temuan terkait dengan id_jawaban
                Temuan::where('id_pertanyaan', $id_pertanyaan)
                    ->where('id_periode', $id_periode)
                    ->whereNull('id_jawaban')
                    ->update(['id_jawaban' => $jawaban->id]);
            }

            DB::commit();

            // Hapus draft jika ada
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

            // Array untuk menyimpan nama-nama file foto
            $savedPhotos = [];

            // Create direktori jika belum ada
            if (!file_exists(public_path('images/5r/temuan'))) {
                mkdir(public_path('images/5r/temuan'), 0755, true);
            }

            // Generate ID temuan DULU (sebelum save foto)
            $idTemuan = 'TM' . time() . uniqid('', true) . bin2hex(random_bytes(4));

            // Process setiap foto
            foreach ($fotoArray as $index => $base64Image) {
                // Hapus prefix data:image jika ada
                if (strpos($base64Image, 'data:image') !== false) {
                    $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
                }

                $base64Image = str_replace(' ', '+', $base64Image);

                // Decode image data
                $imageData = base64_decode($base64Image);

                if ($imageData === false) {
                    throw new \Exception('Gagal decode base64 image pada foto ke-' . ($index + 1));
                }

                // ===================================================================
                // SOLUSI PRODUCTION: Gunakan Str::uuid() atau DB Auto Increment
                // ===================================================================

                // Method 1: UUID v4 (RECOMMENDED untuk production)
                // Guaranteed unique secara global, bahkan di distributed system
                $uuid = \Illuminate\Support\Str::uuid()->toString();
                $uuidShort = str_replace('-', '', $uuid); // Remove dashes

                // Method 2: Tambahkan random bytes ekstra
                $extraRandom = bin2hex(random_bytes(8)); // 16 karakter

                // Method 3: Tambahkan process ID (untuk multi-process server)
                $processId = getmypid(); // Process ID

                // Gabungkan semua untuk MAXIMUM uniqueness
                $imageName = sprintf(
                    'tm_%s_%s_%d_%03d_%s.jpg',
                    $request->area,           // Area ID
                    $uuidShort,               // UUID (32 chars, guaranteed unique)
                    $processId,               // Process ID
                    $index + 1,               // Index foto
                    $extraRandom              // Extra random (16 chars)
                );

                // Sanitize (remove special chars jika ada)
                $imageName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName);

                // FINAL CHECK: Jika masih duplikat (impossible, tapi just in case)
                $finalPath = public_path('images/5r/temuan/' . $imageName);
                $retry = 0;
                while (file_exists($finalPath) && $retry < 3) {
                    $retry++;
                    // Regenerate dengan timestamp nano + retry count
                    $nanoTime = hrtime(true); // Nanosecond precision
                    $imageName = sprintf(
                        'tm_%s_%s_%d_%03d_%s_%d_%d.jpg',
                        $request->area,
                        $uuidShort,
                        $processId,
                        $index + 1,
                        $extraRandom,
                        $nanoTime,
                        $retry
                    );
                    $imageName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName);
                    $finalPath = public_path('images/5r/temuan/' . $imageName);
                }

                // Simpan foto
                \File::put($finalPath, $imageData);

                $savedPhotos[] = $imageName;

                // Log untuk debugging di production
                Log::info('Foto saved', [
                    'filename' => $imageName,
                    'size' => strlen($imageData),
                    'index' => $index
                ]);
            }

            // Gabungkan semua nama foto dengan koma
            $fotoString = implode(',', $savedPhotos);

            // Create temuan record
            $temuan = Temuan::create([
                'id_temuan' => $idTemuan,
                'id_pertanyaan' => $request->id_pertanyaan,
                'id_periode' => $request->id_periode,
                'id_area' => $request->area,
                'foto' => $fotoString,
                'deskripsi' => $request->deskripsi_temuan,
                'created_by' => auth()->user()->name
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Temuan dengan ' . count($savedPhotos) . ' foto berhasil disimpan',
                'data' => [
                    'id_temuan' => $temuan->id_temuan,
                    'foto_count' => count($savedPhotos),
                    'foto_list' => $savedPhotos
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Save Temuan Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['foto']) // Jangan log base64
            ]);

            // Hapus foto yang sudah tersimpan jika terjadi error
            if (!empty($savedPhotos)) {
                foreach ($savedPhotos as $photo) {
                    $photoPath = public_path('images/5r/temuan/' . $photo);
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
            }

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

            // CRITICAL FIX: Validasi id_temuan format
            if (empty($id_temuan)) {
                throw new \Exception('ID Temuan tidak valid');
            }

            Log::info('=== DELETE TEMUAN REQUEST ===', [
                'id_temuan_raw' => $id_temuan,
                'id_temuan_type' => gettype($id_temuan),
                'request_all' => $request->all()
            ]);

            // Lock row untuk mencegah race condition
            // GUNAKAN whereRaw untuk memastikan exact match
            $temuan = Temuan::where('id_temuan', '=', $id_temuan)
                ->lockForUpdate()
                ->first();

            if (!$temuan) {
                Log::error('Temuan not found', [
                    'id_temuan' => $id_temuan,
                    'query' => Temuan::where('id_temuan', '=', $id_temuan)->toSql()
                ]);
                throw new \Exception('Temuan tidak ditemukan (ID: ' . $id_temuan . ')');
            }

            // CRITICAL: Simpan data penting sebelum proses apa pun
            $idPertanyaan = $temuan->id_pertanyaan;
            $idPeriode = $temuan->id_periode;
            $idJawaban = $temuan->id_jawaban;
            $idArea = $temuan->id_area;
            $fotoTemuan = $temuan->foto ? array_map('trim', explode(',', $temuan->foto)) : [];

            Log::info('=== DELETE TEMUAN START ===', [
                'id_temuan' => $id_temuan,
                'id_temuan_db' => $temuan->id_temuan,
                'id_pertanyaan' => $idPertanyaan,
                'id_periode' => $idPeriode,
                'id_area' => $idArea,
                'foto_count' => count($fotoTemuan),
                'foto_list' => $fotoTemuan
            ]);

            // CEK: Apakah foto ini digunakan oleh temuan lain (TANPA BATASAN AREA)
            // Kita cek SEMUA temuan lain di pertanyaan dan periode yang sama
            $temuanLainSemua = Temuan::where('id_pertanyaan', $idPertanyaan)
                ->where('id_periode', $idPeriode)
                ->where('id_temuan', '!=', $id_temuan) // EXCLUDE temuan yang akan dihapus
                ->whereNotNull('foto')
                ->lockForUpdate()
                ->get();

            Log::info('Found other temuan', [
                'count' => $temuanLainSemua->count(),
                'ids' => $temuanLainSemua->pluck('id_temuan')->toArray()
            ]);

            // Kumpulkan SEMUA foto dari SEMUA temuan lain (termasuk area berbeda)
            $fotoYangMasihDigunakan = [];
            foreach ($temuanLainSemua as $temuanLain) {
                if ($temuanLain->foto) {
                    $fotoLain = array_map('trim', explode(',', $temuanLain->foto));
                    foreach ($fotoLain as $foto) {
                        if (!empty($foto)) {
                            $fotoYangMasihDigunakan[] = $foto;
                        }
                    }
                }
            }
            $fotoYangMasihDigunakan = array_unique($fotoYangMasihDigunakan);

            Log::info('Foto usage check', [
                'total_temuan_lain' => $temuanLainSemua->count(),
                'foto_temuan_ini' => $fotoTemuan,
                'foto_masih_digunakan' => $fotoYangMasihDigunakan
            ]);

            // HANYA hapus foto yang TIDAK digunakan temuan lain MANA PUN
            $fotoYangAmanDihapus = [];
            $fotoYangDipertahankan = [];

            foreach ($fotoTemuan as $foto) {
                if (in_array($foto, $fotoYangMasihDigunakan)) {
                    $fotoYangDipertahankan[] = $foto;
                    Log::info('Foto DIPERTAHANKAN (masih digunakan temuan lain)', ['foto' => $foto]);
                } else {
                    $fotoYangAmanDihapus[] = $foto;
                    Log::info('Foto AKAN DIHAPUS (tidak digunakan temuan lain)', ['foto' => $foto]);
                }
            }

            // Update field foto di jawaban (jika ada)
            $jawabanUpdated = false;
            if ($idJawaban) {
                $jawaban = Jawaban::where('id', $idJawaban)
                    ->lockForUpdate()
                    ->first();

                if ($jawaban && $jawaban->foto) {
                    $fotoJawaban = array_map('trim', explode(',', $jawaban->foto));

                    Log::info('Updating jawaban', [
                        'foto_jawaban_before' => $fotoJawaban,
                        'foto_yang_aman_dihapus' => $fotoYangAmanDihapus
                    ]);

                    // Hapus HANYA foto yang aman dihapus dari jawaban
                    $fotoJawabanBaru = [];
                    foreach ($fotoJawaban as $foto) {
                        if (!in_array($foto, $fotoYangAmanDihapus)) {
                            $fotoJawabanBaru[] = $foto;
                        }
                    }

                    // Update jawaban
                    if (empty($fotoJawabanBaru)) {
                        $jawaban->foto = null;
                    } else {
                        $jawaban->foto = implode(',', $fotoJawabanBaru);
                    }

                    $jawaban->save();
                    $jawabanUpdated = true;

                    Log::info('Jawaban updated', [
                        'foto_jawaban_after' => $jawaban->foto
                    ]);
                }
            }

            // Hapus file fisik HANYA yang aman dihapus
            $deletedFiles = 0;
            $failedFiles = [];

            foreach ($fotoYangAmanDihapus as $foto) {
                if (!empty($foto)) {
                    $fotoPath = public_path('images/5r/temuan/' . $foto);

                    if (file_exists($fotoPath)) {
                        try {
                            if (unlink($fotoPath)) {
                                $deletedFiles++;
                                Log::info('File DELETED', ['file' => $foto]);
                            } else {
                                $failedFiles[] = $foto;
                                Log::warning('File DELETE FAILED', ['file' => $foto]);
                            }
                        } catch (\Exception $e) {
                            $failedFiles[] = $foto;
                            Log::error('File DELETE ERROR', [
                                'file' => $foto,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        Log::warning('File NOT FOUND', ['file' => $foto, 'path' => $fotoPath]);
                    }
                }
            }

            // CRITICAL: Hapus data temuan dari database dengan exact match
            $deleted = Temuan::where('id_temuan', '=', $id_temuan)->delete();

            if ($deleted === 0) {
                throw new \Exception('Gagal menghapus temuan dari database');
            }

            DB::commit();

            Log::info('=== DELETE TEMUAN SUCCESS ===', [
                'id_temuan' => $id_temuan,
                'deleted_records' => $deleted,
                'deleted_files' => $deletedFiles,
                'preserved_files' => count($fotoYangDipertahankan),
                'failed_files' => count($failedFiles)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Temuan berhasil dihapus',
                'data' => [
                    'deleted_photos' => $deletedFiles,
                    'preserved_photos' => count($fotoYangDipertahankan),
                    'failed_photos' => count($failedFiles),
                    'jawaban_updated' => $jawabanUpdated
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== DELETE TEMUAN ERROR ===', [
                'id_temuan' => $request->id_temuan,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus temuan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSinglePhoto(Request $request)
    {
        try {
            DB::beginTransaction();

            $id_temuan = $request->id_temuan;
            $foto_name = trim($request->foto_name);

            Log::info('=== DELETE SINGLE PHOTO START ===', [
                'id_temuan' => $id_temuan,
                'id_temuan_type' => gettype($id_temuan),
                'foto_name' => $foto_name
            ]);

            // Validasi input
            if (empty($foto_name)) {
                throw new \Exception('Nama foto tidak valid');
            }

            if (empty($id_temuan)) {
                throw new \Exception('ID Temuan tidak valid');
            }

            // Lock temuan untuk mencegah race condition
            $temuan = Temuan::where('id_temuan', '=', $id_temuan)
                ->lockForUpdate()
                ->first();

            if (!$temuan) {
                Log::error('Temuan not found for photo delete', [
                    'id_temuan' => $id_temuan,
                    'foto_name' => $foto_name
                ]);
                throw new \Exception('Temuan tidak ditemukan (ID: ' . $id_temuan . ')');
            }

            Log::info('Temuan info', [
                'id_temuan_db' => $temuan->id_temuan,
                'id_pertanyaan' => $temuan->id_pertanyaan,
                'id_periode' => $temuan->id_periode,
                'id_area' => $temuan->id_area,
                'current_foto' => $temuan->foto
            ]);

            // Parse foto yang ada di temuan ini
            $fotoArray = $temuan->foto ? array_map('trim', explode(',', $temuan->foto)) : [];

            Log::info('Photos in current temuan', [
                'foto_array' => $fotoArray,
                'count' => count($fotoArray)
            ]);

            // Cari foto yang diminta
            $key = array_search($foto_name, $fotoArray);

            if ($key === false) {
                throw new \Exception('Foto tidak ditemukan dalam temuan ini');
            }

            // CRITICAL FIX: Cek apakah foto ini digunakan oleh temuan lain
            // (TERMASUK AREA BERBEDA dalam pertanyaan dan periode yang sama)
            $temuanLainDenganFotoSama = Temuan::where('id_temuan', '!=', $id_temuan)
                ->where('id_pertanyaan', $temuan->id_pertanyaan)
                ->where('id_periode', $temuan->id_periode)
                ->whereNotNull('foto')
                ->lockForUpdate()
                ->get();

            $fotoDigunakanTemuanLain = false;
            $temuanLainYangMenggunakanFoto = [];

            foreach ($temuanLainDenganFotoSama as $temuanLain) {
                if ($temuanLain->foto) {
                    $fotoTemuanLain = array_map('trim', explode(',', $temuanLain->foto));

                    if (in_array($foto_name, $fotoTemuanLain)) {
                        $fotoDigunakanTemuanLain = true;
                        $temuanLainYangMenggunakanFoto[] = [
                            'id_temuan' => $temuanLain->id_temuan,
                            'id_area' => $temuanLain->id_area,
                            'area_nama' => $temuanLain->area->nama_area ?? 'Unknown'
                        ];
                    }
                }
            }

            Log::info('Photo usage check result', [
                'foto_name' => $foto_name,
                'used_by_others' => $fotoDigunakanTemuanLain,
                'temuan_lain_count' => count($temuanLainYangMenggunakanFoto),
                'temuan_lain_details' => $temuanLainYangMenggunakanFoto
            ]);

            // Hapus foto dari array temuan ini
            unset($fotoArray[$key]);
            $fotoArray = array_values($fotoArray); // Re-index array

            // Update temuan
            if (empty($fotoArray)) {
                $temuan->foto = null;
            } else {
                $temuan->foto = implode(',', $fotoArray);
            }
            $temuan->save();

            Log::info('Temuan updated', [
                'foto_before' => $request->foto_name,
                'foto_after' => $temuan->foto,
                'remaining_count' => count($fotoArray)
            ]);

            // Update jawaban jika ada
            $jawabanUpdated = false;
            if ($temuan->id_jawaban) {
                $jawaban = Jawaban::where('id', $temuan->id_jawaban)
                    ->lockForUpdate()
                    ->first();

                if ($jawaban && $jawaban->foto) {
                    $fotoJawaban = array_map('trim', explode(',', $jawaban->foto));

                    Log::info('Jawaban before update', [
                        'foto_jawaban' => $fotoJawaban
                    ]);

                    // HANYA hapus dari jawaban jika foto tidak digunakan temuan lain
                    if (!$fotoDigunakanTemuanLain) {
                        $keyJawaban = array_search($foto_name, $fotoJawaban);

                        if ($keyJawaban !== false) {
                            unset($fotoJawaban[$keyJawaban]);
                            $fotoJawaban = array_values($fotoJawaban);

                            if (empty($fotoJawaban)) {
                                $jawaban->foto = null;
                            } else {
                                $jawaban->foto = implode(',', $fotoJawaban);
                            }
                            $jawaban->save();
                            $jawabanUpdated = true;

                            Log::info('Jawaban updated (foto dihapus)', [
                                'foto_after' => $jawaban->foto
                            ]);
                        }
                    } else {
                        Log::info('Jawaban NOT updated (foto masih digunakan temuan lain)');
                    }
                }
            }

            // Hapus file fisik HANYA jika tidak digunakan temuan lain
            $fileDeleted = false;
            if (!$fotoDigunakanTemuanLain) {
                $fotoPath = public_path('images/5r/temuan/' . $foto_name);

                if (file_exists($fotoPath)) {
                    try {
                        if (unlink($fotoPath)) {
                            $fileDeleted = true;
                            Log::info('Physical file DELETED', ['file' => $foto_name]);
                        } else {
                            Log::warning('Physical file DELETE FAILED', ['file' => $foto_name]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Physical file DELETE ERROR', [
                            'file' => $foto_name,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    Log::warning('Physical file NOT FOUND', ['file' => $foto_name, 'path' => $fotoPath]);
                }
            } else {
                Log::info('Physical file PRESERVED (still used by other temuan)', [
                    'file' => $foto_name,
                    'used_by' => $temuanLainYangMenggunakanFoto
                ]);
            }

            DB::commit();

            $message = $fileDeleted
                ? 'Foto berhasil dihapus dari temuan dan file fisik terhapus'
                : ($fotoDigunakanTemuanLain
                    ? 'Foto dihapus dari temuan ini (file dipertahankan karena masih digunakan di area lain)'
                    : 'Foto dihapus dari temuan ini');

            Log::info('=== DELETE SINGLE PHOTO SUCCESS ===', [
                'file_deleted' => $fileDeleted,
                'file_preserved' => $fotoDigunakanTemuanLain,
                'jawaban_updated' => $jawabanUpdated,
                'remaining_photos' => count($fotoArray)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'remaining_photos' => count($fotoArray),
                    'file_deleted' => $fileDeleted,
                    'file_preserved' => $fotoDigunakanTemuanLain,
                    'preserved_reason' => $fotoDigunakanTemuanLain ? 'Foto masih digunakan di: ' . implode(', ', array_column($temuanLainYangMenggunakanFoto, 'area_nama')) : null,
                    'jawaban_updated' => $jawabanUpdated
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== DELETE SINGLE PHOTO ERROR ===', [
                'id_temuan' => $request->id_temuan,
                'foto_name' => $request->foto_name,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
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
