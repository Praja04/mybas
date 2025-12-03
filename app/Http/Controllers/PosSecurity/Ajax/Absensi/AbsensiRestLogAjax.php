<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\Absensi\AbsensiRestLog;
use App\Models\PosSecurity\Absensi\GateAccessLog;
use App\Models\PosSecurity\GaVisitorTransaction;
use App\Models\PosSecurity\GaVisitorVendorTransaction;
use Illuminate\Support\Facades\Cache;

// Models
use Illuminate\Support\Facades\Validator;

class AbsensiRestLogAjax extends Controller
{
    // public function search(Request $request)
    // {
    //   $now = Carbon::now();
    //   $keyword = $request->input('keyword');

    //   $validator = Validator::make($request->all(), [
    //     'keyword' => 'required|string|max:50',
    //   ]);

    //   if ($validator->fails()) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Data tidak valid.',
    //     ], 422);
    //   }

    //   // 🔍 1. Cek: apakah ini kartu Security?
    //   $securityResult = $this->cari_security($keyword);

    //   if ($securityResult && !empty($securityResult['data_detail'])) {
    //     $securityData = $securityResult['data_detail'];

    //     if (in_array(strtoupper(trim($securityData['dept'])), ['SECURITY', 'SEC'])) {
    //       // 🔎 Cek: apakah ada visitor yang baru tap (dalam 5 menit)?
    //       $visitorTrn = null;
    //       $visitorNama = null;

    //       foreach (['ga_visitor_transactions', 'ga_visitor_vendor_transactions'] as $origin) {
    //         $key = "gate_pending_visitor_{$keyword}_{$origin}";
    //         $cached = Cache::get($key);
    //         if ($cached && now()->diffInMinutes($cached['tapped_at']) < 5) {
    //           $visitorTrn = $cached['visitor']->trnvisitorid;
    //           $visitorNama = $cached['visitor']->namavisitor;
    //           break;
    //         }
    //       }

    //       // 🔥 Format data security (sama seperti visitor)
    //       $formattedData = [
    //         'trnvisitorid' => 'SEC-' . $securityData['nik'],
    //         'nama' => $securityData['nama'],
    //         'perusahaan' => 'INTERNAL - SECURITY',
    //         'jenis_kunjungan' => 'PETUGAS SECURITY',
    //         'no_polisi' => '-',
    //         'keperluan' => 'Verifikasi Akses Gerbang',
    //         'status_istirahat' => '-',
    //         'next_action' => '-',
    //         'status_display' => 'AKTIF',
    //         'foto_url' => asset($securityData['foto_path']),
    //         'source' => 'security',
    //         'source_detail' => [
    //           'type' => 'security',
    //           'asal_perusahaan' => 'INTERNAL',
    //           'penanggung_jawab' => 'GA / SECURITY',
    //           'no_ktp_sim' => $securityData['nik'],
    //           'no_kartu' => $securityData['idcard'],
    //           'waktu_masuk' => now()->format('H:i'),
    //           'lokasi_tujuan' => 'GERBANG UTAMA',
    //           'catatan' => 'Petugas Keamanan',
    //           'jumlah_tamu' => 1,
    //           'no_hp' => '-',
    //           'nama_kernet' => '-',
    //           'tgl_lahir' => '-',
    //           'gate' => 'POS01',
    //           'plant' => '1001',
    //           'created_at' => now()->format('d-m-Y H:i'),
    //         ],
    //         'status_kartu' => 'aktif',
    //         'status_display' => 'AKTIF',
    //       ];

    //       // ✅ SIMPAN LOG AKSES GATE SECURITY
    //       GateAccessLog::create([
    //         'nik' => $securityData['nik'],
    //         'nama' => $securityData['nama'],
    //         'dept' => $securityData['dept'],
    //         'id_card' => $securityData['idcard'],
    //         'visitor_trn' => $visitorTrn,
    //         'visitor_nama' => $visitorNama,
    //         'gate' => 'POS02', // bisa dari request nanti
    //         'waktu' => $now,
    //       ]);

    //       return response()->json([
    //         'success' => true,
    //         'message' => 'Data petugas security ditemukan',
    //         'type' => 'security',
    //         'data' => $formattedData,
    //       ]);
    //     } else {
    //       return response()->json([
    //         'success' => false,
    //         'message' => 'Akses Ditolak: Bukan Petugas Security',
    //       ]);
    //     }
    //   }

    //   // 🔍 2. Bukan security → cek visitor
    //   $visitorData = $this->findVisitor($keyword);

    //   if (!$visitorData) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu tidak ditemukan.',
    //     ]);
    //   }

    //   [$visitor, $sourceOrigin] = $visitorData;

    //   // Cek: apakah kartu sudah dikembalikan?
    //   if ((int)$visitor->kartu_dikembalikan === 1) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu sudah dikembalikan atau kunjungan selesai.',
    //     ]);
    //   }

    //   // Cek: apakah visitor diblokir?
    //   if ($this->isGloballyBlacklisted($visitor)) {
    //     $this->logBlacklist($visitor->trnvisitorid, $now);
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
    //     ]);
    //   }

    //   // ✅ Simpan visitor di cache (timeout 5 menit)
    //   $pendingKey = "gate_pending_visitor_{$keyword}_{$sourceOrigin}";
    //   Cache::put($pendingKey, [
    //     'visitor' => $visitor,
    //     'source_origin' => $sourceOrigin,
    //     'tapped_at' => $now,
    //   ], 300);

    //   // ✅ Proses absen istirahat (in/out) + simpan log
    //   return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
    //     return $this->handleAbsensi($visitor, $sourceOrigin, $now);
    //   }, 3);
    // }

    // public function search(Request $request)
    // {
    //   $now = Carbon::now();
    //   $keyword = $request->input('keyword');

    //   $validator = Validator::make($request->all(), [
    //     'keyword' => 'required|string|max:50',
    //   ]);

    //   if ($validator->fails()) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Data tidak valid.',
    //     ], 422);
    //   }

    //   // 🔍 1. Cek: apakah ini kartu Security?
    //   $securityResult = $this->cari_security($keyword);

    //   if ($securityResult && !empty($securityResult['data_detail'])) {
    //     $securityData = $securityResult['data_detail'];

    //     if (in_array(strtoupper(trim($securityData['dept'])), ['SECURITY', 'SEC'])) {
    //       // 🔎 Cek: apakah ada visitor yang baru tap (dalam 5 menit)?
    //       $visitorTrn = null;
    //       $visitorNama = null;

    //       $cached = Cache::get('last_tapped_visitor');
    //       if ($cached && now()->diffInMinutes($cached['tapped_at']) < 5) {
    //         $visitorTrn = $cached['visitor']->trnvisitorid;
    //         $visitorNama = $cached['visitor']->namavisitor;
    //       }

