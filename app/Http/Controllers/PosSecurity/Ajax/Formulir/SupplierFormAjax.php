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

class SupplierFormAjax extends Controller
{

    private function normalize_name($name)
    {
        // Ubah ke huruf kecil, hapus spasi berlebih
        return strtolower(preg_replace('/\s+/', ' ', trim($name)));
    }


    public function getVisitorDetail(Request $request)
    {
        $id = $request->query('id');

        $visitor = DB::table('ga_visitor_transaction')
            ->where('trnvisitorid', $id)
            ->first();

        if ($visitor) {
            return response()->json([
                'success' => true,
                'data' => $visitor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data visitor tidak ditemukan.'
        ]);
    }


    public function blacklist(Request $request)
    {
        // $validated = $request->validate([
        //   'trnvisitorid'      => 'required|string|max:50',
        //   'no_identitas'      => 'required|string|max:100',
        //   'tanggal_lahir'     => 'required|date',
        //   'nama'              => 'nullable|string|max:255',
        //   'jenis_identitas'   => 'required|string|max:50',
        //   'alasan_blacklist'  => 'required|string',
        //   // 'diblacklist_oleh'  => 'required|string|max:100',
        // ]);

        // $visitor = DB::table('ga_visitor_transaction')
        //   ->where('trnvisitorid', $request->input('trnvisitorid'))
        //   ->first();


        // if (!$visitor) {
        //   return response()->json(['success' => false, 'message' => 'Data visitor tidak ditemukan']);
        // }

        // $normalized_nama = strtolower(trim(preg_replace('/\s+/', ' ', $visitor->namavisitor)));

        // $alreadyBlacklisted = DB::table('ga_lgtk_blacklist_identitas')
        //   ->where(function ($q) use ($visitor, $normalized_nama) {
        //     $q->where('no_identitas', $visitor->no_ktp_sim)
        //       ->orWhere(function ($q2) use ($visitor, $normalized_nama) {
        //         $q2->whereRaw('LOWER(TRIM(nama)) = ?', [$normalized_nama])
        //           ->where('tanggal_lahir', $visitor->tgl_lahir);
        //       });
        //   })
        //   ->where('aktif', true)
        //   ->exists();

        // if ($alreadyBlacklisted) {
        //   return response()->json([
        //     'success' => false,
        //     'message' => 'Identitas ini sudah diblacklist sebelumnya.'
        //   ]);
        // }

        // $normalized_nama = Str::lower($this->normalize_name($validated['nama'] ?? ''));


        // // Simpan ke tabel blacklist dengan trnvisitorid
        // DB::table('ga_lgtk_blacklist_identitas')->insert([
        //   'trnvisitorid'       => $validated['trnvisitorid'],
        //   'no_identitas'       => $validated['no_identitas'],
        //   'tanggal_lahir'      => $validated['tanggal_lahir'],
        //   'nama'               => $normalized_nama ?? null,
        //   'jenis_identitas'    => $validated['jenis_identitas'],
        //   'alasan_blacklist'   => $validated['alasan_blacklist'],
        //   'diblacklist_oleh'   => "system",
        //   'tanggal_blacklist'  => now(),
        //   'created_at'         => now(),
        //   'updated_at'         => now(),
        //   'aktif'              => true,
        // ]);

        // return response()->json([
        //   'success' => true,
        //   'message' => 'Visitor berhasil diblacklist.'
        // ]);

        $validated = $request->validate([
            'trnvisitorid'      => 'required|string|max:50',
            'no_identitas'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'nama'              => 'nullable|string|max:255',
            'jenis_identitas'   => 'required|string|max:50',
            'alasan_blacklist'  => 'required|string',
        ]);

        $visitor = DB::table('ga_visitor_transaction')
            ->where('trnvisitorid', $validated['trnvisitorid'])
            ->first();

        if (!$visitor) {
            return response()->json(['success' => false, 'message' => 'Data visitor tidak ditemukan']);
        }

        $normalized_nama = strtolower(trim(preg_replace('/\s+/', ' ', $visitor->namavisitor)));

        $existing = DB::table('ga_lgtk_blacklist_identitas')
            ->where(function ($q) use ($visitor, $normalized_nama) {
                $q->where('no_identitas', $visitor->no_ktp_sim)
                    ->orWhere(function ($q2) use ($visitor, $normalized_nama) {
                        $q2->whereRaw('LOWER(TRIM(nama)) = ?', [$normalized_nama])
                            ->where('tanggal_lahir', $visitor->tgl_lahir);
                    });
            })
            ->where('aktif', true)
            ->orderByDesc('tanggal_blacklist')
            ->first();

        if ($existing) {
            // dd($existing);
            return response()->json([
                'success' => false,
                'message' =>
                "Identitas ini sudah diblacklist sebelumnya.<br>" .
                    "<strong>Nama:</strong> {$existing->nama}<br>" .
                    "<strong>No Identitas:</strong> {$existing->no_identitas}<br>" .
                    "<strong>Tanggal Lahir:</strong> " . ($existing->tanggal_lahir ? \Carbon\Carbon::parse($existing->tanggal_lahir)->format('d-m-Y') : '-') . "<br>" .
                    "<strong>Tanggal Blacklist:</strong> " . ($existing->tanggal_blacklist ? \Carbon\Carbon::parse($existing->tanggal_blacklist)->format('d-m-Y H:i') : '-') . "<br>" .
                    "<strong>Alasan:</strong> {$existing->alasan_blacklist}<br>" .
                    "<strong>Diblacklist Oleh:</strong> {$existing->diblacklist_oleh}",
                'data' => [
                    'nama' => $existing->nama,
                    'no_identitas' => $existing->no_identitas,
                    'tanggal_lahir' => $existing->tanggal_lahir ? \Carbon\Carbon::parse($existing->tanggal_lahir)->format('d-m-Y') : null,
                    'tanggal_blacklist' => $existing->tanggal_blacklist ? \Carbon\Carbon::parse($existing->tanggal_blacklist)->format('d-m-Y H:i') : null,
                    'alasan_blacklist' => $existing->alasan_blacklist,
                    'diblacklist_oleh' => $existing->diblacklist_oleh,
                    'aktif' => $existing->aktif,
                ]
            ]);
        }

        $normalized_nama = Str::lower($this->normalize_name($validated['nama'] ?? ''));

        // Simpan blacklist baru
        $blacklistId = DB::table('ga_lgtk_blacklist_identitas')->insertGetId([
            'trnvisitorid'       => $validated['trnvisitorid'],
            'no_identitas'       => $validated['no_identitas'],
            'tanggal_lahir'      => $validated['tanggal_lahir'],
            'nama'               => $normalized_nama ?? null,
            'jenis_identitas'    => $validated['jenis_identitas'],
            'alasan_blacklist'   => $validated['alasan_blacklist'],
            'diblacklist_oleh'   => "system",
            'tanggal_blacklist'  => now(),
            'created_at'         => now(),
            'updated_at'         => now(),
            'aktif'              => true,
        ]);

        $blacklist = DB::table('ga_lgtk_blacklist_identitas')->find($blacklistId);
        $visitor = DB::table('ga_visitor_transaction')
            ->where('trnvisitorid', $blacklist->trnvisitorid)
            ->first();

        $fotoJson = json_decode(optional($visitor)->foto, true);
        $fotoDiri = is_array($fotoJson) ? ($fotoJson[0] ?? null) : null;

        return response()->json([
            'success' => true,
            'message' => 'Visitor berhasil diblacklist.',
            'data' => [
                'id' => $blacklist->id,
                'nama' => $blacklist->nama,
                'no_identitas' => $blacklist->no_identitas,
                'jenis_identitas' => $blacklist->jenis_identitas,
                'tanggal_lahir' => optional($blacklist->tanggal_lahir)->format('d-m-Y'),
                'alasan_blacklist' => $blacklist->alasan_blacklist,
                'tanggal_blacklist' => optional($blacklist->tanggal_blacklist)->format('d-m-Y H:i'),
                'diblacklist_oleh' => $blacklist->diblacklist_oleh,
                'aktif' => $blacklist->aktif,

                'foto_diri_url' => $fotoDiri,
                'foto_ktp_url' => optional($visitor)->imgvisitorpathin,
            ]
        ]);
    }

    public function reportLostCard(Request $request)
    {
        $id = $request->input('visitor_id');

        $updated = DB::table('ga_visitor_transaction')
            ->where('trnvisitorid', $id)
            ->update([
                'kartu_dikembalikan' => false, // anggap false = hilang
                'lost_card_reported' => true, // atau tambahkan field khusus jika perlu
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Kartu telah dilaporkan hilang.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal melaporkan kartu hilang.'
        ]);
    }


    // cari supplier
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $visitor = DB::table('ga_visitor_transaction')
            ->where(function ($query) use ($keyword) {
                $query->where('trnvisitorid', $keyword)
                    ->orWhere('no_kartu', $keyword);
            })
            ->whereNull('dateout')
            ->where(function ($q) {
                $q->whereNull('kartu_dikembalikan')
                    ->orWhere('kartu_dikembalikan', false);
            })
            ->orderBy('createdon', 'desc')
            ->first();

        if ($visitor) {
            // cek apakah sudah keluar
            // if (!is_null($visitor->dateout)) {
            //     $tanggalMasuk = Carbon::parse($visitor->datein)->translatedFormat('d F Y');
            //     $jamMasuk = $visitor->timein ?? '-';

            //     $tanggalKeluar = Carbon::parse($visitor->dateout)->translatedFormat('d F Y');
            //     $jamKeluar = $visitor->timeout ?? '-';

            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Visitor atas nama ' . ($visitor->namavisitor ?? '-') .
            //             ' telah keluar pada tanggal ' . $tanggalKeluar . ' pukul ' . $jamKeluar . ' WIB. ' .
            //             'Kartu dengan nomor ' . ($visitor->no_kartu ?? '-') . ' sebelumnya digunakan untuk kunjungan pada tanggal ' .
            //             $tanggalMasuk . ' pukul ' . $jamMasuk . ' WIB. ' .
            //             'Kartu ini sekarang sudah bisa digunakan kembali.',
            //     ]);
            // }

            // Jika kartu belum dikembalikan atau masih aktif → valid
            // if (is_null($visitor->kartu_dikembalikan) || $visitor->kartu_dikembalikan == false) {
            //     return response()->json([
            //         'success' => true,
            //         'data' => $visitor
            //     ]);
            // }

            // validasi apakah kendaraan sudah dilakukan cek kendaraan
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
                    'message' => 'Kendaraan belum melakukan cek kendaraan masuk & keluar pada kedatangan ini.'
                ]);
            }

            // sudah cek masuk tapi belum cek keluar
            if ($cekKendaraan->checked_in_at && is_null($cekKendaraan->checked_out_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kendaraan belum melakukan cek keluar.'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $visitor
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data pengunjung tidak ditemukan atau sudah keluar.'
        ]);
    }

    // supplier
    public function store(Request $request)
    {

        // dd($request->all());
        // Validasi tetap sama
        $validator = Validator::make($request->all(), [
            'namavisitor'       => 'required|string|max:100',
            'keterangan'        => 'required|string|max:100',
            'nomorktp'          => 'required|string|max:100',
            'tgllahir'          => 'required|nullable|date|before_or_equal:today',
            'namacomp'          => 'required|string|max:100',
            'rfid'              => 'required|string|max:100',
            'purpose'           => 'required|in:BONGKAR,MUAT',
            'nopol'             => 'required|string|max:20',
            'createdby'         => 'nullable',
            'sumpeople'         => 'nullable|integer|min:1|max:10',
            'imgvisitorpathin'  => 'required|string',
            'foto'              => 'required|string',
            'nohpdriver'        => 'nullable|string|max:20'
        ], [
            'namavisitor.required'      => 'Nama visitor harus diisi',
            'namacomp.required'         => 'Nama perusahaan harus diisi',
            'rfid.required'             => 'Nomor Kartu harus diisi',
            'purpose.required'          => 'Tujuan harus dipilih',
            'purpose.in'                => 'Tujuan harus BONGKAR atau MUAT',
            'nopol.required'            => 'Nomor polisi harus diisi',
            'nomorktp.required'         => 'Nomor KTP harus diisi',
            'tgllahir.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan',
            'imgvisitorpathin.required' => 'Foto KTP harus diambil',
            'foto.required'             => 'Foto selfie wajib diambil',
            'sumpeople.min'             => 'Jumlah orang minimal 1',
            'sumpeople.max'             => 'Jumlah orang maksimal 10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Ambil input untuk pengecekan blacklist
        $normalized_nama = Str::lower($this->normalize_name($request->input('namavisitor')));
        $input_tanggal_lahir = Carbon::parse($request->input('tgllahir'))->format('Y-m-d');
        $input_no_identitas = $request->input('nomorktp');

        // Cek blacklist
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

        if ($blacklist) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Identitas a.n. %s, Tanggal Lahir: %s, diblacklist karena: %s.',
                    $blacklist->nama ?? '-',
                    $blacklist->tanggal_lahir,
                    $blacklist->alasan_blacklist ?? '-'
                )
            ], 403);
        }

        try {

            $prefixMapping = [
                'MUAT'    => 'BM',
                'BONGKAR' => 'GB',
                'VENDOR'  => 'VN',
                'TAMU'    => 'TM',
            ];

            $jenis  = strtoupper($request->purpose);
            $prefix = $prefixMapping[$jenis] ?? 'TM';
            $trnVisitorId = $this->generateVisitorId($prefix);

            $ktpImagePath = null;
            if ($request->has('imgvisitorpathin')) {
                $ktpImagePath = $this->saveImageFromBase64(
                    $request->imgvisitorpathin,
                    $trnVisitorId . '_ktp',
                    'uploads/pos-security/suppliers/ktp'
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
                        'uploads/pos-security/suppliers/selfie'
                    );
                    $selfiePaths[] = $path;
                }
            }

            $now = Carbon::now();

            $supplier_data = [
                'trnvisitorid'       => $trnVisitorId,
                'namavisitor'        => $normalized_nama,
                'keterangan'        => strtoupper($request->keterangan),
                'no_ktp_sim'         => strtoupper($request->nomorktp),
                'no_kartu'           => strtoupper($request->rfid),
                'namacomp'           => strtoupper($request->namacomp),
                'purpose'            => $request->purpose,
                'nopol'              => strtoupper($request->nopol),
                'gateidin'           => 'POS01',
                'gatelineidin'       => 'JGB01',
                'datein'             => $now->format('Y-m-d'),
                'timein'             => $now->format('H:i:s'),
                'createdby'          => 'system',
                'createdon'          => $now,
                'sumpeople'          => $request->sumpeople ?? 1,
                'imgvisitorpathin'   => $ktpImagePath,
                'nohpdriver'         => $request->nohpdriver,
                'typevisitor'        => '1',
                'flagtrx'            => 'X',
                'foto'               => json_encode($selfiePaths),
                'kartu_dikembalikan' => false,
                'qr_code_saat_ini'   => $request->qr_code_saat_ini,
                'tgl_lahir'          => $request->tgllahir,
            ];

            // dd($supplier_data);

            $isNewRecord   = false;
            $isUpdated     = false;
            $isCardInUse   = false;

            DB::transaction(function () use ($supplier_data, &$isNewRecord, &$isUpdated, &$isCardInUse) {
                // Cek apakah kartu masih dipakai oleh supplier/transporter lain
                $dipakaiSupplier = GaVisitorTransaction::where('no_kartu', $supplier_data['no_kartu'])
                    ->where(function ($q) {
                        $q->whereNull('kartu_dikembalikan')
                            ->orWhere('kartu_dikembalikan', false)
                            ->orWhereNull('dateout');
                    })
                    ->exists();

                // Cek apakah kartu masih dipakai oleh vendor/tamu lain
                $dipakaiVendor = GaVisitorVendorTransaction::where('no_kartu', $supplier_data['no_kartu'])
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

                $existing = GaVisitorTransaction::where('trnvisitorid', $supplier_data['trnvisitorid'])->first();

                if ($existing) {
                    if (
                        is_null($existing->gateidout) &&
                        is_null($existing->gatelineidout) &&
                        is_null($existing->dateout) &&
                        is_null($existing->timeout) &&
                        $existing->kartu_dikembalikan == false
                    ) {
                        $existing->update($supplier_data);
                        $isUpdated = true;
                    }
                } else {
                    GaVisitorTransaction::create($supplier_data);
                    $isNewRecord = true;
                }
            });

            if ($isCardInUse) {
                return response()->json([
                    'message' => 'Nomor kartu ini masih digunakan oleh visitor lain. Silakan gunakan kartu yang lain.'
                ], 400);
            }

            $message = 'Data Transporter tidak diubah.';
            if ($isNewRecord) {
                $message = 'Data Transporter berhasil disimpan! ID: ' . $supplier_data['trnvisitorid'];
            } elseif ($isUpdated) {
                $message = 'Data Transporter berhasil diperbarui! ID: ' . $supplier_data['trnvisitorid'];
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


    // 
    public function kembali_kartu(Request $request)
    {
        $request->validate([
            'trnvisitorid' => 'required|string|max:50'
        ]);

        try {
            $visitor = GaVisitorTransaction::where('trnvisitorid', $request->trnvisitorid)->first();
            //     ->whereNull('dateout')->where('kartu_dikembalikan', false)

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
