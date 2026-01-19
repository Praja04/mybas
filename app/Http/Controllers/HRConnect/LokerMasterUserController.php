<?php

namespace App\Http\Controllers\HRConnect;

use App\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LokerMasterUserController extends Controller
{
    public function index()
    {
        $departments = Department::where('status', '1')->get();
        return view('hr-connect.masters.user', compact('departments'));
    }

    public function getData(Request $request)
    {
        $query = DB::table('loker_penghuni')
            ->select(
                'nik',
                'nama',
                'divisi',
                'jk',
                'staff',
                DB::raw("
                MAX(CASE
                    WHEN kode_rak IN ('PB','WB')
                    THEN CONCAT(kode_rak, '-', no_loker)
                END) AS loker_baju
            "),
                DB::raw("
                MAX(CASE
                    WHEN kode_rak IN ('PS','WS')
                    THEN CONCAT(kode_rak, '-', no_loker)
                END) AS loker_sepatu
            "),
            )
            ->where('is_active', 'Y')
            ->groupBy('nik', 'nama', 'divisi', 'jk', 'staff');

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-success btnEdit" data-nik="' .
                    $row->nik .
                    '">Edit</button>
                <button class="btn btn-sm btn-danger btnDelete" data-nik="' .
                    $row->nik .
                    '">Delete</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'divisi' => 'required',
            'jk' => 'required|in:L,P',
            'no_loker' => 'required|integer',
            'staff' => 'required',
        ]);

        // Tentukan rak utama berdasarkan jenis kelamin
        if ($request->jk === 'L') {
            $kodeRak = 'PB';
        } elseif ($request->jk === 'P') {
            $kodeRak = 'WB';
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Jenis kelamin tidak valid.',
                ],
                400,
            );
        }
        // Tentukan rak pasangan
        $pairRak = $this->pairRak($kodeRak);

        try {
            DB::transaction(function () use ($request, $kodeRak, $pairRak) {
                // Simpan loker utama
                DB::table('loker_penghuni')->insert([
                    'nik' => $request->nik,
                    'nama' => $request->nama,
                    'divisi' => $request->divisi,
                    'jk' => $request->jk,
                    'kode_rak' => $kodeRak,
                    'no_loker' => (int) $request->no_loker,
                    'staff' => $request->staff,
                    'is_active' => 'Y',
                    'tgl_masuk' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Simpan rak pasangan (jika ada)
                if ($pairRak) {
                    DB::table('loker_penghuni')->insert([
                        'nik' => $request->nik,
                        'nama' => $request->nama,
                        'divisi' => $request->divisi,
                        'jk' => $request->jk,
                        'kode_rak' => $pairRak,
                        'no_loker' => (int) $request->no_loker,
                        'staff' => $request->staff,
                        'is_active' => 'Y',
                        'tgl_masuk' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data loker penghuni berhasil ditambahkan',
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Gagal menyimpan loker . ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function show($id)
    {
        $data = DB::table('loker_penghuni')->where('id', $id)->first();

        if (!$data) {
            return response()->json(
                [
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        return response()->json($data);
    }

    public function getByNik($nik)
    {
        $data = DB::table('loker_penghuni')->where('nik', $nik)->first();

        if (!$data) {
            return response()->json(
                [
                    'message' => 'Data tidak ditemukan',
                ],
                404,
            );
        }

        return response()->json($data);
    }

    public function update(Request $request, string $nik)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'divisi' => 'required',
            'jk' => 'required|in:L,P',
            'no_loker' => 'required|integer|min:1',
            'staff' => 'required',
        ]);

        // Rak baru
        $kodeRakUtama = $request->jk === 'L' ? 'PB' : 'WB';
        $kodeRakPair = $this->pairRak($kodeRakUtama);

        DB::transaction(function () use ($request, $kodeRakUtama, $kodeRakPair) {
            // 1️⃣ HAPUS SEMUA LOKER AKTIF LAMA (UTAMA + PASANGAN)
            DB::table('loker_penghuni')->where('nik', $request->nik)->where('is_active', 'Y')->delete();

            // 2️⃣ CREATE RAK UTAMA BARU
            DB::table('loker_penghuni')->insert([
                'nik' => $request->nik,
                'nama' => $request->nama,
                'divisi' => $request->divisi,
                'jk' => $request->jk,
                'staff' => $request->staff,
                'kode_rak' => $kodeRakUtama,
                'no_loker' => $request->no_loker,
                'is_active' => 'Y',
                'tgl_masuk' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3️⃣ CREATE RAK PASANGAN
            if ($kodeRakPair) {
                DB::table('loker_penghuni')->insert([
                    'nik' => $request->nik,
                    'nama' => $request->nama,
                    'divisi' => $request->divisi,
                    'jk' => $request->jk,
                    'staff' => $request->staff,
                    'kode_rak' => $kodeRakPair,
                    'no_loker' => $request->no_loker,
                    'is_active' => 'Y',
                    'tgl_masuk' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data loker user berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        DB::table('loker_penghuni')->where('id', $id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data loker user berhasil dihapus',
        ]);
    }

    public function destroyByNik(string $nik)
    {
        DB::transaction(function () use ($nik) {
            DB::table('loker_penghuni')->where('nik', $nik)->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Semua loker user berhasil dihapus',
        ]);
    }

    private function pairRak($kodeRak)
    {
        $map = [
            'PB' => 'PS',
            'WB' => 'WS',
        ];

        return $map[$kodeRak] ?? null;
    }
}
