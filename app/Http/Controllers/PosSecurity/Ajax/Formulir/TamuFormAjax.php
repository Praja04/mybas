<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Formulir;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\GaVisitorTransaction;
use App\Models\PosSecurity\GaVisitorVendorTransaction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TamuFormAjax extends Controller
{
    private function normalize_name($name)
    {
        // Ubah ke huruf kecil, hapus spasi berlebih
        return strtolower(preg_replace('/\s+/', ' ', trim($name)));
    }

    public function blacklist(Request $request)
    {
        $validated = $request->validate([
            'trnvisitorid'      => 'required|string|max:50',
            'no_identitas'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'nama'              => 'nullable|string|max:255',
            'jenis_identitas'   => 'required|string|max:50',
            'alasan_blacklist'  => 'required|string',
            'diblacklist_oleh'  => 'required|string|max:100',
        ]);

        $visitor = DB::table('ga_visitor_transaction')
            ->where('trnvisitorid', $request->input('trnvisitorid'))
            ->first();


        if (!$visitor) {
            return response()->json(['success' => false, 'message' => 'Data visitor tidak ditemukan']);
        }

        $normalized_nama = strtolower(trim(preg_replace('/\s+/', ' ', $visitor->namavisitor)));

        $alreadyBlacklisted = DB::table('ga_lgtk_blacklist_identitas')
            ->where(function ($q) use ($visitor, $normalized_nama) {
                $q->where('no_identitas', $visitor->no_ktp_sim)
                    ->orWhere(function ($q2) use ($visitor, $normalized_nama) {
                        $q2->whereRaw('LOWER(TRIM(nama)) = ?', [$normalized_nama])
                            ->where('tanggal_lahir', $visitor->tgl_lahir);
                    });
            })
            ->where('aktif', true)
            ->exists();

        if ($alreadyBlacklisted) {
            return response()->json([
                'success' => false,
                'message' => 'Identitas ini sudah diblacklist sebelumnya.'
            ]);
        }

        $normalized_nama = Str::lower($this->normalize_name($validated['nama'] ?? ''));


        // Simpan ke tabel blacklist dengan trnvisitorid
        DB::table('ga_lgtk_blacklist_identitas')->insert([
            'trnvisitorid'       => $validated['trnvisitorid'],
            'no_identitas'       => $validated['no_identitas'],
            'tanggal_lahir'      => $validated['tanggal_lahir'],
            'nama'               => $normalized_nama ?? null,
            'jenis_identitas'    => $validated['jenis_identitas'],
            'alasan_blacklist'   => $validated['alasan_blacklist'],
            'diblacklist_oleh'   => $validated['diblacklist_oleh'],
            'tanggal_blacklist'  => now(),
            'created_at'         => now(),
            'updated_at'         => now(),
            'aktif'              => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Visitor berhasil diblacklist.'
        ]);
    }

    // 
    public function kembali_kartu(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'trnvisitorid' => 'required|string|max:50',
            'foto_out'     => 'required|string',
        ]);

        try {
            $visitor = GaVisitorVendorTransaction::where('trnvisitorid', $request->trnvisitorid)->first();

            if (!$visitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visitor tidak ditemukan.'
                ]);
            }

            $fotoOutUrl = $this->saveImageFromBase64(
                $request->foto_out,
                // 'foto_out_' . $visitor->trnvisitorid . '_' . now()->format('His'),
                // 'visitors/out'
                $visitor->trnvisitorid . '_foto_out',
                'uploads/pos-security/tamu/selfie_out'
            );

            $now = now();

            $visitor->kartu_dikembalikan = true;
            $visitor->gateidout = $visitor->gateidin;
            $visitor->gatelineidout = $visitor->gatelineidin;
            $visitor->dateout = $now->toDateString(); // YYYY-MM-DD
            $visitor->timeout = $now->format('H:i:s'); // HH:MM:SS
            $visitor->changedon = $now;
            $visitor->changedby = auth()->user()->username ?? 'system'; // atau session user
            $visitor->foto_out = $fotoOutUrl;
            $visitor->kondisi_kacamata_out = $request->kondisi_kacamata_out ?? null;
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

    // cari tamu
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $visitor = DB::table('ga_visitor_vendor')
            ->where(function ($query) use ($keyword) {
                $query->where('trnvisitorid', $keyword)
                    // ->orWhere('barcodepass', $keyword)
                    ->orWhere('no_ktp_sim', $keyword)
                    ->orWhere('no_kartu', $keyword);
                // ->orWhere('empcardid', $keyword)
                // ->orWhere('barcodeemp', $keyword)
                // ->orWhere('rfidemp', $keyword)
                // ->orWhere('qr_code_saat_ini', $keyword);
            })
            // ->whereNull('dateout')  // Pastikan yang belum keluar
            ->where(function ($q) {
                $q->whereNull('dateout')  // belum keluar
                    ->orWhere('kartu_dikembalikan', true); // atau sudah dikembalikan
            })
            ->orderBy('createdon', 'desc')
            ->first();


        if ($visitor) {
            // Cek apakah sudah keluar
            if (!is_null($visitor->dateout)) {
                // Format tanggal & waktu jadi format yang lebih ramah user
                $tanggalMasuk = Carbon::parse($visitor->datein)->translatedFormat('d F Y');
                $jamMasuk = $visitor->timein ?? '-';

                $tanggalKeluar = Carbon::parse($visitor->dateout)->translatedFormat('d F Y');
                $jamKeluar = $visitor->timeout ?? '-';

                return response()->json([
                    'success' => false,
                    'message' => 'Visitor atas nama ' . ($visitor->namavisitor ?? '-') .
                        ' telah keluar pada tanggal ' . $tanggalKeluar . ' pukul ' . $jamKeluar . ' WIB. ' .
                        'Kartu dengan nomor ' . ($visitor->no_kartu ?? '-') . ' sebelumnya digunakan untuk kunjungan pada tanggal ' .
                        $tanggalMasuk . ' pukul ' . $jamMasuk . ' WIB. ' .
                        'Kartu ini sekarang sudah bisa digunakan kembali.',
                ]);
            }

            // Cek apakah sudah cek kendaraan jika jenis kunjungannya transporter kecil
            if ($visitor->type === 'TRANSPORTER') {
                $cekKendaraan = DB::table('ga_cek_kendaraan')
                    ->whereRaw("
                            REPLACE(REPLACE(UPPER(nomor_polisi), ' ', ''), '-', '')
                            =
                            REPLACE(REPLACE(UPPER(?), ' ', ''), '-', '')
                        ", [$visitor->nopol])
                    // ->where('checked_in_at', '>=', $visitor->createdon)
                    ->where('checked_in_at', '>=', now()->subHours(24))
                    ->orderBy('checked_in_at', 'desc')
                    ->first();

                // belum pernah cek kendaraan sama sekali pada kedatangan ini
                if (!$cekKendaraan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tamu ini menggunakan kendaraan dan belum melakukan pengecekan kendaraan masuk & keluar.'
                    ]);
                }

                // sudah cek masuk tapi belum cek keluar
                if ($cekKendaraan->checked_in_at && is_null($cekKendaraan->checked_out_at)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tamu ini menggunakan kendaraan belum melakukan cek kendaraan keluar.'
                    ]);
                }
            }

            // Jika kartu belum dikembalikan atau masih aktif → valid
            if (is_null($visitor->kartu_dikembalikan) || $visitor->kartu_dikembalikan == false) {
                return response()->json([
                    'success' => true,
                    'data' => $visitor
                ]);
            }

            // Jika kartu sudah dikembalikan (tapi belum tap out?)
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah dikembalikan. Silakan check status kunjungan di menu POS 2 Out atau pastikan visitor belum tap out sebelumnya.',
            ]);
        }

        // Data tidak ditemukan sama sekali
        return response()->json([
            'success' => false,
            'message' => 'Data visitor tidak ditemukan. Pastikan data sudah teregistrasi dan belum keluar.',
        ]);
    }

    //    tamu
    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'namavisitor'       => 'required|string|max:100',
            'nomorktp'          => 'required|string|max:100',
            'tgllahir'          => 'nullable|date|before_or_equal:today',
            'namacomp'          => 'required|string|max:100',
            'jenis'             => 'required|string|max:50',
            'sumpeople'         => 'required|integer|min:1|max:10',
            'keperluan'         => 'required|string|max:100',
            'host'              => 'required|string|max:100',
            'hostdeptid'        => 'required|string|max:50',
            'rfid'              => 'required|string|max:100',
            'imgvisitorpathin'  => 'required|string',
            'foto'              => 'required|string',
            'is_kacamata'       => 'required|boolean',
        ], [
            'namavisitor.required'      => 'Nama visitor harus diisi',
            'namacomp.required'         => 'Nama perusahaan harus diisi',
            'nomorktp.required'         => 'Nomor KTP harus diisi',
            'tgllahir.before_or_equal'  => 'Tanggal lahir tidak boleh di masa depan',
            'jenis.required'            => 'Jenis wajib diisi',
            'sumpeople.required'        => 'Jumlah orang harus diisi',
            'sumpeople.min'             => 'Jumlah orang minimal 1',
            'sumpeople.max'             => 'Jumlah orang maksimal 10',
            'keperluan.required'        => 'Keperluan wajib diisi',
            'host.required'             => 'Host wajib diisi',
            'hostdeptid.required'       => 'Host Dept ID wajib diisi',
            'rfid.required'             => 'Nomor kartu wajib diisi',
            'imgvisitorpathin.required' => 'Foto KTP harus diunggah.',
            'foto.required'             => 'Foto selfie wajib diunggah.',
            'is_kacamata.required'      => 'Pertanyaan kacamata wajib diisi',
        ]);

        // Validasi kondisional:
        // Jika jenis visitor adalah "transporter", maka field nopol dan purpose menjadi wajib
        $validator->sometimes(['nopol', 'purpose'], 'required|string|max:20', function ($input) {
            return $input->jenis === 'transporter';
        });

        // Ambil nama, tanggal lahir, dan nomor identitas
        $normalized_nama = Str::lower($this->normalize_name($request->input('namavisitor')));
        $input_tanggal_lahir = Carbon::parse($request->input('tgllahir'))->format('Y-m-d');
        $input_no_identitas = $request->input('nomorktp');

        // Cek blacklist berdasarkan:
        // Nomor identitas atau nama + tanggal lahir
        $blacklist = DB::table('ga_lgtk_blacklist_identitas')
            ->where(function ($q) use ($input_no_identitas, $normalized_nama, $input_tanggal_lahir) {
                $q->where('no_identitas', $input_no_identitas)

                    ->orWhere(function ($q2) use ($normalized_nama, $input_tanggal_lahir) {
                        $q2->whereRaw('LOWER(TRIM(nama)) = ?', [$normalized_nama])
                            ->where('tanggal_lahir', $input_tanggal_lahir);
                    });
            })
            ->where('aktif', true)
            ->first();

        // Jika visitor ditemukan dalam blacklist
        if ($blacklist) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Identitas atas nama. %s, Tanggal Lahir: %s, diblacklist karena: %s.',
                    $blacklist->nama ?? '-',
                    $blacklist->tanggal_lahir,
                    $blacklist->alasan_blacklist ?? '-'
                )
            ], 403);
        }

        // Jika validasi gagal, kembalikan response error 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Mapping purpose (jenis kunjungan) ke prefix ID visitor
            // Prefix ini digunakan sebagai bagian awal ID unik visitor
            $prefixMapping = [
                'MUAT'    => 'TMBM',
                'BONGKAR' => 'TMGB',
                'VENDOR'  => 'VN',
                'TAMU'    => 'TM',
            ];

            $jenis  = strtoupper($request->purpose);
            $prefix = $prefixMapping[$jenis] ?? 'TM';
            $trnVisitorId = $this->generateVisitorId($prefix);

            // Proses foto KTP
            $ktpImagePath = null;
            if ($request->has('imgvisitorpathin')) {
                $ktpImagePath = $this->saveImageFromBase64(
                    $request->imgvisitorpathin,
                    $trnVisitorId . '_ktp',
                    'uploads/pos-security/tamu/ktp'
                );
            }

            // Proses foto selfie
            $selfiePaths = [];
            if ($request->has('foto')) {
                $selfiePhotos = json_decode($request->foto);
                foreach ($selfiePhotos as $index => $selfiePhoto) {
                    $path = $this->saveImageFromBase64(
                        $selfiePhoto,
                        $trnVisitorId . '_selfie_' . $index,
                        'uploads/pos-security/tamu/selfie'
                    );
                    $selfiePaths[] = $path;
                }
            }

            $now = Carbon::now();
            $dateIn = $now->format('Y-m-d');
            $timeIn = $now->format('H:i:s');

            $visitorData = [
                'trnvisitorid'         => $trnVisitorId,
                'namavisitor'          => strtoupper($request->namavisitor),
                'no_ktp_sim'           => strtoupper($input_no_identitas),
                'no_kartu'             => strtoupper($request->rfid),
                'namacomp'             => strtoupper($request->namacomp),
                'keperluan'            => strtoupper($request->keperluan),
                'purpose'              => $request->purpose,
                'nopol'                => strtoupper($request->nopol),
                'host'                 => strtoupper($request->host),
                'hostdeptid'           => strtoupper($request->hostdeptid),
                'type'                 => strtoupper($request->jenis),
                'tgl_lahir'            => $request->tgllahir,
                'sumpeople'            => $request->sumpeople ?? 1,
                'gateidin'             => 'POS01', // gate id masuk
                'gatelineidin'         => 'JGB01', // gate line id
                'datein'               => $dateIn,
                'timein'               => $timeIn,
                'createdby'            => 'system', // default (tidak dikirim)
                'createdon'            => $now,
                'imgvisitorpathin'     => $ktpImagePath,
                'foto'                 => json_encode($selfiePaths),
                // 'nohpdriver'           => null,
                'typevisitor'          => '1',
                'flagtrx'              => 'X', // flag status transaksi (default: X => baru dibuat)
                'kartu_dikembalikan'   => false,
                'qr_code_saat_ini'     => $request->qr_code_saat_ini ?? null,
                'is_kacamata'          => $request->is_kacamata,
                'kondisi_kacamata'     => $request->kondisi_kacamata ?? null,
            ];

            DB::transaction(function () use ($visitorData, &$isNewRecord, &$isUpdated, &$isCardInUse) {

                // Cek apakah kartu masih dipakai oleh vendor/tamu lain
                $dipakaiVendor = GaVisitorVendorTransaction::where('no_kartu', $visitorData['no_kartu'])
                    ->where(function ($q) {
                        $q->whereNull('kartu_dikembalikan')
                            ->orWhere('kartu_dikembalikan', false)
                            ->orWhereNull('dateout');
                    })
                    ->exists();

                // Cek apakah kartu masih dipakai oleh supplier/transporter lain
                $dipakaiSupplier = GaVisitorTransaction::where('no_kartu', $visitorData['no_kartu'])
                    ->where(function ($q) {
                        $q->whereNull('kartu_dikembalikan')
                            ->orWhere('kartu_dikembalikan', false)
                            ->orWhereNull('dateout');
                    })
                    ->exists();

                // Jika dipakai di salah satu tabel → kartu sedang digunakan
                $kartuMasihDipakai = $dipakaiVendor || $dipakaiSupplier;

                if ($kartuMasihDipakai) {
                    $isCardInUse = true;
                    return;
                }

                // Cek apakah record visitor dengan ID ini sudah ada
                $existing = GaVisitorVendorTransaction::where('trnvisitorid', $visitorData['trnvisitorid'])->first();

                if ($existing) {
                    // Jika visitor belum checkout, update record existing
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
                    // Jika belum ada record, buat record baru
                    GaVisitorVendorTransaction::create($visitorData);
                    $isNewRecord = true;
                }
            });

            // $kartuDipakaiOleh = GaVisitorVendorTransaction::where('no_kartu', $visitorData['no_kartu'])
            //     ->where(function ($q) {
            //         $q->whereNull('kartu_dikembalikan')
            //             ->orWhere('kartu_dikembalikan', false)
            //             ->orWhereNull('dateout');
            //     })
            //     ->orderByDesc('id')
            //     ->first();

            if ($isCardInUse) {
                // $namaVisitor = $kartuDipakaiOleh->namavisitor ?? '-';
                // $noKartu     = $kartuDipakaiOleh->no_kartu ?? '-';
                // $noKtp       = $kartuDipakaiOleh->no_ktp_sim ?? '-';
                // $dateIn      = $kartuDipakaiOleh->datein ?? '-';
                // $timeIn      = $kartuDipakaiOleh->timein ?? '-';

                // $message  = "Nomor kartu {$noKartu} saat ini masih digunakan oleh {$namaVisitor} ";
                // $message .= "(Nomor KTP: {$noKtp}) sejak {$dateIn} pukul {$timeIn}. ";
                // $message .= "Silakan pastikan kartu sudah dikembalikan. ";
                // $message .= "Periksa status kartu melalui menu *OUT*, lalu coba lagi.";

                return response()->json([
                    // 'message' => $message,
                    'message' => 'Nomor kartu ini masih digunakan oleh visitor lain. Silakan gunakan kartu yang lain.'
                ], 400);
            }

            $message = 'Data Transporter /vendor / tamu tidak diubah.';
            if ($isNewRecord) {
                $message = 'Data Transporter vendor / tamu berhasil disimpan! ID: ' . $visitorData['trnvisitorid'];
            } elseif ($isUpdated) {
                $message = 'Data Transporter vendor / tamu berhasil diperbarui! ID: ' . $visitorData['trnvisitorid'];
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

    /**
     * Generate unique visitor ID
     */
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
        $relativePath = "{$folder}/{$datePath}";

        $fullPath = public_path($relativePath);

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        File::put($fullPath . '/' . $fileName, $imageData);

        // Return URL public
        return asset($relativePath . '/' . $fileName);
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
     * Show visitor detail
     */
    public function show($id)
    {
        $visitor = GaVisitorTransaction::findOrFail($id);

        return view('visitor.show', compact('visitor'));
    }

    /**
     * Update visitor checkout
     */
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
}
