<?php

namespace App\Http\Controllers\HRConnect;

use Carbon\Carbon;
use App\HrKaryawan;
use App\HrGoodieApd;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Imports\HRConnect\GaShiftIn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class GAShiftInController extends Controller
{
    private function pairRak($kodeRak)
    {
        $map = [
            'PB' => 'PS',
            'WB' => 'WS',
        ];

        return $map[$kodeRak] ?? null;
    }

    public function getData(Request $req)
    {
        $query = HrKaryawan::where('in_complete', 'N')->where('tanggal_masuk', '>', '2025-01-01');

        if ($req->tampilkan_semua == 0) {
            $query->where([
                'tanggal_masuk' => $req->tanggal,
            ]);
        }

        $data = $query->get();

        return Datatables::of($data)->make(true);
    }

    public function index()
    {
        $data['title'] = 'GA - Karyawan Masuk';

        // $data['lokers_pria'] = DB::table('loker_rak')
        //     ->whereIn('kode_rak', ['PB'])
        //     ->get();

        // $data['lokers_wanita'] = DB::table('loker_rak')
        //     ->whereIn('kode_rak', ['WB'])
        //     ->get();

       $data['lokers_pria'] = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->where('lp.is_active', 'Y');
            })
            ->where('lr.kode_rak', 'PB')
            ->where('lr.is_active', 'Y')
            ->select(
                'lr.id',
                'lr.kode_rak',
                'lr.no_loker',
                DB::raw('COUNT(DISTINCT lp.nik) as total_penghuni'),
                DB::raw('MAX(lp.staff) as staff_type')
            )
            ->groupBy('lr.id', 'lr.kode_rak', 'lr.no_loker')
            ->havingRaw('
                COUNT(DISTINCT lp.nik) <
                CASE
                    WHEN MAX(lp.staff) = "staff" THEN 1
                    WHEN MAX(lp.staff) IN ("non_staff", "mitra_kerja") THEN 2
                    ELSE 2
                END
            ')
            ->orderBy('lr.no_loker')
            ->get();


       $data['lokers_wanita'] = DB::table('loker_rak as lr')
            ->leftJoin('loker_penghuni as lp', function ($join) {
                $join->on('lr.kode_rak', '=', 'lp.kode_rak')
                    ->on('lr.no_loker', '=', 'lp.no_loker')
                    ->where('lp.is_active', 'Y');
            })
            ->where('lr.kode_rak', 'WB')
            ->where('lr.is_active', 'Y')
            ->select(
                'lr.id',
                'lr.kode_rak',
                'lr.no_loker',
                DB::raw('COUNT(DISTINCT lp.nik) as total_penghuni'),
                DB::raw('MAX(lp.staff) as staff_type')
            )
            ->groupBy('lr.id', 'lr.kode_rak', 'lr.no_loker')
            ->havingRaw('
                COUNT(DISTINCT lp.nik) <
                CASE
                    WHEN MAX(lp.staff) = "staff" THEN 1
                    WHEN MAX(lp.staff) IN ("non_staff", "mitra_kerja") THEN 2
                    ELSE 2
                END
            ')
            ->orderBy('lr.no_loker')
            ->get();

        return view('hr-connect.ga.shift-in', $data);
    }

    // public function updateStatus(Request $request)
    // {
    //     $data = $request->input('data');
    //      dd($data);
    //     if (!empty($data)) {
    //         $totalChecked = count($data);

    //         HrGoodieApd::create([
    //             'tgl_masuk' => Carbon::parse(now())->format('Y-m-d'),
    //             'jumlah_orang' => $totalChecked,
    //         ]);

    //         // [x] Uncommenct jangan lupa kalo udah selesai Goodie APD nya!
    //         foreach ($data as $item) {
    //             $lokerId = $item['lokerId'];
    //             $idCard = $item['idCard'];
    //             $kodeArea = $item['kodeArea'];
    //             $namaLoker = $item['namaLoker'];
    //             $nomorLoker = $item['nomorLoker'];
    //             $nik = $item['nik'];
    //             $nama = $item['nama'];
    //             $jk = $item['jk'];
    //             $divisi = $item['divisi'];
    //             $bagian = $item['bagian'];
    //             $group = $item['group'];
    //             $kodekontrak = $item['kodekontrak'];

    //             // Buat histori transaksi dulu
    //             $sebelumnya = DB::table('loker_master_user')
    //                             ->where([
    //                                 'kode_area' => $kodeArea,
    //                                 'kode_blok' => $namaLoker,
    //                                 'no_loker' => $nomorLoker
    //                             ])->first();

    //             $data = [
    //                 'nik' => $sebelumnya->nik,
    //                 'no_loker' => $nomorLoker,
    //                 'status' => 'IN',
    //                 'keterangan' => 'Karyawan Baru Join',
    //                 'nama_pengisi' => auth()->user()->name ?? '',
    //                 'tgl_pengisi' => date('Y-m-d'),
    //                 'nik_pengisi' => auth()->user()->username ?? '',
    //                 'jam_pengisi' => date('H:i:s'),
    //                 'penghuni_sebelumnya' => $sebelumnya->nama,
    //                 'alasan' => 'Karyawan Baru Join',
    //                 'kode_area' => $kodeArea,
    //                 'kode_blok' => $namaLoker
    //             ];

    //             DB::table('loker_user_transaksi')->insert($data);

    //             // Update loker menjadi status 1
    //             DB::table('loker_master_nomer')
    //                 ->where('id', $lokerId)
    //                 ->update(['status' => 1]);

    //             // $data = [
    //             //     'nik' => $nik,
    //             //     'nama' => $nama,
    //             //     'jk' => $jk,
    //             //     'divisi' => $divisi,
    //             //     'bagian' => $bagian,
    //             //     'group' => $group,
    //             //     'kode_kontrak' => $kodekontrak,
    //             //     'kode_area' => $kodeArea,
    //             //     'kode_blok' => $namaLoker,
    //             //     'no_loker' => $nomorLoker,
    //             // ];
    //             // DB::table('loker_master_user')->insert($data);

    //             DB::table('loker_master_user')
    //                 ->where([
    //                     'kode_area' => $kodeArea,
    //                     'kode_blok' => $namaLoker,
    //                     'no_loker' => $nomorLoker
    //                 ])->update([
    //                     'nik' => $nik,
    //                     'nama' => $nama,
    //                     'jk' => $jk,
    //                     'divisi' => $divisi,
    //                     'bagian' => $bagian,
    //                     'group' => $group,
    //                     'kode_kontrak' => $kodekontrak,
    //                 ]);

    //             DB::table('hr_karyawan')
    //                 ->where('id', $idCard)
    //                 ->update(['in_complete' => 'Y']);
    //         }

    //         return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Tidak ada data yang dikirim.']);
    //     }
    // }

    public function updateStatus(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->input('data');

            if (empty($data)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Tidak ada data yang dikirim.',
                    ],
                    400,
                );
            }

            HrGoodieApd::create([
                'tgl_masuk' => now()->format('Y-m-d'),
                'jumlah_orang' => count($data),
            ]);

            foreach ($data as $item) {
                $nik = $item['nik'];
                $nama = $item['nama'];
                $jk = $item['jk'];
                $divisi = $item['divisi'];
                $kodeRak = $item['kodeRak']; // PB / WB
                $noLoker = (int) $item['noLoker'];
                $staff = $item['staff']; // staff | non_staff | mitra_kerja
                $idCard = $item['idCard'];

                /* =====================================================
                 * 1️⃣ Cegah user punya lebih dari 1 loker aktif
                 * ===================================================== */
                $hasActive = DB::table('loker_penghuni')->where('nik', $nik)->where('is_active', 'Y')->lockForUpdate()->exists();

                if ($hasActive) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "NIK {$nik} sudah memiliki loker aktif.",
                        ],
                        422,
                    );
                }

                /* =====================================================
                 * 2️⃣ Tentukan rak utama + pasangan
                 * ===================================================== */
                $pairRak = $this->pairRak($kodeRak); // PB→PS, WB→WS
                $rakDicek = array_filter([$kodeRak, $pairRak]);

                /* =====================================================
                 * 3️⃣ Cek isi loker (gabungan rak utama + pasangan)
                 * ===================================================== */
                $rows = DB::table('loker_penghuni')->select('staff', DB::raw('COUNT(DISTINCT nik) as cnt'))->whereIn('kode_rak', $rakDicek)->where('no_loker', $noLoker)->groupBy('staff')->lockForUpdate()->get();

                // Tidak boleh campur kategori
                if ($rows->count() > 1) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} tidak valid karena terdapat campuran kategori.",
                        ],
                        409,
                    );
                }

                $existingType = $rows->first()->staff ?? null;
                $existingCount = (int) ($rows->first()->cnt ?? 0);

                $staffLabel = ucwords(str_replace('_', ' ', $existingType));

                if ($existingType !== null && $existingType !== $staff) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} sudah dipakai oleh {$staffLabel}.",
                        ],
                        422,
                    );
                }

                /* =====================================================
                 * 4️⃣ Validasi kapasitas
                 * ===================================================== */
                $type = $existingType ?? $staff;

                switch ($type) {
                    case 'staff':
                        $maxCapacity = 1;
                        break;

                    case 'non_staff':
                    case 'mitra_kerja':
                        $maxCapacity = 2;
                        break;

                    default:
                        DB::rollBack();
                        return response()->json(
                            [
                                'success' => false,
                                'message' => 'Kategori karyawan tidak valid.',
                            ],
                            422,
                        );
                }

                if ($existingCount >= $maxCapacity) {
                    DB::rollBack();
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Loker {$kodeRak}-{$noLoker} sudah penuh oleh {$staffLabel}.",
                        ],
                        422,
                    );
                }

                $penghuniSebelumnya = DB::table('loker_user_transaksi')
                    ->where('no_loker', $noLoker)
                    ->where('kode_rak', $kodeRak)
                    ->where('status', 'OUT')
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->value('penghuni_sebelumnya');

                /* =====================================================
                 * 5️⃣ Insert transaksi utama
                 * ===================================================== */
                DB::table('loker_user_transaksi')->insert([
                    'nik' => $nik,
                    'no_loker' => $noLoker,
                    'status' => 'IN',
                    'keterangan' => 'Karyawan Baru Join',
                    'nama_pengisi' => auth()->user()->name ?? '',
                    'tgl_pengisi' => now()->format('Y-m-d'),
                    'nik_pengisi' => auth()->user()->username ?? '',
                    'jam_pengisi' => now()->format('H:i:s'),
                    'pindah_to' => null,
                    'penghuni_sebelumnya' => $penghuniSebelumnya ?? null, 
                    'alasan' => 'Karyawan Baru Join',
                    'kode_area' => $request->kode_area ?? '',
                    'kode_blok' => $request->kode_blok ?? '',
                    'kode_rak' => $kodeRak ?? '',
                ]);


                /* =====================================================
                 * 6️⃣ Insert rak utama
                 * ===================================================== */
                DB::table('loker_penghuni')->insert([
                    'kode_rak' => $kodeRak,
                    'no_loker' => $noLoker,
                    'nik' => $nik,
                    'nama' => $nama,
                    'jk' => $jk,
                    'divisi' => $divisi,
                    'staff' => $staff,
                    'is_active' => 'Y',
                    'tgl_masuk' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /* =====================================================
                 * 7️⃣ Insert rak pasangan
                 * ===================================================== */
                if ($pairRak) {
                    DB::table('loker_penghuni')->insert([
                        'kode_rak' => $pairRak,
                        'no_loker' => $noLoker,
                        'nik' => $nik,
                        'nama' => $nama,
                        'jk' => $jk,
                        'divisi' => $divisi,
                        'staff' => $staff,
                        'is_active' => 'Y',
                        'tgl_masuk' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('loker_user_transaksi')->insert([
                        'nik' => $nik,
                        'no_loker' => $noLoker,
                        'status' => 'IN',
                        'keterangan' => 'Karyawan Baru Join',
                        'nama_pengisi' => auth()->user()->name ?? '',
                        'tgl_pengisi' => now()->format('Y-m-d'),
                        'nik_pengisi' => auth()->user()->username ?? '',
                        'jam_pengisi' => now()->format('H:i:s'),
                        'pindah_to' => null,
                        'penghuni_sebelumnya' => $penghuniSebelumnya ?? null,
                        'alasan' => 'Karyawan Baru Join',
                        'kode_area' => $request->kode_area ?? '',
                        'kode_blok' => $request->kode_blok ?? '',
                        'kode_rak' => $pairRak ?? '',
                    ]);
                }

                DB::table('hr_karyawan')
                    ->where('id', $idCard)
                    ->update(['in_complete' => 'Y']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Update Status Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses data.',
                ],
                500,
            );
        }
    }

    public function uploadExcel(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($req->hasFile('excel_file')) {
            $file = $req->file('excel_file');

            if (!$file->isValid()) {
                return response()->json(['message' => 'File tidak valid atau rusak.'], 400);
            }

            try {
                if ($file->getClientOriginalExtension() !== 'xlsx') {
                    return response()->json(['message' => 'Format file tidak valid. Hanya menerima file .xlsx.'], 400);
                }

                Excel::import(new GaShiftIn(), $file);

                return response()->json(['message' => 'Data berhasil diunggah dan diproses.'], 200);
            } catch (\Exception $e) {
                Log::error('Error during file upload: ' . $e->getMessage());

                return response()->json(['message' => 'Terjadi kesalahan saat mengimpor data. ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'File tidak ditemukan atau tidak valid.'], 400);
    }
}