    //       // 🔥 Format data security (sama seperti visitor)
    //       $formattedData = [
    //         'trnvisitorid' => 'SEC-' . $securityData['nik'],
    //         'nama' => $securityData['nama'],
    //         'perusahaan' => 'INTERNAL - SECURITY',
    //         'jenis_kunjungan' => 'PETUGAS SECURITY',
    //         'no_polisi' => '-',
    //         'keperluan' => 'Verifikasi Akses Gerbang',
    //         'status_istirahat' => '-',
    //         'next_action' => '-',
    //         'status_display' => 'AKTIF',
    //         'foto_url' => asset($securityData['foto_path']),
    //         'source' => 'security',
    //         'source_detail' => [
    //           'type' => 'security',
    //           'asal_perusahaan' => 'INTERNAL',
    //           'penanggung_jawab' => 'GA / SECURITY',
    //           'no_ktp_sim' => $securityData['nik'],
    //           'no_kartu' => $securityData['idcard'],
    //           'waktu_masuk' => now()->format('H:i'),
    //           'lokasi_tujuan' => 'GERBANG UTAMA',
    //           'catatan' => 'Petugas Keamanan',
    //           'jumlah_tamu' => 1,
    //           'no_hp' => '-',
    //           'nama_kernet' => '-',
    //           'tgl_lahir' => '-',
    //           'gate' => 'POS01',
    //           'plant' => '1001',
    //           'created_at' => now()->format('d-m-Y H:i'),
    //         ],
    //         'status_kartu' => 'aktif',
    //         'status_display' => 'AKTIF',
    //       ];

    //       // ✅ SIMPAN LOG AKSES GATE SECURITY
    //       GateAccessLog::create([
    //         'nik'          => $securityData['nik'],
    //         'nama'         => $securityData['nama'],
    //         'dept'         => $securityData['dept'],
    //         'id_card'      => $securityData['idcard'],
    //         'visitor_trn'  => $visitorTrn,
    //         'visitor_nama' => $visitorNama,
    //         'gate'         => 'POS02', // bisa dari request nanti
    //         'waktu'        => $now,
    //       ]);

    //       return response()->json([
    //         'success' => true,
    //         'message' => 'Data petugas security ditemukan',
    //         'type' => 'security',
    //         'data' => $formattedData,
    //       ]);
    //     } else {
    //       return response()->json([
    //         'success' => false,
    //         'message' => 'Akses Ditolak: Bukan Petugas Security',
    //       ]);
    //     }
    //   }

    //   // 🔍 2. Bukan security → cek visitor
    //   $visitorData = $this->findVisitor($keyword);

    //   if (!$visitorData) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu tidak ditemukan.',
    //     ]);
    //   }

    //   [$visitor, $sourceOrigin] = $visitorData;

    //   // Cek: apakah kartu sudah dikembalikan?
    //   if ((int)$visitor->kartu_dikembalikan === 1) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu sudah dikembalikan atau kunjungan selesai.',
    //     ]);
    //   }

    //   // Cek: apakah visitor diblokir?
    //   if ($this->isGloballyBlacklisted($visitor)) {
    //     $this->logBlacklist($visitor->trnvisitorid, $now);
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
    //     ]);
    //   }

    //   // ✅ Simpan visitor di cache (timeout 5 menit)
    //   Cache::put('last_tapped_visitor', [
    //     'visitor' => $visitor,
    //     'source_origin' => $sourceOrigin,
    //     'tapped_at' => $now,
    //   ], 300);

    //   // ✅ Proses absen istirahat (in/out) + simpan log
    //   return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
    //     return $this->handleAbsensi($visitor, $sourceOrigin, $now);
    //   }, 3);
    // }

    // public function search(Request $request)
    // {
    //   $now = Carbon::now();
    //   $keyword = $request->input('keyword');

    //   $validator = Validator::make($request->all(), [
    //     'keyword' => 'required|string|max:50',
    //   ]);

    //   if ($validator->fails()) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Data tidak valid.',
    //     ], 422);
    //   }

    //   // 🔍 1. Cek: apakah ini kartu Security?
    //   $securityResult = $this->cari_security($keyword);

    //   if ($securityResult && !empty($securityResult['data_detail'])) {
    //     $securityData = $securityResult['data_detail'];

    //     if (in_array(strtoupper(trim($securityData['dept'])), ['SECURITY', 'SEC'])) {
    //       // 🔎 Ambil semua visitor yang sedang pending di gate
    //       $pendingKey = "pending_visitors_POS01"; // 👉 gate bisa dibuat dinamis
    //       $pending = Cache::get($pendingKey, []);

    //       $validVisitors = collect($pending)
    //         ->filter(fn($v) => now()->diffInMinutes($v['tapped_at']) < 5)
    //         ->pluck('visitor');

    //       // 🔥 Format data security (sama seperti visitor)
    //       $formattedData = [
    //         'trnvisitorid' => 'SEC-' . $securityData['nik'],
    //         'nama' => $securityData['nama'],
    //         'perusahaan' => 'INTERNAL - SECURITY',
    //         'jenis_kunjungan' => 'PETUGAS SECURITY',
    //         'no_polisi' => '-',
    //         'keperluan' => 'Verifikasi Akses Gerbang',
    //         'status_istirahat' => '-',
    //         'next_action' => '-',
    //         'status_display' => 'AKTIF',
    //         'foto_url' => asset($securityData['foto_path']),
    //         'source' => 'security',
    //         'source_detail' => [
    //           'type' => 'security',
    //           'asal_perusahaan' => 'INTERNAL',
    //           'penanggung_jawab' => 'GA / SECURITY',
    //           'no_ktp_sim' => $securityData['nik'],
    //           'no_kartu' => $securityData['idcard'],
    //           'waktu_masuk' => now()->format('H:i'),
    //           'lokasi_tujuan' => 'GERBANG UTAMA',
    //           'catatan' => 'Petugas Keamanan',
    //           'jumlah_tamu' => 1,
    //           'no_hp' => '-',
    //           'nama_kernet' => '-',
    //           'tgl_lahir' => '-',
    //           'gate' => 'POS01',
    //           'plant' => '1001',
    //           'created_at' => now()->format('d-m-Y H:i'),
    //         ],
    //         'status_kartu' => 'aktif',
    //         'status_display' => 'AKTIF',
    //       ];

    //       // ✅ SIMPAN LOG AKSES GATE SECURITY (1x + relasi semua visitor)
    //       if ($validVisitors->isNotEmpty()) {
    //         foreach ($validVisitors as $v) {
    //           GateAccessLog::create([
    //             'nik'          => $securityData['nik'],
    //             'nama'         => $securityData['nama'],
    //             'dept'         => $securityData['dept'],
    //             'id_card'      => $securityData['idcard'],
    //             'visitor_trn'  => $v->trnvisitorid,
    //             'visitor_nama' => $v->namavisitor,
    //             'gate'         => 'POS02', // bisa dari request
    //             'waktu'        => $now,
    //           ]);
    //         }
    //         // hapus queue setelah diproses supaya gak dobel
    //         Cache::forget($pendingKey);
    //       } else {
    //         // kalau gak ada visitor, tetap simpan log security aja
    //         GateAccessLog::create([
    //           'nik'          => $securityData['nik'],
    //           'nama'         => $securityData['nama'],
    //           'dept'         => $securityData['dept'],
    //           'id_card'      => $securityData['idcard'],
    //           'visitor_trn'  => null,
    //           'visitor_nama' => null,
    //           'gate'         => 'POS02',
    //           'waktu'        => $now,
    //         ]);
    //       }

    //       return response()->json([
    //         'success' => true,
    //         'message' => 'Data petugas security ditemukan',
    //         'type'    => 'security',
    //         'data'    => $formattedData,
    //       ]);
    //     } else {
    //       return response()->json([
    //         'success' => false,
    //         'message' => 'Akses Ditolak: Bukan Petugas Security',
    //       ]);
    //     }
    //   }

    //   // 🔍 2. Bukan security → cek visitor
    //   $visitorData = $this->findVisitor($keyword);

    //   if (!$visitorData) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu tidak ditemukan.',
    //     ]);
    //   }

    //   [$visitor, $sourceOrigin] = $visitorData;

