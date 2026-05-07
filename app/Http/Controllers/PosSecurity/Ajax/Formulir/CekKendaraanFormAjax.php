<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Formulir;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaCekKendaraan;
use App\Models\PosSecurity\GaVisitorTransaction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CekKendaraanFormAjax extends Controller
{
    public function searchIn(Request $request)
    {
        $keyword = strtoupper(str_replace(' ', '', $request->input('keyword')));

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor polisi wajib diisi.'
            ], 422);
        }

        // cari kendaraan yang masih ada di area di table supplier
        $visitor = DB::table('ga_visitor_transaction')
            ->whereRaw("REPLACE(UPPER(nopol),' ','') = ?", [$keyword])
            ->where('keterangan', 'SUPIR')
            ->orderBy('created_at', 'desc')
            ->first();

        // jika tidak ada -> cari di table vendor
        if (!$visitor) {
            $visitor = DB::table('ga_visitor_vendor')
                ->whereRaw("REPLACE(UPPER(nopol),' ','') = ?", [$keyword])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Data kendaraan tidak ditemukan atau sudah keluar.'
            ], 404);
        }

        // cek apakah visitor sudah keluar
        if ($visitor->kartu_dikembalikan == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tamu sudah mengambalikan kartu, tidak dapat cek kendaraan.'
            ], 409);
        }

        // validasi apakah sudah cek kendaraan pada kedatangan ini
        $alreadyChecked = DB::table('ga_cek_kendaraan')
            ->where('nomor_polisi', $visitor->nopol)
            // ->where('checked_in_at', '>=', now()->subHours(24))
            ->where('checked_in_at', '>=', $visitor->created_at)
            ->exists();

        if ($alreadyChecked) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan ini sudah melakukan cek kendaraan pada kedatangan ini.'
            ], 409);
        }

        return response()->json([
            'success' => true,
            'data' => $visitor
        ]);
    }

    public function searchOut(Request $request)
    {
        $keyword = strtoupper(str_replace(' ', '', $request->input('keyword')));

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor polisi wajib diisi.'
            ], 422);
        }

        $visitor = DB::table('ga_visitor_transaction')
            ->whereRaw("REPLACE(UPPER(nopol),' ','') = ?", [$keyword])
            ->where('keterangan', 'SUPIR')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$visitor) {
            $visitor = DB::table('ga_visitor_vendor')
                ->whereRaw("REPLACE(UPPER(nopol),' ','') = ?", [$keyword])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$visitor) {
            return response()->json([
                'success' => false,
                'message' => 'Data kendaraan tidak ditemukan atau sudah keluar.'
            ], 404);
        }

        if ($visitor->kartu_dikembalikan == 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tamu sudah mengembalikan kartu.'
            ], 409);
        }

        $cekKendaraan = DB::table('ga_cek_kendaraan')
            ->whereRaw("REPLACE(UPPER(nomor_polisi),' ','') = ?", [$keyword])
            ->where('checked_in_at', '>=', $visitor->created_at)
            ->orderBy('checked_in_at', 'desc')
            ->first();

        if (!$cekKendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan belum melakukan pengecekan masuk.'
            ], 409);
        }

        if ($cekKendaraan->checked_out_at) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan sudah melakukan pengecekan keluar.'
            ], 409);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'visitor' => $visitor,
                'cek_kendaraan' => $cekKendaraan
            ]
        ]);
    }

    // in
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trnvisitorid'  => 'required',
            'nama_supir'    => 'required|string|max:100',
            'company'       => 'required|string|max:100',
            'nomor_polisi'  => 'required|string|max:50',
            'nama_petugas'  => 'required|string|max:100',
            'muatan_type'   => 'required|string|max:50',
            'truck_type'    => 'required|string|max:50',
            'otherTruckType' => 'nullable|string|max:50',
            'photos'        => 'required',
            // Validasi foto identitas wajib ada
            'photos.foto_diri' => 'required',
        ], [
            'nama_supir.required'       => 'Nama supir harus diisi',
            'company.required'          => 'Nama perusahaan harus diisi',
            'nomor_polisi.required'     => 'Nomor polisi wajib diisi',
            'nama_petugas.required'     => 'Nama petugas wajib diisi',
            'muatan_type.required'      => 'Jenis muatan wajib diisi',
            'truck_type.required'       => 'Jenis truk wajib diisi',
            'photos.required'           => 'Foto kendaraan tidak terdeteksi',
            'photos.foto_diri.required' => 'Foto diri supir wajib diambil',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $alreadyChecked = DB::table('ga_cek_kendaraan')
            ->where('trnvisitorid', $request->trnvisitorid)
                ->whereNotNull('checked_in_at')
                ->exists();

            if ($alreadyChecked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan ini sudah melakukan cek kendaraan masuk.'
                ], 409);
            }

            $now = now();
            $trnCekId = 'CK-' . $now->format('YmdHis');
            $otherTruckType = trim((string) $request->otherTruckType);

            $photoPaths = [];

            if ($request->filled('photos') && is_array($request->photos)) {
                $decodedPhotos = [];

                foreach ($request->photos as $key => $value) {
                    $decodedPhotos[$key] = json_decode($value, true) ?? [];
                }

                $photoPaths = $this->saveBase64Images(
                    $decodedPhotos,
                    $trnCekId,
                    'MASUK'
                );
            }

            // ── Pisahkan foto identitas dari foto kendaraan ──────────────
            //
            // foto_ktp  → imgvisitorpathin di ga_visitor_transaction (string path)
            // foto_diri → foto             di ga_visitor_transaction (json array)
            //
            // Keduanya TIDAK disimpan ke foto_in di ga_cek_kendaraan supaya
            // foto_in hanya berisi foto pemeriksaan kendaraan.

            $selfPaths  = $photoPaths['foto_diri'] ?? [];

            // Hapus foto identitas dari $photoPaths agar tidak masuk foto_in
            unset($photoPaths['foto_diri']);

            // ── Insert ke ga_cek_kendaraan ───────────────────────────────
            $data = [
                'trncekid'           => $trnCekId,
                'nama_supir'         => strtoupper($request->nama_supir),
                'company'            => strtoupper($request->company),
                'nomor_polisi'       => strtoupper($request->nomor_polisi),
                'nama_petugas_masuk' => strtoupper($request->nama_petugas),
                'muatan_type'        => $request->muatan_type,
                'truck_type'         => $request->truck_type,
                'truck_type_other'   => $otherTruckType !== ''
                    ? strtoupper($otherTruckType)
                    : null,
                'foto_in'            => json_encode($photoPaths), // hanya foto kendaraan
                'checked_in_at'      => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
                'trnvisitorid'       => $request->trnvisitorid,
            ];

            DB::table('ga_cek_kendaraan')->insert($data);

            // ── Update foto identitas ke ga_visitor_transaction ──────────
            //
            // imgvisitorpathin : path foto KTP (string, ambil index 0)
            // foto             : JSON array path foto selfie
            //
            // Hanya diupdate jika foto berhasil disimpan.

            $visitorUpdate = [];

            if (!empty($ktpPaths)) {
                // imgvisitorpathin menyimpan satu path string — full URL
                $visitorUpdate['imgvisitorpathin'] = asset($ktpPaths[0]);
            }

            if (!empty($selfPaths)) {
                // foto menyimpan JSON array full URL (konsisten dengan store visitor asli)
                $visitorUpdate['foto'] = json_encode(
                    array_map(fn ($p) => asset($p), $selfPaths)
                );
            }

            if (!empty($visitorUpdate)) {
                $visitorUpdate['updated_at'] = now();

                // Coba update di ga_visitor_transaction
                $updated = DB::table('ga_visitor_transaction')
                    ->where('trnvisitorid', $request->trnvisitorid)
                    ->update($visitorUpdate);

                // Jika tidak ada yang diupdate (0 row affected), coba update di ga_visitor_vendor
                if (!$updated) {
                    DB::table('ga_visitor_vendor')
                        ->where('trnvisitorid', $request->trnvisitorid)
                        ->update($visitorUpdate);
                }
            }
            // ────────────────────────────────────────────────────────────

            return response()->json([
                'success' => true,
                'message' => 'Data & foto pengecekan kendaraan berhasil disimpan',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving cek kendaraan data: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }


    // out
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trncekid'      => 'required|exists:ga_cek_kendaraan,trncekid',
            'nama_petugas'  => 'required|string|max:100',
            'photos'   => 'required'

        ], [
            'trncekid.required' => 'Data cek kendaraan tidak valid',
            'nama_petugas.required'     => 'Nama petugas wajib diisi',
            'photos.required' => 'Foto kendaraan tidak terdeteksi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $cek = DB::table('ga_cek_kendaraan')
                ->where('trncekid', $request->trncekid)
                ->first();

            if (!$cek) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kendaraan tidak ditemukan.'
                ], 404);
            }

            if (!$cek->checked_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan ini belum melakukan cek kendaraan masuk.'
                ], 409);
            }

            if ($cek->checked_out_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan ini sudah melakukan cek kendaraan keluar.'
                ], 409);
            }

            $photoPaths = [];

            // if ($request->has('photos')) {
            //     $photoPaths = $this->saveBase64Images(
            //         $request->photos,
            //         $request->trncekid
            //         'KELUAR'
            //     );
            // }

            if ($request->filled('photos') && is_array($request->photos)) {
                $decodedPhotos = [];

                foreach ($request->photos as $key => $value) {
                    $decodedPhotos[$key] = json_decode($value, true) ?? [];
                }

                $photoPaths = $this->saveBase64Images(
                    $decodedPhotos,
                    $request->trncekid,
                    'KELUAR'
                );
            }

            $updateData = [
                'nama_petugas_keluar' => strtoupper($request->nama_petugas),
                'foto_out'         => json_encode($photoPaths),
                'checked_out_at'   => now(),
                'updated_at'       => now(),
            ];

            DB::table('ga_cek_kendaraan')
                ->where('trncekid', $request->trncekid)
                ->update($updateData);

            // ────────────────────────────────────────────────────────────

            return response()->json([
                'success' => true,
                'message' => 'Data pengecekan kendaraan keluar berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving cek kendaraan keluar data: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $trncekid = $request->id;

        $data = GaCekKendaraan::query()
            ->where('trncekid', $trncekid)
            ->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengecekan kendaraan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    private function saveBase64Images(
        $photos,
        $trnCekId,
        $status // MASUK / KELUAR
    ) {
        $photoPaths = [];

        foreach ($photos as $key => $images) {
            if (!is_array($images)) continue;

            // sanitasi nama kategori
            $label = preg_replace('/[^A-Za-z0-9_-]/', '_', $key);

            foreach ($images as $index => $base64Image) {
                if (!$base64Image) continue;

                if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    continue;
                }

                $extension = strtolower($type[1]);

                $imageData = base64_decode(
                    substr($base64Image, strpos($base64Image, ',') + 1)
                );

                if ($imageData === false) continue;

                // tanggal folder 
                $year  = now()->format('Y');
                $month = now()->format('m');
                $day   = now()->format('d');

                $fileName = "{$index}.{$extension}";

                $relativePath = implode('/', [
                    'uploads/pos-security/cek-kendaraan',
                    $year,
                    $month,
                    $day,
                    $trnCekId,
                    $status,
                    $label
                ]);

                $fullPath = public_path($relativePath);

                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                }

                File::put($fullPath . '/' . $fileName, $imageData);

                $photoPaths[$key][] = $relativePath . '/' . $fileName;
            }
        }

        return $photoPaths;
    }
}
