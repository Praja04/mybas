<?php

namespace App\Http\Controllers\PosSecurity\Web\Form;

use App\Department;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
// GaVisitorVendorTransaction
// GaVisitorTransaction

class TamuFormController extends Controller
{
    public function index()
    {
        $departments = Department::where('status', '1')->get();

        return view('pos-security.formulir.tamu.index', compact('departments'));
    }

    // ===== in =====
    private function generateVisitorId($prefix)
    {
        $date = date('Ymd');

        // Get last visitor ID for today by prefix
        $lastVisitor = GaVisitorVendorTransaction::where('trnvisitorid', 'LIKE', $prefix . $date . '%')
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

    private function saveImageFromBase64($base64Image, $filename, $folder = 'visitors')
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

    public function store(Request $request)
    {
        // 1. validasi data
        $validator = Validator::make($request->all(), [
            'namavisitor' => 'required|string|max:100',
            'namacomp' => 'required|string|max:100',
            'tgllahir' => 'required|date|before:today',
            'jenis' => 'required|string|max:50',
            'noktp' => 'required|string|max:100',
            'sumpeople' => 'required|integer|min:1|max:10',
            'keperluan' => 'required|string|max:100',
            'host' => 'required|string|max:100',
            'hostdeptid' => 'required|string|max:50',
            // 'plant' => 'required|string|max:20',
            // 'trnvisitorid' => 'required|string|max:100',
            'nomor_kartu' => 'required|string|max:100',
            'imgvisitorpathin' => 'required|string',
            'foto'     => 'required|string', // jika dikirim sebagai JSON string
        ], [
            'namavisitor.required' => 'Nama visitor harus diisi',
            'namacomp.required' => 'Nama perusahaan harus diisi',
            'tgllahir.required' => 'Tanggal lahir harus diisi',
            'tgllahir.date' => 'Format tanggal lahir tidak valid, gunakan format YYYY-MM-DD',
            'jenis.required' => 'Jenis wajib diisi',
            'noktp.required' => 'Nomor KTP harus diisi',
            'sumpeople.required' => 'Jumlah orang harus diisi',
            'sumpeople.min' => 'Jumlah orang minimal 1',
            'sumpeople.max' => 'Jumlah orang maksimal 10',
            'keperluan.required' => 'Keperluan wajib diisi',
            'host.required' => 'Nama PIC wajib diisi',
            'hostdeptid.required' => 'Departemen PIC wajib diisi',
            // 'plant.required' => 'Plant wajib diisi',
            // 'trnvisitorid.required' => 'Transaction Visitor ID wajib diisi',
            'nomor_kartu.required' => 'Nomor kartu wajib diisi',
            'imgvisitorpathin.required' => 'Foto KTP harus diunggah.',
            'foto.required'             => 'Foto selfie wajib diunggah.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validasi gagal saat simpan formulir', [
                'errors' => $validator->errors(),
                'input' => $request->except(['imgvisitorpathin', 'foto']),
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // 2. generate visitor ID
            $prefixMapping = [
                'MUAT'    => 'BM',
                'BONGKAR' => 'GB',
                'VENDOR'  => 'VN',
                'TAMU'    => 'TM',
            ];

            $jenis = strtoupper($request->jenis); // Contoh: "VENDOR"
            $prefix = $prefixMapping[$jenis] ?? 'TM'; // Default TM kalau tidak ada
            $trnVisitorId = $this->generateVisitorId($prefix);

            // 3. image process
            $ktpImagePath = $request->imgvisitorpathin;

            $selfiePaths = [];
            if ($request->filled('foto')) {
                $fotoData = json_decode($request->foto, true);

                if (is_array($fotoData)) {
                    foreach ($fotoData as $index => $photo) {
                        if (str_starts_with($photo, 'data:image')) {
                            // Base64 → simpan ke storage
                            $path = $this->saveImageFromBase64($photo, $trnVisitorId . '_selfie_' . $index, 'visitors/selfie');
                            $selfiePaths[] = $path;
                        } elseif (filter_var($photo, FILTER_VALIDATE_URL)) {
                            // URL → langsung gunakan
                            $selfiePaths[] = $photo;
                        }
                    }
                } else {
                    // Jika isinya hanya satu URL
                    $selfiePaths[] = $request->foto;
                }
            }

            $fotoidentitas = "";
            if ($request->filled('imgvisitorpathin')) {
                $fotoDataktp = json_decode($request->foto, true);

                if (is_array($fotoDataktp)) {
                    foreach ($fotoDataktp as $index => $photo) {
                        if (str_starts_with($photo, 'data:image')) {
                            // Base64 → simpan ke storage
                            $path = $this->saveImageFromBase64($photo, $trnVisitorId . '_selfie_' . $index, 'visitors/ktp');
                            $fotoidentitas = $path;
                        } elseif (filter_var($photo, FILTER_VALIDATE_URL)) {
                            // URL → langsung gunakan
                            $fotoidentitas = $photo;
                        }
                    }
                } else {
                    // Jika isinya hanya satu URL
                    $fotoidentitas = $request->foto;
                }
            }

            // 4. prepare stored data
            $now = Carbon::now();
            $dateIn = $now->format('Y-m-d');
            $timeIn = $now->format('H:i:s');

            $visitorData = [
                'trnvisitorid' => $trnVisitorId,
                'namavisitor' => strtoupper($request->namavisitor),
                'namacomp' => strtoupper($request->namacomp),
                'tgllahir' => strtoupper($request->tgllahir),
                'jenis' => strtoupper($request->jenis),
                'no_ktp_sim' => strtoupper($request->noktp),
                'sumpeople' => $request->sumpeople ?? 1,
                'keperluan' => strtoupper($request->keperluan),
                'host' => strtoupper($request->host),
                'hostdeptid' => strtoupper($request->hostdeptid),
                'no_kartu' => strtoupper($request->nomor_kartu),
                'gateidin' => 'POS01',
                'gatelineidin' => 'JGB02',
                'datein' => $dateIn,
                'timein' => $timeIn,
                'createdby' => '100071', // default (tidak dikirim)
                'createdon' => $now,
                'imgvisitorpathin' => $fotoidentitas,
                'nohpdriver' => null, // tidak dikirim
                'typevisitor' => '1',
                'flagtrx' => 'X',
                'foto' => json_encode($selfiePaths),
                'kartu_dikembalikan' => false,
                'qr_code_saat_ini' => $request->qr_code_saat_ini ?? null,
            ];

            // dd($visitorData);

            DB::transaction(function () use ($visitorData, &$isNewRecord, &$isUpdated, &$isCardInUse) {
                // 5. cek apakah kartu RFID masih dipakai oleh tamu lain
                $kartuMasihDipakai = GaVisitorVendorTransaction::where('no_kartu', $visitorData['no_kartu'])
                    ->where(function ($q) {
                        $q->whereNull('kartu_dikembalikan')
                            ->orWhere('kartu_dikembalikan', false) // belum dikembalikan
                            ->orWhereNull('dateout'); // orangnya blm keluar
                    })
                    ->exists();

                if ($kartuMasihDipakai) {
                    $isCardInUse = true;
                    return;
                }

                // 6. cek apakah transaksi (visitor) sudah ada
                $existing = GaVisitorVendorTransaction::where('trnvisitorid', $visitorData['trnvisitorid'])->first();

                // kalau ada, update
                if ($existing) {
                    if (
                        is_null($existing->gateidout) &&
                        is_null($existing->gatelineidout) &&
                        is_null($existing->dateout) &&
                        is_null($existing->timeout) &&
                        $existing->kartu_dikembalikan == false
                    ) {
                        $existing->update($visitorData);
                        $isUpdated = true;
                    }
                } else {
                    GaVisitorVendorTransaction::create($visitorData);
                    $isNewRecord = true;
                }
            });

            if ($isCardInUse) {
                return response()->json([
                    'message' => 'Nomor kartu ini masih digunakan oleh visitor lain. Silakan gunakan kartu yang lain.'
                ], 400);
            }

            $message = 'Data visitor tidak diubah.';
            if ($isNewRecord) {
                $message = 'Data visitor berhasil disimpan! ID: ' . $visitorData['trnvisitorid'];
            } elseif ($isUpdated) {
                $message = 'Data visitor berhasil diperbarui! ID: ' . $visitorData['trnvisitorid'];
            }

            return response()->json([
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving visitor data: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ===== out =====
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $visitor = DB::table('ga_visitor_vendor')
            ->where(function ($query) use ($keyword) {
                $query->where('trnvisitorid', $keyword)
                    ->orWhere('barcodepass', $keyword)
                    ->orWhere('no_ktp_sim', $keyword)
                    ->orWhere('no_kartu', $keyword)
                    ->orWhere('empcardid', $keyword)
                    ->orWhere('barcodeemp', $keyword)
                    ->orWhere('rfidemp', $keyword)
                    ->orWhere('qr_code_saat_ini', $keyword);
            })
            ->whereNull('dateout')  // Pastikan yang belum keluar
            ->orderBy('createdon', 'desc')
            ->first();

        if ($visitor) {
            return response()->json([
                'success' => true,
                'data' => $visitor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data visitor tidak ditemukan atau sudah keluar.'
        ]);
    }

    public function checkout(Request $request, $id)
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

    public function kembali_kartu(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'trnvisitorid' => 'required|string|max:50'
        ]);

        try {
            $visitor = GaVisitorVendorTransaction::where('trnvisitorid', $request->trnvisitorid)->first();

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
}