    //   // Cek: apakah kartu sudah dikembalikan?
    //   if ((int)$visitor->kartu_dikembalikan === 1) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu sudah dikembalikan atau kunjungan selesai.',
    //     ]);
    //   }

    //   // Cek: apakah visitor diblokir?
    //   if ($this->isGloballyBlacklisted($visitor)) {
    //     $this->logBlacklist($visitor->trnvisitorid, $now);
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
    //     ]);
    //   }

    //   // ✅ Tambahkan visitor ke queue (antrian cache per gate)
    //   $pendingKey = "pending_visitors_POS01"; // gate bisa dibuat dinamis
    //   $pending = Cache::get($pendingKey, []);
    //   $pending[] = [
    //     'visitor'   => $visitor,
    //     'tapped_at' => $now,
    //   ];
    //   Cache::put($pendingKey, $pending, 300);

    //   // ✅ Proses absen istirahat (in/out) + simpan log
    //   return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
    //     return $this->handleAbsensi($visitor, $sourceOrigin, $now);
    //   }, 3);
    // }


    // v1
    // public function search(Request $request)
    // {
    //   $now = Carbon::now();
    //   $keyword = $request->input('keyword');

    //   $validator = Validator::make($request->all(), [
    //     'keyword' => 'required|string|max:50',
    //   ]);

    //   if ($validator->fails()) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Data tidak valid.',
    //     ], 422);
    //   }

    //   // 🔍 1. Cek kartu Security
    //   $securityResult = $this->cari_security($keyword);
    //   if ($securityResult && !empty($securityResult['data_detail'])) {
    //     $securityData = $securityResult['data_detail'];

    //     if (in_array(strtoupper(trim($securityData['dept'])), ['SECURITY', 'SEC'])) {
    //       $pendingKey = "pending_visitors_POS01";
    //       $pending = Cache::get($pendingKey, []);

    //       // filter visitor masih valid (5 menit terakhir)
    //       $validVisitors = collect($pending)
    //         ->filter(fn($v) => $now->diffInMinutes($v['tapped_at']) < 5)
    //         ->pluck('visitor');

    //       // Format data security
    //       $formattedData = [
    //         'trnvisitorid' => 'SEC-' . $securityData['nik'],
    //         'nama' => $securityData['nama'],
    //         'perusahaan' => 'INTERNAL - SECURITY',
    //         'jenis_kunjungan' => 'PETUGAS SECURITY',
    //         'no_polisi' => '-',
    //         'keperluan' => 'Verifikasi Akses Gerbang dan Buka Gerbang',
    //         'status_istirahat' => '-',
    //         'next_action' => '-',
    //         'status_display' => 'AKTIF',
    //         'foto_url' => asset($securityData['foto_path']),
    //         'source' => 'security',
    //         'source_detail' => [
    //           'type' => 'security',
    //           'asal_perusahaan' => 'INTERNAL',
    //           'penanggung_jawab' => 'GA / SECURITY',
    //           'no_ktp_sim' => $securityData['nik'],
    //           'no_kartu' => $securityData['idcard'],
    //           'waktu_masuk' => $now->format('H:i'),
    //           'lokasi_tujuan' => 'GERBANG UTAMA',
    //           'catatan' => 'Petugas Keamanan',
    //           'jumlah_tamu' => 1,
    //           'no_hp' => '-',
    //           'nama_kernet' => '-',
    //           'tgl_lahir' => '-',
    //           'gate' => 'POS01',
    //           'plant' => '1001',
    //           'created_at' => $now->format('d-m-Y H:i'),
    //         ],
    //         'status_kartu' => 'aktif',
    //         'status_display' => 'AKTIF',
    //       ];

    //       // ✅ Simpan log security + relasi semua visitor
    //       if ($validVisitors->isNotEmpty()) {
    //         foreach ($validVisitors as $v) {
    //           GateAccessLog::create([
    //             'nik'          => $securityData['nik'],
    //             'nama'         => $securityData['nama'],
    //             'dept'         => $securityData['dept'],
    //             'id_card'      => $securityData['idcard'],
    //             'visitor_trn'  => $v->trnvisitorid,
    //             'visitor_nama' => $v->namavisitor,
    //             'foto_url'     => asset($securityData['foto_path']),
    //             'gate'         => 'POS02',
    //             'waktu'        => $now,
    //           ]);
    //         }
    //         Cache::forget($pendingKey);
    //       } else {
    //         GateAccessLog::create([
    //           'nik'          => $securityData['nik'],
    //           'nama'         => $securityData['nama'],
    //           'dept'         => $securityData['dept'],
    //           'id_card'      => $securityData['idcard'],
    //           'visitor_trn'  => null,
    //           'visitor_nama' => null,
    //           'gate'         => 'POS01',
    //           'waktu'        => $now,
    //         ]);
    //       }

    //       return response()->json([
    //         'success' => true,
    //         'message' => 'Data petugas security ditemukan',
    //         'type'    => 'security',
    //         'data'    => $formattedData,
    //       ]);
    //     }

    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Akses Ditolak: Bukan Petugas Security',
    //     ]);
    //   }

    //   // 🔍 2. Bukan security → cek visitor
    //   $visitorData = $this->findVisitor($keyword);
    //   if (!$visitorData) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu tidak ditemukan.',
    //     ]);
    //   }

    //   [$visitor, $sourceOrigin] = $visitorData;

    //   if ((int)$visitor->kartu_dikembalikan === 1) {
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Kartu sudah dikembalikan atau kunjungan selesai.',
    //     ]);
    //   }

    //   if ($this->isGloballyBlacklisted($visitor)) {
    //     $this->logBlacklist($visitor->trnvisitorid, $now);
    //     return response()->json([
    //       'success' => false,
    //       'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
    //     ]);
    //   }

    //   // ✅ Tambahkan / update visitor ke queue
    //   $pendingKey = "pending_visitors_POS01";
    //   $pending = Cache::get($pendingKey, []);

    //   $alreadyExists = false;
    //   foreach ($pending as &$entry) {
    //     if ($entry['visitor']->trnvisitorid === $visitor->trnvisitorid) {
    //       // 🚀 kalau sama, cek jeda minimal (10 detik misalnya)
    //       if ($now->diffInSeconds($entry['tapped_at']) < 5) {
    //         return response()->json([
    //           'success' => false,
    //           'message' => 'Tapping terlalu cepat, silakan tunggu beberapa 5 detik.',
    //         ]);
    //       }
    //       // update waktu
    //       $entry['tapped_at'] = $now;
    //       $alreadyExists = true;
    //       break;
    //     }
    //   }

    //   if (!$alreadyExists) {
    //     $pending[] = [
    //       'visitor'   => $visitor,
    //       'tapped_at' => $now,
    //     ];
    //   }

    //   Cache::put($pendingKey, $pending, 300);

    //   // ✅ Proses absen istirahat + log
    //   return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
    //     return $this->handleAbsensi($visitor, $sourceOrigin, $now);
    //   }, 3);
    // }

    // protected function findVisitor($keyword)
    // {
    //   $visitor = GaVisitorTransaction::where(function ($q) use ($keyword) {
    //     $q->where('trnvisitorid', $keyword)
    //       ->orWhere('no_ktp_sim', $keyword)
    //       ->orWhere('no_kartu', $keyword);
    //   })->whereNull('dateout')->orderBy('createdon', 'desc')->first();

    //   if ($visitor) {
    //     return [$visitor, 'ga_visitor_transactions'];
    //   }

