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
            // 'foto_in'   => 'required|array|min:1',
        ], [
            'nama_supir.required'   => 'Nama supir harus diisi',
            'company.required'      => 'Nama perusahaan harus diisi',
            'nomor_polisi.required' => 'Nomor polisi wajib diisi',
            'nama_petugas.required' => 'Nama petugas wajib diisi',
            'muatan_type.required'  => 'Jenis muatan wajib diisi',
            'truck_type.required'   => 'Jenis truk wajib diisi',
            // 'foto_in.required' => 'Minimal 1 foto kendaraan wajib diambil',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $now = now();
            $trnCekId = 'CK-' . $now->format('YmdHis');
            $otherTruckType = trim((string) $request->otherTruckType);


            $photoPaths = [];

            if ($request->has('photos')) {
                foreach ($request->photos as $key => $base64Image) {
                    if (!$base64Image) continue;

                    if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                        continue;
                    }

                    $extension = strtolower($type[1]);
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $imageData = base64_decode($base64Image);

                    if ($imageData === false) continue;

                    $nopolClean = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($request->nomor_polisi));

                    $tanggal   = now()->format('Y-m-d');
                    $status    = 'MASUK';
                    $timestamp = now()->format('Ymd_His');

                    $label = preg_replace('/[^A-Za-z0-9_-]/', '_', $key);

                    $fileName = $timestamp . '.' . $extension;

                    $path = implode('/', [
                        'cek-kendaraan',
                        $tanggal,
                        $status,
                        $nopolClean,
                        $label,
                        $fileName
                    ]);

                    Storage::disk('public')->put($path, $imageData);

                    $photoPaths[$key][] = $path;
                }
            }

            $data = [
                'trncekid'      => $trnCekId,
                'nama_supir'    => strtoupper($request->nama_supir),
                'company'       => strtoupper($request->company),
                'nomor_polisi'  => strtoupper($request->nomor_polisi),
                'nama_petugas_masuk'  => strtoupper($request->nama_petugas),
                'muatan_type'   => $request->muatan_type,
                'truck_type'    => $request->truck_type,
                'truck_type_other' => $otherTruckType !== ''
                    ? strtoupper($otherTruckType)
                    : null,
                'foto_in'       => json_encode($photoPaths),
                'checked_in_at'    => now(),
                'created_at'    => now(),
                'updated_at'    => now(),
                'trnvisitorid'  => $request->trnvisitorid
            ];

            DB::table('ga_cek_kendaraan')->insert($data);

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
            // 'foto_out'   => 'required|array|min:1',

        ], [
            'trncekid.required' => 'Data cek kendaraan tidak valid',
            'nama_petugas.required'     => 'Nama petugas wajib diisi',
            // 'foto_out.required' => 'Minimal 1 foto kendaraan wajib diambil',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
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
                    'message' => 'Data cek kendaraan tidak ditemukan'
                ], 404);
            }

            if ($cek->checked_out_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan sudah keluar pada kunjungan saat ini'
                ], 409);
            }

            $photoPaths = [];

            if ($request->has('photos')) {
                foreach ($request->photos as $key => $base64Image) {
                    if (!$base64Image) continue;

                    if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                        continue;
                    }

                    $extension = strtolower($type[1]);
                    $imageData = base64_decode(
                        substr($base64Image, strpos($base64Image, ',') + 1)
                    );

                    if ($imageData === false) continue;

                    $nopolClean = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($cek->nomor_polisi));

                    $tanggal   = now()->format('Y-m-d');
                    $status    = 'KELUAR';
                    $timestamp = now()->format('Ymd_His');

                    $label = preg_replace('/[^A-Za-z0-9_-]/', '_', $key);


                    $fileName = $timestamp . '.' . $extension;

                    $path = implode('/', [
                        'cek-kendaraan',
                        $tanggal,
                        $status,
                        $nopolClean,
                        $label,
                        $fileName
                    ]);

                    Storage::disk('public')->put($path, $imageData);

                    $photoPaths[$key][] = $path;
                }
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

    // 
    public function kembali_kartu(Request $request)
    {
        $request->validate([
            'trnvisitorid' => 'required|string|max:50'
        ]);

        try {
            $visitor = GaVisitorTransaction::where('trnvisitorid', $request->trnvisitorid)->first();

            if (!$visitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visitor tidak ditemukan.'
                ]);
            }

            $now = now();

            $visitor->kartu_dikembalikan = true;
            $visitor->gateidout = $visitor->gateidin;
            $visitor->gatelineidout = $visitor->gatelineidin;
            $visitor->dateout = $now->toDateString(); // YYYY-MM-DD
            $visitor->timeout = $now->format('H:i:s'); // HH:MM:SS
            $visitor->changedon = $now;
            $visitor->changedby = auth()->user()->username ?? 'system'; // atau session user
            $visitor->save();

            return response()->json([
                'success' => true,
                'message' => 'Kartu berhasil dikembalikan untuk visitor ID: ' . $visitor->trnvisitorid
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengembalikan kartu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengembalikan kartu.'
            ]);
        }
    }

    /**
     * Generate unique visitor ID
     */
    private function generateVisitorId($prefix)
    {
        $date = date('Ymd');

        // Get last visitor ID for today by prefix
        $lastVisitor = GaVisitorTransaction::where('trnvisitorid', 'LIKE', $prefix . $date . '%')
            ->orderBy('trnvisitorid', 'desc')
            ->first();

        if ($lastVisitor) {
            $lastNumber = (int) substr($lastVisitor->trnvisitorid, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }


    private function saveImageFromBase64($base64Image, $filename, $folder = 'suppliers')
    {
        // Bersihkan base64 prefix jika ada
        if (strpos($base64Image, 'base64,') !== false) {
            $base64Image = explode('base64,', $base64Image)[1];
        }

        // Decode
        $imageData = base64_decode($base64Image);

        // Buat nama file (png misalnya)
        $fileName = $filename . '.png';

        // Folder + tanggal (misal pakai per hari)
        $datePath = now()->format('Y/m/d');
        $savePath = "{$folder}/{$datePath}/{$fileName}";

        // Simpan ke storage public
        Storage::disk('public')->put($savePath, $imageData);

        // Return URL public
        return url('/storage/' . $savePath);
    }


    /**
     * Generate QR Code data
     */
    private function generateQRCode($visitorId, $namaVisitor)
    {
        $qrData = [
            'visitor_id' => $visitorId,
            'nama' => $namaVisitor,
            'timestamp' => time(),
            'status' => 'active'
        ];

        return base64_encode(json_encode($qrData));
    }


    /**
     * Show cek kendaraan detail
     */
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

    /**
     * Update visitor checkout
     */
    public function checkoutOld(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'gateidout' => 'required|string|max:20',
            'gatelineidout' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $visitor = GaVisitorTransaction::findOrFail($id);

            if ($visitor->dateout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visitor sudah checkout sebelumnya'
                ], 400);
            }

            $now = Carbon::now();

            $updateData = [
                'gateidout' => $request->gateidout,
                'gatelineidout' => $request->gatelineidout,
                'dateout' => $now->format('Y-m-d'),
                'timeout' => $now->format('H:i:s'),
                'changedon' => $now,
                'kartu_dikembalikan' => true
            ];

            GaVisitorTransaction::where('id', $id)->update($updateData);


            return response()->json([
                'success' => true,
                'message' => 'Visitor berhasil checkout'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checkout visitor: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat checkout'
            ], 500);
        }
    }

    /**
     * Update data in both databases
     */
    private function updateInBothDatabases($id, $data)
    {
        DB::beginTransaction();

        try {
            // Update main database
            GaVisitorTransaction::where('id', $id)->update($data);

            // Update backup database
            $backupDb = DB::connection('backup_db');
            $backupDb->beginTransaction();
            $backupDb->table('TRNVISITORINOUT')->where('id', $id)->update($data);
            $backupDb->commit();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if (isset($backupDb)) {
                $backupDb->rollback();
            }
            throw $e;
        }
    }

    private function normalize_name($name)
    {
        // Ubah ke huruf kecil, hapus spasi berlebih
        return strtolower(preg_replace('/\s+/', ' ', trim($name)));
    }
}
