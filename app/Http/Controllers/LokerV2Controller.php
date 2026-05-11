<?php
namespace App\Http\Controllers;

use App\Exports\LokerExport;
use App\Http\Controllers\Controller;
use App\Imports\LokerImport;
use App\Imports\LokerSheetSelectorImport;
use App\Models\HR\Karyawan;
use App\Models\Loker\Rak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LokerV2Controller extends Controller
{
    /**
     * Helper untuk standarisasi Prefix (LP/LW)
     */
    private function getPrefix($gender)
    {
        $g = strtoupper($gender);
        return ($g == 'L' || $g == 'PRIA' || $g == 'LAKI-LAKI' || $g == 'LP') ? 'LP' : 'LW';
    }

    /**
     * Helper Kategori Karyawan menyesuaikan struktur ENUM ('staff','non_staff','mitra_kerja')
     */
    private function getKategoriKaryawan($karyawan)
    {
        if (! $karyawan) {
            return 'non_staff';
        }

        if (strtoupper($karyawan->staff ?? 'N') === 'Y') {
            return 'staff';
        }

        $divisi = strtoupper($karyawan->kode_divisi ?? '');
        $bagian = strtoupper($karyawan->kode_bagian ?? '');

        if (str_contains($divisi, 'MITRA') || str_contains($bagian, 'MITRA')) {
            return 'mitra_kerja';
        }

        return 'non_staff';
    }

    public function index()
    {
        $genders       = ['L' => 'Pria', 'P' => 'Wanita'];
        $dashboardData = [];
        $grandTotal    = ['total' => 0, 'penuh' => 0, 'tersedia' => 0, 'rusak' => 0];

        foreach ($genders as $code => $label) {
            $prefix  = ($code == 'L') ? 'LP' : 'LW';
            $lockers = Rak::where('kode_rak', $prefix)->get();

            $allPenghuni = DB::table('loker_penghuni')
                ->where('kode_rak', $prefix)
                ->where('is_active', 'Y')
                ->whereNull('tgl_keluar')
                ->get()
                ->groupBy('no_loker');

            $processed = $lockers->map(function ($rak) use ($allPenghuni) {
                $penghuniLoker = $allPenghuni->get($rak->no_loker) ?? collect();
                $count         = $penghuniLoker->count();

                $penghuniPertama = $penghuniLoker->first();
                $kategori        = $penghuniPertama ? strtolower(trim($penghuniPertama->kategori_karyawan)) : 'non_staff';

                $maxDisplay = ($kategori == 'staff') ? 1 : (int) $rak->kapasitas;
                $status     = ($count >= $maxDisplay) ? 'penuh' : 'tersedia';

                if ($rak->is_active == 'N') {
                    $status = 'rusak';
                }

                return [
                    'id'       => $rak->id,
                    'no'       => $rak->no_loker,
                    'count'    => $count,
                    'max'      => $maxDisplay,
                    'status'   => $status,
                    'gender'   => $rak->kode_rak,
                    'kategori' => $kategori,
                ];
            })->sortBy(function ($item) {
                return (int) $item['no'];
            });

            $stats = [
                'total'    => $processed->count(),
                'penuh'    => $processed->where('status', 'penuh')->count(),
                'tersedia' => $processed->where('status', 'tersedia')->count(),
                'rusak'    => $processed->where('status', 'rusak')->count(),
            ];

            foreach ($stats as $key => $val) {
                $grandTotal[$key] += $val;
            }

            $dashboardData[$label] = ['lockers' => $processed, 'stats' => $stats];
        }

        return view('loker.index', compact('dashboardData', 'grandTotal'));
    }

    public function management(Request $request)
    {
        $gender = $request->get('gender', 'tab_pria');
        $page   = $request->get('page', 1);

        $queryPria = DB::table('loker_rak')
            ->where('gender', 'L')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('loker_penghuni')
                    ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                    ->whereRaw('loker_penghuni.kode_rak = loker_rak.kode_rak')
                    ->where('loker_penghuni.is_active', 'Y');
            })
            ->orderBy('no_loker', 'ASC');

        $queryWanita = DB::table('loker_rak')
            ->where('gender', 'P')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('loker_penghuni')
                    ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                    ->whereRaw('loker_penghuni.kode_rak = loker_rak.kode_rak')
                    ->where('loker_penghuni.is_active', 'Y');
            })
            ->orderBy('no_loker', 'ASC');

        $query = ($gender == 'tab_pria') ? $queryPria : $queryWanita;
        $data  = $query->paginate(10);

        if ($data->isEmpty() && $data->lastPage() > 0 && $page > $data->lastPage()) {
            return redirect()->route('loker.management', [
                'page'   => $data->lastPage(),
                'gender' => $gender,
            ]);
        }

        if ($request->ajax()) {
            return view('loker.management.partials.table_management', compact('data', 'gender'))->render();
        }

        $lokerPria   = $queryPria->paginate(10);
        $lokerWanita = $queryWanita->paginate(10);

        return view('loker.management.index', compact('lokerPria', 'lokerWanita', 'gender'));
    }

    public function bulkAdd(Request $request)
    {
        $request->validate([
            'kode_rak' => 'required|in:LP,LW',
            'jumlah'   => 'required|integer|min:1|max:100',
        ]);

        $kodeRak    = $request->kode_rak;
        $gender     = ($kodeRak == 'LP') ? 'L' : 'P';
        $jumlahBaru = $request->jumlah;

        $existingNumbers = DB::table('loker_rak')
            ->where('kode_rak', $kodeRak)
            ->pluck('no_loker')
            ->toArray();

        DB::beginTransaction();
        try {

            $insertedCount = 0;
            $currentNumber = 1;
            $addedNumbers  = [];

            while ($insertedCount < $jumlahBaru) {
                if (! in_array($currentNumber, $existingNumbers)) {
                    DB::table('loker_rak')->insert([
                        'kode_rak'   => $kodeRak,
                        'no_loker'   => $currentNumber,
                        'gender'     => $gender,
                        'kapasitas'  => 2,
                        'is_active'  => 'Y',
                        'updated_at' => now(),
                    ]);

                    $addedNumbers[] = $currentNumber;
                    $insertedCount++;
                }

                $currentNumber++;

                if ($currentNumber > 10000) {
                    break;
                }
            }

            DB::commit();

            $infoRange = count($addedNumbers) > 0 ? "(" . min($addedNumbers) . " s/d " . max($addedNumbers) . ")" : "";

            return response()->json([
                'status'  => 'success',
                'message' => "Berhasil menambahkan $jumlahBaru data master loker $infoRange",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchKaryawan($search)
    {
        $search = trim((string) $search);

        $lokerAktif = DB::table('loker_penghuni')
            ->where('is_active', 'Y')
            ->where(function ($q) use ($search) {
                $q->whereRaw("CAST(nik AS UNSIGNED) = CAST(? AS UNSIGNED)", [$search])
                    ->orWhere('nama', 'LIKE', "%$search%");
            })->first();

        $dataPusat = null;
        try {
            $dataPusat = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK', 'EMPNM', 'DEPTID', 'CARDNODEVICE', 'RFID', 'FOTOBLOB', 'STATUS')
                ->where(function ($q) use ($search, $lokerAktif) {
                    $q->whereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$search])
                        ->orWhere('EMPNM', 'LIKE', "%$search%")
                        ->orWhere('CARDNODEVICE', $search)
                        ->orWhere('RFID', $search);

                    if ($lokerAktif) {
                        $q->orWhereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$lokerAktif->nik]);
                    }
                })
                ->where('STATUS', 'X')
                ->first();
        } catch (\Exception $e) {
            Log::error("Koneksi DB Pusat Gagal: " . $e->getMessage());
        }

        if (! $lokerAktif && ! $dataPusat) {
            return response()->json(['success' => false, 'message' => 'Verifikasi gagal: Data karyawan tidak ditemukan. Pastikan kartu terdaftar atau NIK valid.']);
        }

        $finalNIK = $dataPusat ? $dataPusat->NIK : ($lokerAktif->nik ?? $search);
        $nama     = $dataPusat->EMPNM ?? ($lokerAktif->nama ?? 'Tidak dikenali');
        $divisi   = $dataPusat->DEPTID ?? ($lokerAktif->divisi ?? '-');
        $kategori = $lokerAktif ? $lokerAktif->kategori_karyawan : $this->getKategoriKaryawan($dataPusat);

        $gender          = null;
        $isGenderEmpty   = true;
        $isCategoryEmpty = true;

        if ($lokerAktif) {
            $gender = ($lokerAktif->kode_rak == 'LP') ? 'L' : (($lokerAktif->kode_rak == 'LW') ? 'P' : null);
            if ($gender) {
                $isGenderEmpty = false;
            }
            if ($kategori) {
                $isCategoryEmpty = false;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'nik'               => $finalNIK,
                'nama'              => strtoupper($nama),
                'gender'            => $gender,
                'is_gender_empty'   => $isGenderEmpty,
                'is_category_empty' => $isCategoryEmpty,
                'kategori'          => $kategori,
                'divisi'            => strtoupper($divisi),
                'no_loker'          => $lokerAktif ? $lokerAktif->no_loker : null,
                'status_data'       => $lokerAktif ? 'Telah Dialokasikan' : 'Data Baru',
                'foto'              => ($dataPusat && $dataPusat->FOTOBLOB)
                    ? 'data:image/jpeg;base64,' . base64_encode($dataPusat->FOTOBLOB)
                    : null,
            ],
        ]);
    }

    public function searchGlobal(Request $request)
    {
        $keyword = trim($request->q);
        $gender  = $request->gender;

        $searchNIK = $keyword;

        try {
            $dataPusat = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK')
                ->where(function ($q) use ($keyword) {
                    $q->where('CARDNODEVICE', $keyword)
                        ->orWhereRaw('CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)', [$keyword])
                        ->orWhereRaw('CAST(BARCODE AS UNSIGNED) = CAST(? AS UNSIGNED)', [$keyword])
                        ->orWhere('RFID', $keyword);
                })
                ->where('STATUS', 'X')
                ->first();

            if ($dataPusat) {
                $searchNIK = $dataPusat->NIK;
            }
        } catch (\Exception $e) {
            Log::error("Koneksi DB Pusat Gagal: " . $e->getMessage());
        }

        $data = DB::table('loker_penghuni')
            ->where('is_active', 'Y')
            ->where(function ($q) use ($searchNIK, $keyword) {
                $q->whereRaw('CAST(nik AS UNSIGNED) = CAST(? AS UNSIGNED)', [$searchNIK])
                    ->orWhere('nama', 'LIKE', "%$keyword%");
            })
            ->select('no_loker', 'kode_rak', 'nama')
            ->first();

        if ($data) {
            $foundGender = ($data->kode_rak == 'LP') ? 'L' : 'P';

            if ($foundGender !== $gender) {
                return response()->json([
                    'success'      => true,
                    'is_wrong_tab' => true,
                    'no_loker'     => $data->no_loker,
                    'gender'       => $foundGender,
                    'message'      => "Karyawan a.n. {$data->nama} teralokasi di Loker Area " . ($foundGender == 'L' ? 'PRIA' : 'WANITA') . ". Silakan beralih ke tab yang sesuai.",
                ]);
            }

            return response()->json([
                'success'      => true,
                'is_wrong_tab' => false,
                'no_loker'     => $data->no_loker,
                'gender'       => $foundGender,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Data alokasi loker tidak ditemukan.']);
    }

    public function apiSuggestLoker(Request $request)
    {
        $prefix = $this->getPrefix($request->gender);

        $kategori = $request->kategori;

        $suggest = Rak::where('kode_rak', $prefix)
            ->where('is_active', 'Y')
            ->where(function ($q) {
                $q->whereNull('keterangan_kondisi')
                    ->orWhere('keterangan_kondisi', 'NOT LIKE', '%Pemeliharaan%')
                    ->orWhere('keterangan_kondisi', 'NOT LIKE', '%Rusak%');
            })
            ->where(function ($q) use ($kategori, $prefix) {
                if ($kategori === 'staff') {
                    $q->whereNotExists(function ($sq) use ($prefix) {
                        $sq->select(DB::raw(1))
                            ->from('loker_penghuni')
                            ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                            ->where('loker_penghuni.kode_rak', $prefix)
                            ->where('loker_penghuni.is_active', 'Y');
                    });
                } else {
                    $q->whereRaw("(SELECT COUNT(*) FROM loker_penghuni
                              WHERE no_loker = loker_rak.no_loker
                              AND kode_rak = ?
                              AND is_active = 'Y') < 2", [$prefix])
                        ->whereNotExists(function ($sq) use ($prefix) {
                            $sq->select(DB::raw(1))
                                ->from('loker_penghuni')
                                ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                                ->where('loker_penghuni.kode_rak', $prefix)
                                ->where('loker_penghuni.is_active', 'Y')
                                ->where('loker_penghuni.kategori_karyawan', 'staff');
                        });
                }
            })
            ->orderByRaw('CAST(no_loker AS UNSIGNED) ASC')
            ->first();

        return response()->json([
            'status'            => 'success',
            'rekomendasi_loker' => $suggest ? $suggest->no_loker : 'penuh',
        ]);
    }

    public function getAvailableLockers($gender, $kategori = 'non_staff')
    {
        $prefix = $this->getPrefix($gender);

        $available = DB::table('loker_rak')
            ->where('kode_rak', $prefix)
            ->where('is_active', 'Y')
            ->where(function ($q) use ($prefix, $kategori) {
                $subCount = "SELECT COUNT(*) FROM loker_penghuni
                         WHERE loker_penghuni.no_loker = loker_rak.no_loker
                         AND loker_penghuni.kode_rak = '$prefix'
                         AND loker_penghuni.is_active = 'Y'";

                $hasStaff = "SELECT COUNT(*) FROM loker_penghuni
                WHERE loker_penghuni.no_loker = loker_rak.no_loker
                AND loker_penghuni.kode_rak = '$prefix'
                AND loker_penghuni.is_active = 'Y'
                AND loker_penghuni.kategori_karyawan = 'staff'";

                if ($kategori == 'staff') {
                    $q->whereRaw("($subCount) = 0");
                } else {
                    $q->whereRaw("($subCount) < 2")
                        ->whereRaw("($hasStaff) = 0");
                }
            })
            ->orderByRaw('CAST(no_loker AS UNSIGNED) ASC')
            ->get(['no_loker']);

        return response()->json($available);
    }

    public function getDetailLoker($gender, $no_loker)
    {
        $prefix = $this->getPrefix($gender);

        $unit = DB::table('loker_rak')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $no_loker)
            ->first();

        $statusUnit = 'aktif';

        if ($unit && $unit->is_active === 'N') {
            $statusUnit = 'rusak';
        }

        $penghuni = DB::table('loker_penghuni')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $no_loker)
            ->whereNull('tgl_keluar')
            ->where('is_active', 'Y')
            ->orderBy('id', 'asc')
            ->select('id', 'nik', 'nama', 'divisi', 'kategori_karyawan', 'tgl_masuk')
            ->get()
            ->map(function ($item) {
                if (empty($item->divisi) || $item->divisi == '-') {
                    $hris = Karyawan::where('nik', $item->nik)->first();

                    if ($hris && ! empty($hris->kode_divisi)) {
                        $item->divisi = $hris->kode_divisi;
                    } else {
                        try {
                            $pusat = DB::connection('192.168.178.44-admin')
                                ->table('MSIDCARD')
                                ->where('NIK', $item->nik)
                                ->select('DEPTID')
                                ->first();

                            $item->divisi = $pusat->DEPTID ?? '-';
                        } catch (\Exception $e) {
                            $item->divisi = '-';
                        }
                    }
                }

                $item->nik       = $item->nik ?? '-';
                $item->nama      = $item->nama ?? '-';
                $item->kategori  = $item->kategori_karyawan ? str_replace('_', ' ', strtoupper($item->kategori_karyawan)) : '-';
                $item->tgl_masuk = $item->tgl_masuk ? date('d-m-Y', strtotime($item->tgl_masuk)) : '-';

                return $item;
            });

        return response()->json([
            'status_unit' => $statusUnit,
            'data'        => $penghuni,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'no_loker' => 'required',
            'status'   => 'required|in:aktif,rusak',
            'gender'   => 'required|in:L,P',
            'alasan'   => 'required_if:status,rusak|nullable|string|max:255',
        ]);

        $prefix  = $this->getPrefix($request->gender);
        $status  = $request->status;
        $noLoker = trim($request->no_loker);
        $tanggal = date('d-m-Y H:i');

        if ($status == 'rusak') {
            $keterangan = "DALAM PEMELIHARAAN";

            $alasanRaw = ucwords(strtolower($request->alasan));
            $catatan   = "Pemeliharaan ($alasanRaw - Dilaporkan: $tanggal)";

            DB::table('loker_rak')
                ->where('kode_rak', $prefix)
                ->where('no_loker', $noLoker)
                ->update([
                    'is_active'          => 'N',
                    'keterangan_kondisi' => $keterangan,
                    'catatan_admin'      => $catatan,
                    'updated_at'         => now(),
                ]);
        } else {
            DB::table('loker_rak')
                ->where('kode_rak', $prefix)
                ->where('no_loker', $noLoker)
                ->update([
                    'is_active'  => 'Y',
                    'updated_at' => now(),
                ]);

            $this->updateKeteranganRak($prefix, $noLoker);
        }

        return response()->json(['status' => 'success', 'message' => $status == 'rusak' ? 'Loker berhasil ditandai dalam status pemeliharaan.' : 'Loker telah diaktifkan dan siap dialokasikan.']);
    }

    public function tarikKunci(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'alasan' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $p = DB::table('loker_penghuni')->where('id', $request->id)->first();
            if (! $p) {
                return response()->json(['status' => 'error', 'message' => 'Data alokasi tidak ditemukan.'], 422);
            }

            DB::table('loker_penghuni')
                ->where('id', $request->id)
                ->update([
                    'is_active'  => 'N',
                    'tgl_keluar' => now(),
                    'updated_at' => now(),
                ]);

            $sisaPenghuni = DB::table('loker_penghuni')
                ->where('no_loker', $p->no_loker)
                ->where('kode_rak', $p->kode_rak)
                ->where('is_active', 'Y')
                ->exists();

            $alasan  = ucwords($request->alasan);
            $tanggal = date('d-m-Y');

            if (! $sisaPenghuni) {
                DB::table('loker_rak')
                    ->where('kode_rak', $p->kode_rak)
                    ->where('no_loker', $p->no_loker)
                    ->update([
                        'keterangan_kondisi' => 'Pengosongan Loker: ' . $alasan,
                        'catatan_admin'      => "Fasilitas dicabut pada: $tanggal",
                        'updated_at'         => now(),
                    ]);
            } else {
                DB::table('loker_rak')
                    ->where('kode_rak', $p->kode_rak)
                    ->where('no_loker', $p->no_loker)
                    ->update([
                        'catatan_admin' => "Pencabutan sebagian fasilitas ($alasan) - $tanggal",
                        'updated_at'    => now(),
                    ]);
            }

            // KEMBALI KE ENUM: 'KELUAR'
            DB::table('loker_transaksi')->insert([
                'nik'            => $p->nik ?? '-',
                'nama'           => ucwords(strtolower($p->nama)),
                'kode_rak'       => $p->kode_rak,
                'no_loker'       => $p->no_loker,
                'tipe_transaksi' => 'KELUAR',
                'operator'       => auth()->user()->name ?? 'Sistem',
                'keterangan'     => "Pencabutan Fasilitas: " . $alasan, // Penjelasan masuk sini
                'created_at'     => now(),
            ]);

            return response()->json(['status' => 'success']);
        });
    }

    private function updateKeteranganRak($prefix, $noLoker)
    {
        $allPenghuni = DB::table('loker_penghuni')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $noLoker)
            ->where('is_active', 'Y')
            ->orderBy('id', 'asc')
            ->get();

        if ($allPenghuni->isEmpty()) {
            $keterangan = "Tersedia";
            $catatan    = null;
        } else {
            $keterangan = "Telah Dialokasikan";
            $catatan    = null;
        }

        DB::table('loker_rak')
            ->where('kode_rak', $prefix)
            ->where('no_loker', $noLoker)
            ->update([
                'keterangan_kondisi' => $keterangan,
                'catatan_admin'      => $catatan,
                'updated_at'         => now(),
            ]);
    }

    public function store(Request $request)
    {
        $this->permission('loker_operator');

        $request->validate([
            'nik'      => 'required',
            'no_loker' => 'required',
            'gender'   => 'required',
        ]);

        $nikInput = trim((string) $request->nik);
        $prefix   = $this->getPrefix($request->gender);
        $operator = auth()->user()->name ?? 'Sistem';

        $dataExternal = null;
        try {
            $dataExternal = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK', 'EMPNM', 'DEPTID', 'TYPECARD')
                ->whereRaw("CAST(NIK AS UNSIGNED) = CAST(? AS UNSIGNED)", [$nikInput])
                ->where('STATUS', 'X')
                ->first();
        } catch (\Exception $e) {
            Log::error("Koneksi DB Pusat Gagal: " . $e->getMessage());
        }

        if ($dataExternal) {
            $nikFix       = $dataExternal->NIK;
            $namaKaryawan = $dataExternal->EMPNM;
            $divisi       = $dataExternal->DEPTID;
            $kategori     = $request->kategori_karyawan ?? (($dataExternal->TYPECARD == 1) ? 'mitra_kerja' : 'non_staff');
        } elseif ($request->nama) {
            $nikFix       = $nikInput;
            $namaKaryawan = $request->nama;
            $divisi       = $request->dept;
            $kategori     = $request->kategori_karyawan ?? 'non_staff';
        } else {
            return response()->json(['status' => 'error', 'message' => "Verifikasi gagal: Data karyawan tidak ditemukan."], 422);
        }

        return DB::transaction(function () use ($request, $nikFix, $namaKaryawan, $divisi, $kategori, $prefix, $operator) {

            $kondisiRak = DB::table('loker_rak')
                ->where(['kode_rak' => $prefix, 'no_loker' => $request->no_loker])
                ->first();

            if ($kondisiRak && $kondisiRak->is_active === 'N') {
                return response()->json(['status' => 'error', 'message' => "Penempatan gagal! Loker nomor {$request->no_loker} berstatus: " . ($kondisiRak->keterangan_kondisi ?? 'DALAM PEMELIHARAAN')], 422);
            }

            if ($kategori === 'staff') {
                $isOccupied = DB::table('loker_penghuni')
                    ->where(['kode_rak' => $prefix, 'no_loker' => $request->no_loker, 'is_active' => 'Y'])
                    ->where('nik', '!=', $nikFix)
                    ->exists();

                if ($isOccupied) {
                    return response()->json(['status' => 'error', 'message' => 'Kapasitas loker khusus Staff telah terpenuhi!'], 422);
                }
            }

            $lokerLama = DB::table('loker_penghuni')
                ->where('is_active', 'Y')
                ->whereRaw("CAST(nik AS UNSIGNED) = CAST(? AS UNSIGNED)", [$nikFix])
                ->first();

            if ($lokerLama) {
                DB::table('loker_penghuni')->where('id', $lokerLama->id)->update([
                    'is_active'  => 'N',
                    'tgl_keluar' => now(),
                    'updated_at' => now(),
                ]);

                // KEMBALI KE ENUM: 'PINDAH'
                DB::table('loker_transaksi')->insert([
                    'nik'            => $lokerLama->nik,
                    'nama'           => $namaKaryawan,
                    'kode_rak'       => $lokerLama->kode_rak,
                    'no_loker'       => $lokerLama->no_loker,
                    'tipe_transaksi' => 'PINDAH',
                    'operator'       => $operator,
                    'keterangan'     => "Relokasi ke area {$prefix}-{$request->no_loker}", // Penjelasan masuk sini
                    'created_at' => now(),
                ]);

                $this->updateKeteranganRak($lokerLama->kode_rak, $lokerLama->no_loker);
            }

            DB::table('loker_penghuni')->insert([
                'nik'               => $nikFix,
                'nama'              => strtoupper($namaKaryawan),
                'divisi'            => strtoupper($divisi),
                'kode_rak'          => $prefix,
                'no_loker'          => $request->no_loker,
                'kategori_karyawan' => $kategori,
                'tgl_masuk'         => now(),
                'is_active'         => 'Y',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $this->updateKeteranganRak($prefix, $request->no_loker);

            // KEMBALI KE ENUM: 'MASUK'
            DB::table('loker_transaksi')->insert([
                'nik'            => $nikFix,
                'nama'           => $namaKaryawan,
                'kode_rak'       => $prefix,
                'no_loker'       => $request->no_loker,
                'tipe_transaksi' => 'MASUK',
                'operator'       => $operator,
                'keterangan'     => 'Alokasi Penempatan Baru', // Penjelasan tambahan
                'created_at'     => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Alokasi penempatan berhasil disimpan.']);
        });
    }

    public function import(Request $request)
    {
        $request->validate([
            'file'   => 'required|mimes:xlsx,xls',
            'gender' => 'required|in:L,P',
        ]);

        try {
            $gender = $request->gender;
            $prefix = ($gender == 'L') ? 'LP' : 'LW';

            $filePath = $request->file('file')->path();

            $spreadsheet = IOFactory::load($filePath);

            $sheetNames = $spreadsheet->getSheetNames();

            $sheetIndex = 0;

            if (count($sheetNames) > 1 && $gender == 'P') {
                $sheetIndex = 1;
            }

            DB::transaction(function () use ($request, $gender, $prefix, $sheetIndex) {
                DB::table('loker_penghuni')->where('kode_rak', $prefix)->delete();

                $importInstance = new LokerImport($gender);
                $selector       = new LokerSheetSelectorImport($sheetIndex, $importInstance);

                Excel::import($selector, $request->file('file'));

                $allLockers = DB::table('loker_rak')
                    ->where('kode_rak', $prefix)
                    ->pluck('no_loker');

                foreach ($allLockers as $noLoker) {
                    $this->updateKeteranganRak($prefix, $noLoker);
                }
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Proses unggah berhasil! Data alokasi loker telah disinkronisasi.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function export($gender)
    {
        $namaFile = 'Loker_' . ($gender == 'L' ? 'Pria' : 'Wanita') . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new LokerExport($gender), $namaFile);
    }

    public function getFoto($nik)
    {
        $imageData = Cache::remember("foto_karyawan_{$nik}", 3600, function () use ($nik) {
            try {
                $user = DB::connection('192.168.178.44-admin')->table('MSIDCARD')
                    ->select('FOTOBLOB')->whereRaw('CAST(BARCODE AS SIGNED) = ?', [trim($nik)])->first();
                return ($user && $user->FOTOBLOB) ? 'data:image/jpeg;base64,' . base64_encode($user->FOTOBLOB) : null;
            } catch (\Throwable $e) {
                return 'error';
            }
        });
        return response()->json(['success' => ($imageData && $imageData !== 'error'), 'image' => $imageData]);
    }

    public function destroy(Request $request, $id)
    {
        $loker = DB::table('loker_rak')->where('id', $id)->first();

        if (! $loker) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data master loker tidak ditemukan.',
            ], 404);
        }

        $isOccupied = DB::table('loker_penghuni')
            ->where('no_loker', $loker->no_loker)
            ->where('kode_rak', $loker->kode_rak)
            ->exists();

        if ($isOccupied) {
            return response()->json([
                'status'  => 'error',
                'message' => "Loker {$loker->no_loker} gagal dihapus karena masih memiliki data alokasi karyawan aktif.",
            ], 422);
        }

        $gender = $loker->gender;

        DB::table('loker_rak')->where('id', $id)->delete();

        $data = DB::table('loker_rak')
            ->where('gender', $gender)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('loker_penghuni')
                    ->whereRaw('loker_penghuni.no_loker = loker_rak.no_loker')
                    ->whereRaw('loker_penghuni.kode_rak = loker_rak.kode_rak');
            })->paginate(10);

        $html = view('loker.management.partials.table_management', [
            'data'   => $data,
            'gender' => $gender,
        ])->render();

        return response()->json([
            'status'  => 'success',
            'message' => "Data loker {$loker->no_loker} berhasil dihapus dari sistem.",
            'newTotal' => $data->total(),
            'html'     => $html,
            'gender'   => $gender,
        ]);
    }
}