    //   $visitor = GaVisitorVendorTransaction::where(function ($q) use ($keyword) {
    //     $q->where('trnvisitorid', $keyword)
    //       ->orWhere('no_ktp_sim', $keyword)
    //       ->orWhere('no_kartu', $keyword);
    //   })->whereNull('dateout')->orderBy('createdon', 'desc')->first();

    //   return $visitor ? [$visitor, 'ga_visitor_vendor_transactions'] : null;
    // }

    // protected function isGloballyBlacklisted($visitor)
    // {
    //   return DB::table('ga_lgtk_blacklist_identitas')
    //     ->where('aktif', 1)
    //     ->where(function ($q) use ($visitor) {
    //       $q->where('trnvisitorid', $visitor->trnvisitorid)
    //         ->orWhere('no_identitas', $visitor->no_ktp_sim);
    //     })
    //     ->exists();
    // }

    // protected function logBlacklist($trnvisitorid, $now)
    // {
    //   $blacklisted = DB::table('ga_lgtk_blacklist_identitas')
    //     ->where('aktif', 1)
    //     ->where('trnvisitorid', $trnvisitorid)
    //     ->orWhere('no_identitas', $trnvisitorid)
    //     ->first();

    //   if (!$blacklisted) return;

    //   $visitor = GaVisitorTransaction::where('trnvisitorid', $blacklisted->trnvisitorid)->first();
    //   $sourceOrigin = 'ga_visitor_transactions';

    //   if (!$visitor) {
    //     $visitor = GaVisitorVendorTransaction::where('trnvisitorid', $blacklisted->trnvisitorid)->first();
    //     $sourceOrigin = 'ga_visitor_vendor_transactions';
    //   }

    //   $fotoUrl = null;
    //   if ($visitor) {
    //     $fotoArray = json_decode($visitor->foto, true);
    //     $fotoUrl = is_array($fotoArray) && !empty($fotoArray) ? $fotoArray[0] : null;
    //   }

    //   AbsensiRestLog::create([
    //     'trnvisitorid' => $blacklisted->trnvisitorid ?? $trnvisitorid,
    //     'source_origin' => 'blacklist',
    //     'activity_type' => 'failed',
    //     'scan_time' => $now,
    //     'tanggal_log' => $now->toDateString(),
    //     'nama' => $blacklisted->nama ?? null,
    //     'no_ktp_sim' => $blacklisted->no_identitas ?? null,
    //     'namacomp' => $visitor->namacomp ?? null,
    //     'host' => $visitor->host ?? null,
    //     'purpose' => $visitor->keperluan ?? null,
    //     'foto' => $fotoUrl,
    //     'catatan' => $blacklisted->alasan_blacklist ?? 'Diblokir oleh sistem',
    //   ]);
    // }

    // protected function handleAbsensi($visitor, $sourceOrigin, $now)
    // {
    //   $gracePeriod = 30;
    //   $lastLog = AbsensiRestLog::where('trnvisitorid', $visitor->trnvisitorid)
    //     ->where('source_origin', $sourceOrigin)
    //     ->orderBy('scan_time', 'desc')
    //     ->lockForUpdate()
    //     ->first();

    //   $fotoUrl = $this->getFotoUrl($visitor);
    //   $activity = 'in';
    //   $statusText = 'Izin Keluar untuk Istirahat';

    //   if (!$lastLog) {
    //     return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
    //   }

    //   $diff = $now->diffInSeconds(Carbon::parse($lastLog->scan_time));
    //   $wait = $gracePeriod - $diff;

    //   if ($diff < $gracePeriod) {
    //     return response()->json([
    //       'success' => true,
    //       'message' => "Kamu sudah absen, silahkan tunggu {$wait} detik untuk absen lagi.",
    //       'data' => $this->formatVisitorData($visitor, $sourceOrigin, $lastLog->activity_type, $statusText),
    //     ]);
    //   }

    //   $activity = $lastLog->activity_type === 'in' ? 'out' : 'in';
    //   $statusText = $activity === 'out' ? 'Kembali dari Istirahat' : 'Izin Keluar untuk Istirahat';

    //   return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
    // }

    // protected function createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now)
    // {
    //   AbsensiRestLog::create([
    //     'trnvisitorid' => $visitor->trnvisitorid,
    //     'source_origin' => $sourceOrigin,
    //     'activity_type' => $activity,
    //     'scan_time' => $now,
    //     'tanggal_log' => $now->toDateString(),
    //     'no_kartu' => $visitor->no_kartu ?? null,
    //     'no_ktp_sim' => $visitor->no_ktp_sim ?? null,
    //     'nama' => $visitor->namavisitor ?? null,
    //     'namacomp' => $visitor->namacomp ?? null,
    //     'host' => $visitor->host ?? null,
    //     'hostdeptid' => $visitor->hostdeptid ?? null,
    //     'purpose' => $visitor->purpose ?? $visitor->keperluan ?? null,
    //     'nopol' => $visitor->nopol ?? null,
    //     'nohpdriver' => $visitor->nohpdriver ?? null,
    //     'nama_kernet' => $visitor->nama_kernet ?? null,
    //     'tgl_lahir' => $visitor->tgl_lahir ?? null,
    //     'imgvisitorpathin' => $visitor->imgvisitorpathin ?? null,
    //     'foto' => $fotoUrl,
    //   ]);

    //   return response()->json([
    //     'success' => true,
    //     'message' => 'Absen berhasil',
    //     'data' => $this->formatVisitorData($visitor, $sourceOrigin, $activity, $statusText),
    //   ]);
    // }

    // protected function getFotoUrl($visitor)
    // {
    //   $fotoJson = $visitor->foto ?? '[]';
    //   $fotoArray = json_decode($fotoJson, true);

    //   if (is_array($fotoArray) && !empty($fotoArray)) {
    //     return $fotoArray[0];
    //   }

    //   return 'https://via.placeholder.com/300';
    // }

    // protected function formatVisitorData($visitor, $sourceOrigin, $nextAction = 'in', $statusText = '')
    // {
    //   $fotoUrl = $this->getFotoUrl($visitor);

    //   return [
    //     'trnvisitorid' => $visitor->trnvisitorid,
    //     'nama' => $visitor->namavisitor,
    //     'perusahaan' => $visitor->namacomp ?? $visitor->perusahaan ?? '-',
    //     'jenis_kunjungan' => $sourceOrigin === 'ga_visitor_vendor_transactions' ? 'VENDOR' : 'TAMU',
    //     'no_polisi' => $visitor->nopol ?? '-',
    //     'keperluan' => $visitor->keperluan ?? $visitor->purpose ?? '-',
    //     'status_istirahat' => $nextAction === 'in' ? 'keluar' : 'masuk',
    //     'next_action' => $nextAction === 'in' ? 'out' : 'in',
    //     'status_display' => $statusText,
    //     'foto_url' => $fotoUrl,
    //     'source' => $sourceOrigin,
    //     'source_detail' => [
    //       'type' => $sourceOrigin === 'ga_visitor_vendor_transactions' ? 'vendor' : 'tamu',
    //       'asal_perusahaan' => $visitor->namacomp ?? '-',
    //       'penanggung_jawab' => $visitor->host ?? '-',
    //       'no_ktp_sim' => $visitor->no_ktp_sim ?? '-',
    //       'no_kartu' => $visitor->no_kartu ?? '-',
    //       'waktu_masuk' => $visitor->datein ? Carbon::parse($visitor->datein . ' ' . $visitor->timein)->format('H:i') : '-',
    //       'lokasi_tujuan' => $visitor->gateidin ?? '-',
    //       'catatan' => $visitor->keperluan ?? $visitor->purpose ?? '-',
    //       'jumlah_tamu' => $visitor->sumpeople ?? 1,
    //       'no_hp' => $visitor->nohpdriver ?? '-',
    //       'nama_kernet' => $visitor->nama_kernet ?? '-',
    //       'tgl_lahir' => $visitor->tgl_lahir ? Carbon::parse($visitor->tgl_lahir)->format('d-m-Y') : '-',
    //       'gate' => $visitor->gateidin ?? '-',
    //       'plant' => $visitor->plant ?? '-',
    //       'created_at' => $visitor->created_at ? $visitor->created_at->format('d-m-Y H:i') : '-',
    //     ],
    //     'status_kartu' => $visitor->kartu_dikembalikan ? 'dikembalikan' : 'aktif',
    //     'status_display' => $visitor->kartu_dikembalikan ? 'Kartu Sudah Dikembalikan' : 'Kartu Aktif',
    //   ];
    // }

    // // ========================
    // // SECURITY DATA CHECKER
    // // ========================

    // public function cari_security($id_card)
    // {
    //   if (empty($id_card)) {
    //     return [
    //       'data' => ['pesan' => 'Indikasi scanner rusak', 'notif' => 'failed', 'status' => 'error'],
    //       'data_detail' => null,
    //       'raw_data' => null
    //     ];
    //   }

    //   $id_card = (int)$id_card;
    //   if ($id_card == 1215) {
    //     $id_card = 2421193138;
    //   }

    //   $MSIDCARD = DB::connection('192.168.154.44')
    //     ->table('MSIDCARD')
    //     ->select('NIK', 'EMPNM', 'CREATEDON', 'DEPTID', 'CARDNODEVICE', 'BARCODE', 'FOTOBLOB')
    //     ->whereNotNull('FOTOTYPE')
    //     ->where('CARDNODEVICE', $id_card)
    //     ->orderBy('CREATEDON', 'desc')
    //     ->orderByRaw('NIK desc')
    //     ->first();

    //   if (!$MSIDCARD) {
    //     return [
    //       'data' => ['pesan' => 'Kartu Tidak Terdaftar', 'notif' => 'failed', 'status' => 'tidak-terdaftar'],
    //       'data_detail' => null,
    //       'raw_data' => null
    //     ];
    //   }

    //   $nik = $MSIDCARD->NIK;
    //   $nama = $MSIDCARD->EMPNM ?? 'SEC';
    //   $absenTime = Carbon::now();

    //   $namaClean = preg_replace('/[\s\/\\\\<>?*:|"()]+/', '_', $nama);
    //   $namaClean = trim($namaClean, '_');
    //   $imageName = "{$nik}_{$namaClean}.jpeg";
    //   $storagePath = 'public/ga/monitoring/security/' . $imageName;
    //   $publicPath = 'storage/ga/monitoring/security/' . $imageName;

    //   if ($MSIDCARD->FOTOBLOB) {
    //     $fullPath = storage_path('app/' . $storagePath);
    //     $dir = dirname($fullPath);
    //     if (!file_exists($dir)) {
    //       mkdir($dir, 0777, true);
    //     }
    //     file_put_contents($fullPath, $MSIDCARD->FOTOBLOB);
    //   }

    //   $vendorData = DB::connection('newpas')
    //     ->table('hr_vendor')
    //     ->where('nik', $nik)
    //     ->first();

    //   $isVendor = $vendorData !== null;

    //   $dept = $isVendor && !empty($vendorData->departement)
    //     ? $vendorData->departement
    //     : $MSIDCARD->DEPTID;

    //   $isSecurity = in_array(strtoupper(trim($dept)), ['SECURITY', 'SEC']);

    //   $fromStaff = false;
    //   if (!$isVendor) {
    //     $staffData = DB::connection('newpas')
    //       ->table('hr_karyawan')
    //       ->select('kode_bagian AS dept', 'staff')
    //       ->where('active', 'Y')
    //       ->where('nik', $nik)
    //       ->first();

    //     if ($staffData && $staffData->staff === 'Y') {
    //       $dept = $staffData->dept;
    //       $isSecurity = in_array(strtoupper(trim($dept)), ['SECURITY', 'SEC']);
    //       $fromStaff = true;
    //     }
    //   }

    //   $dataDetail = [
    //     'nik' => $MSIDCARD->NIK,
    //     'idcard' => $MSIDCARD->CARDNODEVICE,
    //     'nama' => $MSIDCARD->EMPNM,
    //     'dept' => $dept,
    //     'foto_path' => $publicPath,
    //   ];

    //   $rawData = [
    //     'nik' => $dataDetail['nik'],
    //     'nama' => $dataDetail['nama'],
    //     'dept' => $dataDetail['dept'],
    //     'id_card' => $dataDetail['idcard'],
    //     'absen_time' => $absenTime->format('Y-m-d H:i:s'),
    //     'status' => $isSecurity ? 'berhasil' : 'gagal',
    //     'notif' => $isSecurity ? 'success' : 'failed',
    //     'pesan' => $isSecurity
    //       ? ($isVendor ? 'Vendor SECURITY ditemukan' : 'Staff SECURITY ditemukan')
    //       : 'Bukan petugas security',
    //     'staff' => $isVendor ? 'N' : ($fromStaff ? 'Y' : 'N/A'),
    //     'location' => 'security',
    //     'image_name' => $imageName,
    //     'foto_path' => $publicPath,
    //   ];

    //   if (!$isSecurity) {
    //     return [
    //       'data' => [
    //         'pesan' => 'Akses Ditolak: Bukan Petugas Security',
    //         'notif' => 'failed',
    //         'status' => 'bukan-security'
    //       ],
    //       'data_detail' => $dataDetail,
    //       'raw_data' => $rawData
    //     ];
    //   }

    //   if (!$isVendor && !$fromStaff) {
    //     return [
    //       'data' => [
    //         'pesan' => 'Akses Ditolak: Bukan Vendor atau Staff Tetap',
    //         'notif' => 'failed',
    //         'status' => 'tidak-diizinkan'
    //       ],
    //       'data_detail' => $dataDetail,
    //       'raw_data' => $rawData
    //     ];
    //   }

    //   return [
    //     'data' => [
    //       'pesan' => 'Petugas Security ditemukan',
    //       'notif' => 'success',
    //       'status' => 'berhasil'
    //     ],
    //     'data_detail' => $dataDetail,
    //     'raw_data' => $rawData
    //   ];
    // }

    // v2
    public function search(Request $request)
    {
        $now = Carbon::now();
        $keyword = $request->input('keyword');

        // Log::channel('ga_security_telegram')->debug('Search request diterima', [
        //   'keyword' => $keyword,
        //   'ip' => $request->ip(),
        //   'user_agent' => $request->userAgent(),
        // ]);

        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            // Log::channel('ga_security_telegram')->warning('Validasi gagal', [
            //   'keyword' => $keyword,
            //   'errors' => $validator->errors()->toArray(),
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
            ], 422);
        }

        // Log::channel('ga_security_telegram')->info('Validasi berhasil, lanjut pencarian', ['keyword' => $keyword]);

        // 🔍 1. Cek kartu Security
        $securityResult = $this->cari_security($keyword);

        if ($securityResult && !empty($securityResult['data_detail'])) {
            $securityData = $securityResult['data_detail'];

            if (in_array(strtoupper(trim($securityData['dept'])), ['SECURITY', 'SEC'])) {
                // Log::channel('ga_security_telegram')->info('Petugas Security valid', ['nik' => $securityData['nik'], 'nama' => $securityData['nama']]);

                $pendingKey = "pending_visitors_POS01";
                $pending = Cache::get($pendingKey, []);

                $validVisitors = collect($pending)
                    ->filter(fn($v) => $now->diffInMinutes($v['tapped_at']) < 5)
                    ->pluck('visitor');

                $formattedData = [
                    'trnvisitorid' => 'SEC-' . $securityData['nik'],
                    'nama' => $securityData['nama'],
                    'perusahaan' => 'INTERNAL - SECURITY',
                    'jenis_kunjungan' => 'PETUGAS SECURITY',
                    'no_polisi' => '-',
                    'keperluan' => 'Verifikasi Akses Gerbang dan Buka Gerbang',
                    'status_istirahat' => '-',
                    'next_action' => '-',
                    'status_display' => 'AKTIF',
                    'foto_url' => asset($securityData['foto_path']),
                    'source' => 'security',
                    'source_detail' => [
                        'type' => 'security',
                        'asal_perusahaan' => 'INTERNAL',
                        'penanggung_jawab' => 'GA / SECURITY',
                        'no_ktp_sim' => $securityData['nik'],
                        'no_kartu' => $securityData['idcard'],
                        'waktu_masuk' => $now->format('H:i'),
                        'lokasi_tujuan' => 'GERBANG UTAMA',
                        'catatan' => 'Petugas Keamanan',
                        'jumlah_tamu' => 1,
                        'no_hp' => '-',
                        'nama_kernet' => '-',
                        'tgl_lahir' => '-',
                        'gate' => 'POS01',
                        'plant' => '1001',
                        'created_at' => $now->format('d-m-Y H:i'),
                    ],
                    'status_kartu' => 'aktif',
                    'status_display' => 'AKTIF',
                ];

                if ($validVisitors->isNotEmpty()) {
                    foreach ($validVisitors as $v) {
                        GateAccessLog::create([
                            'nik'          => $securityData['nik'],
                            'nama'         => $securityData['nama'],
                            'dept'         => $securityData['dept'],
                            'id_card'      => $securityData['idcard'],
                            'visitor_trn'  => $v->trnvisitorid,
                            'visitor_nama' => $v->namavisitor,
                            'foto_url'     => asset($securityData['foto_path']),
                            'gate'         => 'POS02',
                            'waktu'        => $now,
                        ]);
                    }
                    Cache::forget($pendingKey);
                    // Log::channel('ga_security_telegram')->info('Visitor terkait diproses dan cache dibersihkan', ['count' => $validVisitors->count()]);
                } else {
                    GateAccessLog::create([
                        'nik'          => $securityData['nik'],
                        'nama'         => $securityData['nama'],
                        'dept'         => $securityData['dept'],
                        'id_card'      => $securityData['idcard'],
                        'visitor_trn'  => null,
                        'visitor_nama' => null,
                        'gate'         => 'POS01',
                        'waktu'        => $now,
                    ]);
                    // Log::channel('ga_security_telegram')->info('Akses tanpa visitor terkait', ['nik' => $securityData['nik']]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data petugas security ditemukan',
                    'type'    => 'security',
                    'data'    => $formattedData,
                ]);
            }

            // Log::channel('ga_security_telegram')->warning('Bukan petugas security', [
            //   'nik' => $securityData['nik'],
            //   'dept' => $securityData['dept'],
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: Bukan Petugas Security',
            ]);
        }

        // 🔍 2. Bukan security → cek visitor
        // Log::channel('ga_security_telegram')->debug('Cek visitor karena bukan security', ['keyword' => $keyword]);

        $visitorData = $this->findVisitor($keyword);
        if (!$visitorData) {
            // Log::channel('ga_security_telegram')->warning('Visitor tidak ditemukan', ['keyword' => $keyword]);
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak ditemukan.',
            ]);
        }

        [$visitor, $sourceOrigin] = $visitorData;

        // Log::channel('ga_security_telegram')->info('Visitor ditemukan', [
        //   'trnvisitorid' => $visitor->trnvisitorid,
        //   'nama' => $visitor->namavisitor,
        //   'source' => $sourceOrigin,
        // ]);

        if ((int)$visitor->kartu_dikembalikan === 1) {
            // Log::channel('ga_security_telegram')->warning('Kartu sudah dikembalikan', ['trnvisitorid' => $visitor->trnvisitorid]);
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah dikembalikan atau kunjungan selesai.',
            ]);
        }

        if ($this->isGloballyBlacklisted($visitor)) {
            // Log::channel('ga_security_telegram')->error('Visitor diblokir - blacklist', [
            //   'trnvisitorid' => $visitor->trnvisitorid,
            //   'no_ktp_sim' => $visitor->no_ktp_sim,
            // ]);
            $this->logBlacklist($visitor->trnvisitorid, $now);
            return response()->json([
                'success' => false,
                'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
            ]);
        }

        // ✅ Tambahkan / update visitor ke queue
        $pendingKey = "pending_visitors_POS01";
        $pending = Cache::get($pendingKey, []);
        $alreadyExists = false;

        foreach ($pending as &$entry) {
            if ($entry['visitor']->trnvisitorid === $visitor->trnvisitorid) {
                if ($now->diffInSeconds($entry['tapped_at']) < 5) {
                    // Log::channel('ga_security_telegram')->warning('Tapping terlalu cepat', [
                    //   'trnvisitorid' => $visitor->trnvisitorid,
                    //   'interval' => $now->diffInSeconds($entry['tapped_at']),
                    // ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Tapping terlalu cepat, silakan tunggu beberapa 5 detik.',
                    ]);
                }
                $entry['tapped_at'] = $now;
                $alreadyExists = true;
                break;
            }
        }

        if (!$alreadyExists) {
            $pending[] = [
                'visitor'   => $visitor,
                'tapped_at' => $now,
            ];
        }

        Cache::put($pendingKey, $pending, 300);
        // Log::channel('ga_security_telegram')->debug('Visitor ditambahkan ke antrian', [
        //   'trnvisitorid' => $visitor->trnvisitorid,
        //   'queue_size' => count($pending),
        // ]);

        // ✅ Proses absen istirahat
        return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
            return $this->handleAbsensi($visitor, $sourceOrigin, $now);
        }, 3);
    }

    protected function findVisitor($keyword)
    {
        $visitor = GaVisitorTransaction::where(function ($q) use ($keyword) {
            $q->where('trnvisitorid', $keyword)
                ->orWhere('no_ktp_sim', $keyword)
                ->orWhere('no_kartu', $keyword);
        })->whereNull('dateout')->orderBy('createdon', 'desc')->first();

        if ($visitor) {
            // Log::channel('ga_security_telegram')->debug('Visitor ditemukan di ga_visitor_transactions', ['trnvisitorid' => $visitor->trnvisitorid]);
            return [$visitor, 'ga_visitor_transactions'];
        }

        $visitor = GaVisitorVendorTransaction::where(function ($q) use ($keyword) {
            $q->where('trnvisitorid', $keyword)
                ->orWhere('no_ktp_sim', $keyword)
                ->orWhere('no_kartu', $keyword);
        })->whereNull('dateout')->orderBy('createdon', 'desc')->first();

        if ($visitor) {
            // Log::channel('ga_security_telegram')->debug('Vendor visitor ditemukan di ga_visitor_vendor_transactions', ['trnvisitorid' => $visitor->trnvisitorid]);
            return [$visitor, 'ga_visitor_vendor_transactions'];
        }

        return null;
    }

    protected function isGloballyBlacklisted($visitor)
    {
        $isBlacklisted = DB::table('ga_lgtk_blacklist_identitas')
            ->where('aktif', 1)
            ->where(function ($q) use ($visitor) {
                $q->where('trnvisitorid', $visitor->trnvisitorid)
                    ->orWhere('no_identitas', $visitor->no_ktp_sim);
            })
            ->exists();

        if ($isBlacklisted) {
            // Log::channel('ga_security_telegram')->warning('Visitor terdeteksi blacklist', [
            //   'trnvisitorid' => $visitor->trnvisitorid,
            //   'no_ktp_sim' => $visitor->no_ktp_sim,
            // ]);
        }

        return $isBlacklisted;
    }

    protected function logBlacklist($trnvisitorid, $now)
    {
        $blacklisted = DB::table('ga_lgtk_blacklist_identitas')
            ->where('aktif', 1)
            ->where('trnvisitorid', $trnvisitorid)
            ->orWhere('no_identitas', $trnvisitorid)
            ->first();

        if (!$blacklisted) return;

        $visitor = GaVisitorTransaction::where('trnvisitorid', $blacklisted->trnvisitorid)->first();
        $sourceOrigin = 'ga_visitor_transactions';

        if (!$visitor) {
            $visitor = GaVisitorVendorTransaction::where('trnvisitorid', $blacklisted->trnvisitorid)->first();
            $sourceOrigin = 'ga_visitor_vendor_transactions';
        }

        $fotoUrl = null;
        if ($visitor) {
            $fotoArray = json_decode($visitor->foto, true);
            $fotoUrl = is_array($fotoArray) && !empty($fotoArray) ? $fotoArray[0] : null;
        }

        AbsensiRestLog::create([
            'trnvisitorid' => $blacklisted->trnvisitorid ?? $trnvisitorid,
            'source_origin' => 'blacklist',
            'activity_type' => 'failed',
            'scan_time' => $now,
            'tanggal_log' => $now->toDateString(),
            'nama' => $blacklisted->nama ?? null,
            'no_ktp_sim' => $blacklisted->no_identitas ?? null,
            'namacomp' => $visitor->namacomp ?? null,
            'host' => $visitor->host ?? null,
            'purpose' => $visitor->keperluan ?? null,
            'foto' => $fotoUrl,
            'catatan' => $blacklisted->alasan_blacklist ?? 'Diblokir oleh sistem',
        ]);
    }

    protected function handleAbsensi($visitor, $sourceOrigin, $now)
    {
        $gracePeriod = 30;
        $lastLog = AbsensiRestLog::where('trnvisitorid', $visitor->trnvisitorid)
            ->where('source_origin', $sourceOrigin)
            ->orderBy('scan_time', 'desc')
            ->lockForUpdate()
            ->first();

        $fotoUrl = $this->getFotoUrl($visitor);
        $activity = 'in';
        $statusText = 'Izin Keluar untuk Istirahat';

        if (!$lastLog) {
            // Log::channel('ga_security_telegram')->info('Belum ada log sebelumnya, buat log baru', ['trnvisitorid' => $visitor->trnvisitorid]);
            return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
        }

        $diff = $now->diffInSeconds(Carbon::parse($lastLog->scan_time));
        $wait = $gracePeriod - $diff;

        if ($diff < $gracePeriod) {
            // Log::channel('ga_security_telegram')->warning('Absen terlalu cepat', [
            //   'trnvisitorid' => $visitor->trnvisitorid,
            //   'delay' => $diff,
            //   'wait' => $wait,
            // ]);
            return response()->json([
                'success' => true,
                'message' => "Kamu sudah absen, silahkan tunggu {$wait} detik untuk absen lagi.",
                'data' => $this->formatVisitorData($visitor, $sourceOrigin, $lastLog->activity_type, $statusText),
            ]);
        }

        $activity = $lastLog->activity_type === 'in' ? 'out' : 'in';
        $statusText = $activity === 'out' ? 'Kembali dari Istirahat' : 'Izin Keluar untuk Istirahat';

        return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
    }

    protected function createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now)
    {
        AbsensiRestLog::create([
            'trnvisitorid' => $visitor->trnvisitorid,
            'source_origin' => $sourceOrigin,
            'activity_type' => $activity,
            'scan_time' => $now,
            'tanggal_log' => $now->toDateString(),
            'no_kartu' => $visitor->no_kartu ?? null,
            'no_ktp_sim' => $visitor->no_ktp_sim ?? null,
            'nama' => $visitor->namavisitor ?? null,
            'namacomp' => $visitor->namacomp ?? null,
            'host' => $visitor->host ?? null,
            'hostdeptid' => $visitor->hostdeptid ?? null,
            'purpose' => $visitor->purpose ?? $visitor->keperluan ?? null,
            'nopol' => $visitor->nopol ?? null,
            'nohpdriver' => $visitor->nohpdriver ?? null,
            'nama_kernet' => $visitor->nama_kernet ?? null,
            'tgl_lahir' => $visitor->tgl_lahir ?? null,
            'imgvisitorpathin' => $visitor->imgvisitorpathin ?? null,
            'foto' => $fotoUrl,
        ]);

        // Log::channel('ga_security_telegram')->info('Log absensi dibuat', [
        //   'trnvisitorid' => $visitor->trnvisitorid,
        //   'activity' => $activity,
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen berhasil',
            'data' => $this->formatVisitorData($visitor, $sourceOrigin, $activity, $statusText),
        ]);
    }

    protected function getFotoUrl($visitor)
    {
        $fotoJson = $visitor->foto ?? '[]';
        $fotoArray = json_decode($fotoJson, true);

        if (is_array($fotoArray) && !empty($fotoArray)) {
            return $fotoArray[0];
        }

        return 'https://via.placeholder.com/300';
    }

    protected function formatVisitorData($visitor, $sourceOrigin, $nextAction = 'in', $statusText = '')
    {
        $fotoUrl = $this->getFotoUrl($visitor);

        return [
            'trnvisitorid' => $visitor->trnvisitorid,
            'nama' => $visitor->namavisitor,
            'perusahaan' => $visitor->namacomp ?? $visitor->perusahaan ?? '-',
            'jenis_kunjungan' => $sourceOrigin === 'ga_visitor_vendor_transactions' ? 'VENDOR' : 'TAMU',
            'no_polisi' => $visitor->nopol ?? '-',
            'keperluan' => $visitor->keperluan ?? $visitor->purpose ?? '-',
            'status_istirahat' => $nextAction === 'in' ? 'keluar' : 'masuk',
            'next_action' => $nextAction === 'in' ? 'out' : 'in',
            'status_display' => $statusText,
            'foto_url' => $fotoUrl,
            'source' => $sourceOrigin,
            'source_detail' => [
                'type' => $sourceOrigin === 'ga_visitor_vendor_transactions' ? 'vendor' : 'tamu',
                'asal_perusahaan' => $visitor->namacomp ?? '-',
                'penanggung_jawab' => $visitor->host ?? '-',
                'no_ktp_sim' => $visitor->no_ktp_sim ?? '-',
                'no_kartu' => $visitor->no_kartu ?? '-',
                'waktu_masuk' => $visitor->datein ? Carbon::parse($visitor->datein . ' ' . $visitor->timein)->format('H:i') : '-',
                'lokasi_tujuan' => $visitor->gateidin ?? '-',
                'catatan' => $visitor->keperluan ?? $visitor->purpose ?? '-',
                'jumlah_tamu' => $visitor->sumpeople ?? 1,
                'no_hp' => $visitor->nohpdriver ?? '-',
                'nama_kernet' => $visitor->nama_kernet ?? '-',
                'tgl_lahir' => $visitor->tgl_lahir ? Carbon::parse($visitor->tgl_lahir)->format('d-m-Y') : '-',
                'gate' => $visitor->gateidin ?? '-',
                'plant' => $visitor->plant ?? '-',
                'created_at' => $visitor->created_at ? $visitor->created_at->format('d-m-Y H:i') : '-',
            ],
            'status_kartu' => $visitor->kartu_dikembalikan ? 'dikembalikan' : 'aktif',
            'status_display' => $visitor->kartu_dikembalikan ? 'Kartu Sudah Dikembalikan' : 'Kartu Aktif',
        ];
    }

    public function cari_security($id_card)
    {
        // Log::channel('ga_security_telegram')->debug('Pengecekan kartu security dimulai', ['id_card' => $id_card]);

        if (empty($id_card)) {
            // Log::channel('ga_security_telegram')->error('ID Card kosong', ['id_card' => $id_card]);
            return [
                'data' => ['pesan' => 'Indikasi scanner rusak', 'notif' => 'failed', 'status' => 'error'],
                'data_detail' => null,
                'raw_data' => null
            ];
        }

        $id_card = (int)$id_card;
        if ($id_card == 1215) {
            $id_card = 2421193138;
            // Log::channel('ga_security_telegram')->warning('ID Card 1215 di-remap ke 2421193138');
        }

        try {
            $MSIDCARD = DB::connection('192.168.154.44')
                ->table('MSIDCARD')
                ->select('NIK', 'EMPNM', 'CREATEDON', 'DEPTID', 'CARDNODEVICE', 'BARCODE', 'FOTOBLOB')
                ->whereNotNull('FOTOTYPE')
                ->where('CARDNODEVICE', $id_card)
                ->orderBy('CREATEDON', 'desc')
                ->orderByRaw('NIK desc')
                ->first();

            if (!$MSIDCARD) {
                // Log::channel('ga_security_telegram')->warning('Kartu tidak terdaftar di MSIDCARD', ['id_card' => $id_card]);
                return [
                    'data' => ['pesan' => 'Kartu Tidak Terdaftar', 'notif' => 'failed', 'status' => 'tidak-terdaftar'],
                    'data_detail' => null,
                    'raw_data' => null
                ];
            }

            // Log::channel('ga_security_telegram')->info('Data MSIDCARD ditemukan', ['nik' => $MSIDCARD->NIK, 'dept' => $MSIDCARD->DEPTID]);

            $nik = $MSIDCARD->NIK;
            $nama = $MSIDCARD->EMPNM ?? 'SEC';
            $absenTime = Carbon::now();

            $namaClean = preg_replace('/[\s\/\\\\<>?*:|"()]+/', '_', $nama);
            $namaClean = trim($namaClean, '_');
            $imageName = "{$nik}_{$namaClean}.jpeg";
            $storagePath = 'public/ga/monitoring/security/' . $imageName;
            $publicPath = 'storage/ga/monitoring/security/' . $imageName;

            if ($MSIDCARD->FOTOBLOB) {
                $fullPath = storage_path('app/' . $storagePath);
                $dir = dirname($fullPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                    // Log::channel('ga_security_telegram')->debug('Direktori foto dibuat', ['path' => $dir]);
                }
                file_put_contents($fullPath, $MSIDCARD->FOTOBLOB);
                // Log::channel('ga_security_telegram')->info('Foto disimpan', ['path' => $publicPath]);
            }

            $vendorData = DB::connection('newpas')
                ->table('hr_vendor')
                ->where('nik', $nik)
                ->first();

            $isVendor = $vendorData !== null;

            $dept = $isVendor && !empty($vendorData->departement)
                ? $vendorData->departement
                : $MSIDCARD->DEPTID;

            $isSecurity = in_array(strtoupper(trim($dept)), ['SECURITY', 'SEC']);

            $fromStaff = false;
            if (!$isVendor) {
                $staffData = DB::connection('newpas')
                    ->table('hr_karyawan')
                    ->select('kode_bagian AS dept', 'staff')
                    ->where('active', 'Y')
                    ->where('nik', $nik)
                    ->first();

                if ($staffData && $staffData->staff === 'Y') {
                    $dept = $staffData->dept;
                    $isSecurity = in_array(strtoupper(trim($dept)), ['SECURITY', 'SEC']);
                    $fromStaff = true;
                    // Log::channel('ga_security_telegram')->info('Staff internal ditemukan', ['nik' => $nik, 'dept' => $dept]);
                }
            }

            $dataDetail = [
                'nik' => $MSIDCARD->NIK,
                'idcard' => $MSIDCARD->CARDNODEVICE,
                'nama' => $MSIDCARD->EMPNM,
                'dept' => $dept,
                'foto_path' => $publicPath,
            ];

            $rawData = [
                'nik' => $dataDetail['nik'],
                'nama' => $dataDetail['nama'],
                'dept' => $dataDetail['dept'],
                'id_card' => $dataDetail['idcard'],
                'absen_time' => $absenTime->format('Y-m-d H:i:s'),
                'status' => $isSecurity ? 'berhasil' : 'gagal',
                'notif' => $isSecurity ? 'success' : 'failed',
                'pesan' => $isSecurity
                    ? ($isVendor ? 'Vendor SECURITY ditemukan' : 'Staff SECURITY ditemukan')
                    : 'Bukan petugas security',
                'staff' => $isVendor ? 'N' : ($fromStaff ? 'Y' : 'N/A'),
                'location' => 'security',
                'image_name' => $imageName,
                'foto_path' => $publicPath,
            ];

            if (!$isSecurity) {
                // Log::channel('ga_security_telegram')->warning('Dept bukan SECURITY', ['dept' => $dept, 'nik' => $nik]);
                return [
                    'data' => [
                        'pesan' => 'Akses Ditolak: Bukan Petugas Security',
                        'notif' => 'failed',
                        'status' => 'bukan-security'
                    ],
                    'data_detail' => $dataDetail,
                    'raw_data' => $rawData
                ];
            }

            if (!$isVendor && !$fromStaff) {
                // Log::channel('ga_security_telegram')->error('Bukan vendor dan bukan staff tetap', ['nik' => $nik]);
                return [
                    'data' => [
                        'pesan' => 'Akses Ditolak: Bukan Vendor atau Staff Tetap',
                        'notif' => 'failed',
                        'status' => 'tidak-diizinkan'
                    ],
                    'data_detail' => $dataDetail,
                    'raw_data' => $rawData
                ];
            }

            // Log::channel('ga_security_telegram')->info('Petugas security valid dan lolos semua cek', ['nik' => $nik]);
            return [
                'data' => [
                    'pesan' => 'Petugas Security ditemukan',
                    'notif' => 'success',
                    'status' => 'berhasil'
                ],
                'data_detail' => $dataDetail,
                'raw_data' => $rawData
            ];
        } catch (\Exception $e) {
            Log::channel('ga_security_telegram')->error('Error di cari_security', [
                'id_card' => $id_card,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'data' => ['pesan' => 'Terjadi kesalahan sistem', 'notif' => 'error', 'status' => 'error'],
                'data_detail' => null,
                'raw_data' => null
            ];
        }
    }
}
