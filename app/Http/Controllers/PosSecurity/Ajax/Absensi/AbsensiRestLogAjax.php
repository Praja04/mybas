<?php

namespace App\Http\Controllers\PosSecurity\Ajax\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PosSecurity\Absensi\AbsensiRestLog;
use App\Models\PosSecurity\Absensi\GateAccessLog;
use App\Models\PosSecurity\GaDataSecurity;
use Illuminate\Support\Facades\Validator;
use App\Models\PosSecurity\GaVisitorTransaction;
use App\Models\PosSecurity\GaVisitorVendorTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AbsensiRestLogAjax extends Controller
{
    public function search(Request $request)
    {
        $now = Carbon::now();
        $keyword = $request->input('keyword');

        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
            ], 422);
        }

        // Cek apakah kartu adalah kartu security
        $securityResult = $this->cari_security($keyword);

        if ($securityResult && !empty($securityResult['data_detail'])) {

            $security = $securityResult['data_detail'];
            $fotoUrl = $security['foto_path'];

            $pendingKey = "pending_visitors";
            $pending = Cache::get($pendingKey, []);

            $validVisitors = collect($pending)
                ->filter(fn($v) => $now->diffInMinutes($v['tapped_at']) < 5) // hanya visitor yang berlaku <= 5 menit
                ->pluck('visitor');

            $formattedData = [
                'trnvisitorid' => 'SEC-' . $security['nik'],
                'nama'         => $security['nama'],
                'perusahaan'   => 'INTERNAL - SECURITY',
                'jenis_kunjungan' => 'PETUGAS SECURITY',
                'no_polisi'    => '-',
                'keperluan'    => 'Verifikasi Akses Gerbang & Pembukaan Gerbang',
                'foto_url'     => $fotoUrl,
                'status_display' => 'AKTIF',
                'source'       => 'security',
                'source_detail' => [
                    'nik'            => $security['nik'],
                    'nama'           => $security['nama'],
                    'no_kartu'       => $security['idcard'],
                    'dept'           => 'SECURITY',
                    'plant'          => '1001',
                    'gate'           => 'POS01',
                    'created_at'     => now()->format('Y-m-d H:i:s'),
                ],
            ];

            if ($validVisitors->isNotEmpty()) {
                foreach ($validVisitors as $v) {
                    GateAccessLog::create([
                        'nik'          => $security['nik'],
                        'nama'         => $security['nama'],
                        'dept'         => 'SECURITY',
                        'id_card'      => $security['idcard'],
                        'visitor_trn'  => $v->trnvisitorid,
                        'visitor_nama' => $v->namavisitor, // data tamu
                        'gate'         => 'POS01',
                        'foto_url'     => $fotoUrl,
                        'waktu'        => now(),
                    ]);
                }
                Cache::forget($pendingKey); // clear setelah diproses
            } else {
                // Security scan tanpa visitor
                GateAccessLog::create([
                    'nik'          => $security['nik'],
                    'nama'         => $security['nama'],
                    'dept'         => 'SECURITY',
                    'id_card'      => $security['idcard'],
                    'visitor_trn'  => null,
                    'visitor_nama' => null,
                    'gate'         => 'POS01',
                    'foto_url'     => $fotoUrl,
                    'waktu'        => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'type'    => 'security',
                'message' => 'Data petugas security ditemukan',
                'data'    => $formattedData,
            ]);
        }
        // Cek ketika ada visitor pending valid tapi kartu yang tap bukan security
        $pendingKey = "pending_visitors";
        $pending = Cache::get($pendingKey, []);

        if (empty($pending)) {
            $validPending = collect([]);
        } else {
            $validPending = collect($pending)
                ->filter(fn($v) => isset($v['visitor']) && $now->diffInMinutes($v['tapped_at']) < 5);
        }

        // Jika visitor pending dan kartu ini bukan security → tolak
        if ($validPending->isNotEmpty()) {
            if (!$securityResult || empty($securityResult['data_detail'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan petugas security',
                ], 403);
            }
        }

        // Cek apakah kartu adalah kartu visitor
        $visitorData = $this->findVisitor($keyword);
        if (!$visitorData) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak ditemukan atau tamu sudah mengembalikan kartu.',
            ]);
        }

        [$visitor, $sourceOrigin] = $visitorData;


        if ((int)$visitor->kartu_dikembalikan === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu sudah dikembalikan.',
            ]);
        }

        // if ($this->isGloballyBlacklisted($visitor)) {
        //     $this->logBlacklist($visitor->trnvisitorid, $now);
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Akses diblokir: identitas berada dalam daftar hitam.',
        //     ]);
        // }

        // Masukkan visitor ke cache pending
        $alreadyExists = false;

        foreach ($pending as &$entry) {
            if ($entry['visitor']->trnvisitorid === $visitor->trnvisitorid) {
                if ($now->diffInSeconds($entry['tapped_at']) < 5) {
                    // jangan simpan cache dulu
                    Cache::forget('pending_visitors');

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

        // Simpan cache hanya jika visitor benar-benar valid dan masuk queue
        if (!empty($pending)) {
            Cache::put($pendingKey, $pending, 300); // 5 menit
        }

        return DB::transaction(function () use ($visitor, $sourceOrigin, $now) {
            return $this->handleAbsensi($visitor, $sourceOrigin, $now);
        }, 3);
    }

    public function cari_security($id_card)
    {
        if (empty($id_card)) {
            return [
                'data' => [
                    'pesan' => 'ID Card kosong / scanner error',
                    'notif' => 'failed',
                    'status' => 'error'
                ],
                'data_detail' => null,
                'raw_data' => null
            ];
        }

        try {
            $security = GaDataSecurity::where('nomor_kartu', $id_card)
                ->where('status', 'active')
                ->first();

            if (!$security) {
                return [
                    'data' => [
                        'pesan' => 'Kartu tidak terdaftar sebagai security',
                        'notif' => 'failed',
                        'status' => 'not-found'
                    ],
                    'data_detail' => null,
                    'raw_data' => null
                ];
            }

            $fotoUrl = $security->foto ? asset($security->foto) : asset('assets/media/images/no-image.jpg');

            $dataDetail = [
                'nik'       => $security->nik,
                'nama'      => $security->nama_security,
                'idcard'    => $security->nomor_kartu,
                'foto_path' => $fotoUrl,
            ];

            $rawData = [
                'nik'        => $security->nik,
                'nama'       => $security->nama_security,
                'dept'       => 'SECURITY',
                'id_card'    => $security->nomor_kartu,
                'foto_path'  => $fotoUrl,
                'absen_time' => now()->format('Y-m-d H:i:s'),
                'notif'      => 'success',
                'status'     => 'berhasil',
                'pesan'      => 'Security ditemukan',
            ];

            return [
                'data'        => [
                    'pesan' => 'Security ditemukan',
                    'notif' => 'success',
                    'status' => 'berhasil'
                ],
                'data_detail' => $dataDetail,
                'raw_data'    => $rawData
            ];
        } catch (\Exception $e) {
            Log::error('Error cari_security(): ' . $e->getMessage());

            return [
                'data' => [
                    'pesan' => 'Kesalahan sistem saat mengambil data security',
                    'notif' => 'error',
                    'status' => 'error'
                ],
                'data_detail' => null,
                'raw_data' => null
            ];
        }
    }

    protected function findVisitor($keyword)
    {
        $visitor = GaVisitorTransaction::where(function ($q) use ($keyword) {
            $q->where('trnvisitorid', $keyword)
                ->orWhere('no_ktp_sim', $keyword)
                ->orWhere('no_kartu', $keyword);
        })->where(function ($q) {
            $q->whereNull('dateout')->orWhere('kartu_dikembalikan', true);
        })->orderBy('createdon', 'desc')->first();

        if ($visitor) return [$visitor, 'ga_visitor_transactions'];

        $visitor = GaVisitorVendorTransaction::where(function ($q) use ($keyword) {
            $q->where('trnvisitorid', $keyword)
                ->orWhere('no_ktp_sim', $keyword)
                ->orWhere('no_kartu', $keyword);
        })->where(function ($q) {
            $q->whereNull('dateout')->orWhere('kartu_dikembalikan', true);
        })->orderBy('createdon', 'desc')->first();

        return $visitor ? [$visitor, 'ga_visitor_vendor_transactions'] : null;
    }

    protected function isGloballyBlacklisted($visitor)
    {
        return DB::table('ga_lgtk_blacklist_identitas')
            ->where('aktif', 1)
            ->where(function ($q) use ($visitor) {
                $q->where('trnvisitorid', $visitor->trnvisitorid)
                    ->orWhere('no_identitas', $visitor->no_ktp_sim);
            })
            ->exists();
    }

    protected function logBlacklist($keyword, $now)
    {
        // 1. Cari di blacklist dulu
        $blacklisted = DB::table('ga_lgtk_blacklist_identitas')
            ->where('aktif', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('trnvisitorid', $keyword)
                    ->orWhere('no_identitas', $keyword);
            })
            ->first();

        if (!$blacklisted) {
            // Jika tidak ditemukan, minimal log keyword
            AbsensiRestLog::create([
                'trnvisitorid' => $keyword,
                'source_origin' => 'ga_lgtk_blacklist_identitas',
                'activity_type' => 'failed',
                'scan_time' => $now,
                'tanggal_log' => $now->toDateString(),
                'catatan' => 'Akses diblokir: identitas tidak dikenali atau tidak aktif.',
            ]);
            return;
        }

        // Ambil trnvisitorid dari blacklist (bisa jadi keyword itu no_identitas)
        $trnvisitorid = $blacklisted->trnvisitorid;

        // 2. Cari tamu di ga_visitor_transactions
        $visitor = GaVisitorTransaction::where('trnvisitorid', $trnvisitorid)->first();

        // 3. Jika tidak ketemu, coba cari di vendor
        $sourceOrigin = 'ga_visitor_transactions';
        if (!$visitor) {
            $visitor = GaVisitorVendorTransaction::where('trnvisitorid', $trnvisitorid)->first();
            $sourceOrigin = 'ga_visitor_vendor_transactions';
        }

        // 4. Siapkan data untuk log
        $dataLog = [
            'trnvisitorid' => $trnvisitorid,
            'source_origin' => 'ga_lgtk_blacklist_identitas',
            'activity_type' => 'failed',
            'scan_time' => $now,
            'tanggal_log' => $now->toDateString(),
            'no_kartu' => null,
            'no_ktp_sim' => $blacklisted->no_identitas ?? null,
            'nama' => $blacklisted->nama ?? null,
            'namacomp' => null,
            'host' => null,
            'hostdeptid' => null,
            'purpose' => null,
            'nopol' => null,
            'nohpdriver' => null,
            'nama_kernet' => null,
            'tgl_lahir' => $blacklisted->tanggal_lahir ?? null,
            'imgvisitorpathin' => null,
            'foto' => null,
            'catatan' => $blacklisted->alasan_blacklist ?? 'Identitas diblokir oleh sistem.',
        ];

        // 5. Jika visitor ditemukan, tambahkan detail dari tabel tamu
        if ($visitor) {
            $fotoArray = json_decode($visitor->foto, true);
            $fotoUrl = is_array($fotoArray) && !empty($fotoArray) ? $fotoArray[0] : null;

            $dataLog['no_kartu'] = $visitor->no_kartu ?? null;
            $dataLog['nama'] = $visitor->namavisitor ?? $dataLog['nama'];
            $dataLog['namacomp'] = $visitor->namacomp ?? null;
            $dataLog['host'] = $visitor->host ?? null;
            $dataLog['hostdeptid'] = $visitor->hostdeptid ?? null;
            $dataLog['purpose'] = $visitor->keperluan ?? null;
            $dataLog['nopol'] = $visitor->no_polisi ?? null;
            $dataLog['nohpdriver'] = $visitor->no_hp ?? null;
            $dataLog['nama_kernet'] = $visitor->nama_kernet ?? null;
            $dataLog['tgl_lahir'] = $visitor->tgl_lahir ?? $dataLog['tgl_lahir'];
            $dataLog['imgvisitorpathin'] = $visitor->imgvisitorpathin ?? null;
            $dataLog['foto'] = $fotoUrl;
        }

        // 6. Simpan log
        AbsensiRestLog::create($dataLog);
    }

    public function logGateAccess(Request $request)
    {
        $securityCard = $request->input('security_card');
        $trnvisitorid = $request->input('trnvisitorid'); // Boleh null

        // Ambil dari config/database.php
        // Pastikan kamu sudah daftarin 'security' di database.php
        $security = DB::connection('security')
            ->table('users') // Ganti sesuai tabel di db_security
            ->where('no_kartu', $securityCard)
            ->orWhere('card_number', $securityCard) // kalau kolomnya beda
            ->first();

        // Kalau kartu tidak dikenal, tetap lanjut, cuma gak simpen nama
        $securityNama = $security ? ($security->nama ?? $security->name ?? 'Unknown') : 'Unknown Security';

        // Simpen log akses gate
        DB::table('gate_access_logs')->insert([
            'security_card' => $securityCard,
            'security_nama' => $securityNama,
            'trnvisitorid' => $trnvisitorid,
            'nama_pengunjung' => null,
            'activity_type' => null,
            'gate' => 'gate1', // bisa dari config atau request
            'waktu_buka' => now(),
        ]);

        return response()->json([
            'success' => true,
            'nama' => $securityNama,
        ]);
    }

    protected function handleAbsensi($visitor, $sourceOrigin, $now)
    {
        $gracePeriod = 7;
        $lastLog = AbsensiRestLog::where('trnvisitorid', $visitor->trnvisitorid)
            ->where('source_origin', $sourceOrigin)
            ->orderBy('scan_time', 'desc')
            ->lockForUpdate()
            ->first();

        $fotoUrl = $this->getFotoUrl($visitor);
        $activity = 'in';
        $statusText = 'Izin Keluar untuk Istirahat';

        if (!$lastLog) {
            return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
        }

        $diff = $now->diffInSeconds(Carbon::parse($lastLog->scan_time));
        $wait = $gracePeriod - $diff;

        if ($diff < $gracePeriod) {
            Cache::forget('pending_visitors');

            return response()->json([
                'success' => false,
                'message' => "Kamu sudah absen, silahkan tunggu {$wait} detik untuk absen lagi.",
                'data' => $this->formatVisitorData($visitor, $sourceOrigin, $lastLog->activity_type, $statusText),
            ]);
        }

        $activity = $lastLog->activity_type === 'in' ? 'out' : 'in';
        $statusText = $activity === 'out' ? 'Kembali dari Istirahat' : 'Izin Keluar untuk Istirahat';

        return $this->createLogAndResponse($visitor, $sourceOrigin, $activity, $statusText, $fotoUrl, $now);
    }

    public function do_absen(Request $request)
    {
        $data = [];

        // Ini kalau yang rf id nya kosong
        if ($request->id_card == '') {

            $data['pesan'] = "Indikasi scanner rusak";
            $data['notif'] = "failed";
            $data['status'] = "tidak-terdaftar";
            return ['data' => $data];
        }

        $id_card = (int)$request->id_card;
        if ($id_card == 1215) {
            $id_card = 2421193138;
        }

        // Get data dari secure access berdasarkan nomor kartu
        $msidcard = DB::connection('192.168.154.44')
            ->table('MSIDCARD')
            ->select('NIK', 'EMPNM', 'CREATEDON', 'DEPTID', 'CARDNODEVICE', 'BARCODE')
            // ->select('NIK')
            ->whereNotNull('FOTOTYPE')
            ->where(['NIK' => '00101493'])
            // ->where(['CARDNODEVICE' => $id_card ])
            ->orderBy('CREATEDON', 'desc')
            ->orderByRaw('NIK desc')
            ->first();

        dd($msidcard);

        //if( count(explode('-', $nik->NIK)) > 1 ) {
        // $nik = DB::connection('192.168.154.44')
        //   ->table('MSIDCARD')
        //   ->select('BARCODE', 'CARDNODEVICE', 'NIK', 'EMPNM', 'CREATEDON', 'DEPTID')
        //   // ->whereNotNull('FOTOTYPE')
        //   ->where(['CARDNODEVICE' => $id_card])
        //   // ->orderBy('CREATEDON', 'desc')
        //   ->orderByRaw('CAST(SUBSTR(NIK, 8) AS SIGNED) desc')
        //   ->first();
        //}

        $isVendor = false;

        if (count(explode('-', $nik->NIK)) <= 1) {
            $nik = DB::connection('192.168.154.44')
                ->table('MSIDCARD')
                //->select('NIK','EMPNM','CREATEDON', 'DEPTID', 'CARDNODEVICE', 'BARCODE')
                ->select('NIK', 'CARDNODEVICE', 'NIK', 'EMPNM', 'CREATEDON', 'DEPTID')
                // ->whereNotNull('FOTOTYPE')
                ->where(['CARDNODEVICE' => $id_card])
                // ->orderBy('CREATEDON', 'desc')
                ->orderByRaw('NIK desc')
                ->first();
            $isVendor = true;
        }

        // Jika tidak ditemukan di secure access
        if ($nik == null) {
            // Berarti id card nya tidak bisa.
            $tidak_bisa = DB::connection('localhost')
                ->table('tidak_bisa')
                ->insert([
                    'id_card' => $request->id_card,
                ]);
            $data['pesan'] = "Tidak Terdaftar";
            $data['notif'] = "failed";
            $data['status'] = "tidak-terdaftar";
            return ['data' => $data];
        }

        // Ambil data dari database secure access + fotoblob
        $data['nik'] = $nik->NIK;
        $nik->FOTOBLOB = 'asdf';
        $user = $nik;
        //DB::connection('192.168.154.44')
        //->table('MSIDCARD')
        //->select('BARCODE', 'CARDNODEVICE','EMPNM','DEPTID','FOTOBLOB')
        //->where([
        //	'BARCODE' => $data['nik'],
        //	'CARDNODEVICE' => $id_card
        //])
        //->orderByRaw('SUBSTR(NIK, 8) desc')
        //->first();

        $data['idcard']   = $user->CARDNODEVICE;
        $data['nama']     = $user->EMPNM;
        $data['dept']     = $user->DEPTID;
        $data['foto']     = base64_encode($user->FOTOBLOB);

        $vendorDeptID = ['BJP', 'CLNIC', 'CSR1', 'CSR2', 'CSR3', 'CSR4', 'CSR5', 'CSR6', 'CSR7', 'FOR', 'GEM', 'ISN', 'ISS', 'KMJ', 'MP', 'SEC', 'RUR', 'TM', 'EJ'];

        // Absensi untuk vendor
        if (in_array($data['dept'], $vendorDeptID)) {
            $imageName = 'MASUK-' . uniqid() . '.' . 'jpeg';

            $data['tgl_absen'] = date('Ymd');
            $data['jam_absen'] = date('H:i:s');
            $data['location'] = $request->location;
            $data['image'] = $imageName;
            $data['mesin'] = 'PASVENDOR';
            $data['ip_address'] = '100';
            $data['absen_time'] = date('d/m/Y') . ' ' . date('H:i:s');
            $data['pesan'] = "Absen Berhasil ( Vendor )";
            $data['notif'] = "success";
            $data['status'] = "masuk";
            $data['staff'] = 'N';

            $this->absen_raw($data);

            // Upload gambar
            $image = $request->data_gambar;  // your base64 encoded
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            File::put(app()->basePath('public/absen_images/') . $imageName, base64_decode($image));
            return ['data' => $data];
        }

        $spesial_information = DB::connection('newpas')
            ->table('hr_spesial_information')
            ->where('is_active', 'Y')
            ->join('hr_spesial_information_detail', 'hr_spesial_information.id_informasi', '=', 'hr_spesial_information_detail.id_informasi')
            ->where('hr_spesial_information_detail.nik', $data['nik'])
            ->get();

        $my_data = DB::connection('newpas')
            ->table('hr_karyawan')
            ->select('nama', DB::raw('kode_bagian AS dept'), DB::raw('kode_divisi AS divisi'), 'staff')
            ->where('active', 'Y')
            // ->where('Endda', '9998-12-31')
            ->where('nik', $nik->NIK)
            ->first();

        if (strtolower($request->location) == 'vendor') {
            // dd($cek_produksi);
            // kalau data adalah orang produksi
            if (!$isVendor) {
                $data['pesan'] = "<span class='text-red'>Tidak Boleh Absen Di Sini</span><br/> Hanya untuk <strong>VENDOR</strong>";
                $data['notif'] = "failed";
                $data['status'] = "tidak-boleh";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            }
        }

        // Cek kalau ini absensi GA, maka ga boleh ada orang produksi
        if (strtolower($request->location) == 'ga') {
            // Ambil data ke 
            $nik_bleh = [
                '080310-8084', // RND
                '090320-32087', // RND
                '270715-19203', // RND
                '290212-10531', // RND
            ];

            $nik_ga_boleh = [];
            if (in_array($nik->NIK, $nik_bleh)) {
                $cek_restrict = 'boleh';
            } else {
                $cek_restrict = DB::connection('newpas')
                    ->table('hr_karyawan')
                    ->select('nama', DB::raw('kode_bagian AS dept'), DB::raw('kode_divisi AS divisi'))
                    ->where('active', 'Y')
                    // ->where('Endda', '9998-12-31')
                    ->where('nik', $nik->NIK)
                    ->where(function ($query) {
                        // List divisi yang tidak boleh absen di GA ( loker )
                        $query->where('kode_bagian', 'WFG')
                            ->orWhere('kode_bagian', 'HGA')
                            ->orWhere('kode_bagian', 'ITE')
                            ->orWhere('kode_bagian', 'HSE')
                            ->orWhere('kode_bagian', 'TIM')
                            ->orWhere('kode_bagian', 'R&I')
                            ->orWhere('kode_bagian', 'OPE');
                    })
                    ->first();

                if (in_array($nik->NIK, $nik_ga_boleh)) {
                    $cek_restrict = null;
                }
            }
            // dd($cek_produksi);
            // kalau data adalah orang produksi
            if ($cek_restrict == null) {
                $data['pesan'] = "<span class='text-red'>Tidak Boleh Absen Di Sini</span><br/> Hanya untuk <strong>WFG, GA, ITE, HSE, OPE, TIM, R&I</strong>. Kamu adalah: <strong>" . $my_data->divisi . ", " . $my_data->dept . "</strong>";
                $data['notif'] = "failed";
                $data['status'] = "tidak-boleh";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            }
        }

        // Cek kalau ini absensi GA, maka ga boleh ada orang produksi
        if (strtolower($request->location) == 'office') {
            // Ambil data ke 
            $nik_bleh = [
                '131120-34345',
                '021203-016',
                '150517-24949',
                '010319-28921',
                '011121-37927',
                '020120-31264',
                '051020-33791',
                '020621-36106',
                '090814-16422',
                '170504-1894',
                '010322-38868',
                '151204-4405',
                '050115-17895',
                '040116-20421',
                '020608-6752',
                '021203-028',
                '010219-28872',
                '010721-36484',
                '170914-16987',
                '230604-2306',
                '060711-9490',
                '100122-38794',
                '250904-3205',
                '250904-3206',
                '051109-7831',
                '190615-19094',
                '105000483',
            ];

            $nik_ga_boleh = [];
            if (in_array($nik->NIK, $nik_bleh)) {
                $cek_restrict = 'boleh';
            } else {
                $cek_restrict = DB::connection('newpas')
                    ->table('hr_karyawan')
                    ->select('nama', DB::raw('kode_bagian AS dept'), DB::raw('kode_divisi AS divisi'))
                    ->where('active', 'Y')
                    // ->where('Endda', '9998-12-31')
                    ->where('nik', $nik->NIK)
                    ->where(function ($query) {
                        // List divisi yang tidak boleh absen di GA ( loker )
                        $query->where('kode_bagian', 'DIR')
                            ->orWhere('kode_bagian', 'GMN')
                            ->orWhere('kode_divisi', 'EXS')
                            ->orWhere('kode_divisi', 'FAC')
                            ->orWhere('kode_bagian', 'HAP')
                            ->orWhere('kode_bagian', 'HRT')
                            ->orWhere('kode_bagian', 'HIR')
                            ->orWhere('kode_bagian', 'HRR')
                            ->orWhere('kode_bagian', 'HRD')
                            ->orWhere('kode_divisi', 'PPC')
                            ->orWhere('kode_divisi', 'PUR')
                            ->orWhere('kode_divisi', 'PUR02');
                    })
                    ->first();

                if (in_array($nik->NIK, $nik_ga_boleh)) {
                    $cek_restrict = null;
                }
            }
            // dd($cek_produksi);
            // kalau data adalah orang produksi
            if ($cek_restrict == null) {
                $data['pesan'] = "<span class='text-red'>Tidak Boleh Absen Di Sini</span><br/> Hanya untuk <strong>DIR, GMN, EXS, FAC, HAP, HRT, HIR, HRR, HRD, PPC, PUR</strong>. Kamu adalah: <strong>" . $my_data->divisi . ", " . $my_data->dept . "</strong>";
                $data['notif'] = "failed";
                $data['status'] = "tidak-boleh";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            }
        }

        // Cek kalau ini absensi GA, maka ga boleh ada orang produksi
        if (strtolower($request->location) == 'wrh') {
            // Ambil data ke 
            $nik_bleh = [];

            $nik_ga_boleh = [];
            if (in_array($nik->NIK, $nik_bleh)) {
                $cek_restrict = 'boleh';
            } else {
                $cek_restrict = DB::connection('newpas')
                    ->table('hr_karyawan')
                    ->select('nama', DB::raw('kode_bagian AS dept'), DB::raw('kode_divisi AS divisi'))
                    ->where('active', 'Y')
                    // ->where('Endda', '9998-12-31')
                    ->where('nik', $nik->NIK)
                    ->where(function ($query) {
                        // List divisi yang tidak boleh absen di GA ( loker )
                        $query->where('kode_divisi', 'WRH')
                            ->orWhere('kode_divisi', 'WRM')
                            ->orWhere('kode_divisi', 'WSM')
                            ->orWhere('kode_bagian', 'QRM')
                            ->orWhere('kode_bagian', 'WSP');
                    })
                    ->first();

                if (in_array($nik->NIK, $nik_ga_boleh)) {
                    $cek_restrict = null;
                }
            }
            // dd($cek_produksi);
            // kalau data adalah orang produksi
            if ($cek_restrict == null) {
                $data['pesan'] = "<span class='text-red'>Tidak Boleh Absen Di Sini</span><br/> Hanya untuk <strong>WRH, QRM, WSP</strong>. Kamu adalah: <strong>" . $my_data->divisi . ", " . $my_data->dept . "</strong>";
                $data['notif'] = "failed";
                $data['status'] = "tidak-boleh";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            }
        }

        // Cek kalau ini absensi QA, maka ga boleh ada orang produksi
        if (strtolower($request->location) == 'qa') {
            $nik_bleh = [];

            $nik_ga_boleh = [];
            if (in_array($nik->NIK, $nik_bleh)) {
                $cek_restrict = 'boleh';
            } else {
                $cek_restrict = DB::connection('newpas')
                    ->table('hr_karyawan')
                    ->select('nama', DB::raw('kode_bagian AS dept'), DB::raw('kode_divisi AS divisi'))
                    ->where('active', 'Y')
                    // ->where('Endda', '9998-12-31')
                    ->where('nik', $nik->NIK)
                    ->where(function ($query) {
                        // List divisi yang tidak boleh absen di GA ( loker )
                        $query->where('kode_divisi', 'RQA');
                    })
                    ->first();

                if (in_array($nik->NIK, $nik_ga_boleh)) {
                    $cek_restrict = null;
                }
            }
            // dd($cek_produksi);
            // kalau data adalah orang produksi
            if ($cek_restrict == null) {
                $data['pesan'] = "<span class='text-red'>Tidak Boleh Absen Di Sini</span><br/> Hanya untuk <strong>RQA</strong>. Kamu adalah: <strong>" . $my_data->divisi . ", " . $my_data->dept . "</strong>";
                $data['notif'] = "failed";
                $data['status'] = "tidak-boleh";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            }
        }

        // Cek apakah nik nya ada - untuk mengecek yang absen adalah security atau ISS
        if (strpos($nik->NIK, '-') == false) {
            $data['pesan'] = "Tidak Boleh absen di sini";
            $data['notif'] = "failed";
            $data['status'] = "tidak-boleh";
            $data['spesial_information'] = $spesial_information;
            return ['data' => $data];
        }


        // Ini untuk absensi staff
        // $staff = DB::connection('192.168.154.37')
        // ->table('masteremployee')
        // ->select(DB::raw('`Kode Bagian` AS dept'))
        // ->where('Aktif', '1')
        // ->where('Endda', '9998-12-31')
        // ->where('nip', $nik->NIK)
        // ->first();

        if ($my_data == null) {
            $staff = 'N';
            $data['staff'] = $staff;
        } else {
            if ($my_data->staff == 'Y') {
                $staff = 'Y';
                $data['staff'] = $staff;

                // Insert absensi ke database staff
                // Jangan insert jika 105 dan 205
                if (substr($nik->NIK, 0, 3) != '105' && substr($nik->NIK, 0, 3) != '205') {
                    $staff = DB::connection('192.168.154.37')
                        ->table('absensiraw')
                        ->insert(
                            [
                                'Tgl' =>  date('Y-m-d H:i:s'),
                                'Nip' => $nik->NIK,
                                'Mesin' => 'PAS',
                                'IPAddress' => '100',
                                'FlagTrans' => 'N'
                            ]
                        );
                }
            } else {
                $staff = 'N';
                $data['staff'] = $staff;
            }
        }


        // Cek apakah belum absen.

        $cek = DB::connection('localhost')
            ->table('t_absensi_raw')
            ->where('idcard', $data['idcard'])
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($cek == null) {
            // Ini jika belum ada data sama sekali
            // Maka absen masuk dibuat
            $imageName = 'MASUK-' . uniqid() . '.' . 'jpeg';

            // Jika dia absen si ke esokan hari nya maka buat absen baru
            // Yang sebelumnya dibiarkan kosong
            $insert = DB::connection('localhost')
                ->table('t_absensi')
                ->insert([
                    'idcard' => $data['idcard'],
                    'nik' => $data['nik'],
                    'nama' => $data['nama'],
                    'dept' => $data['dept'],
                    'tgl_in' => date('Ymd'),
                    'jam_in' => date('H:i:s'),
                    'gambar_masuk' => $imageName,
                    'location' => $request->location,
                    'mesin' => 'PAS',
                    'ip_address' => '100',
                    'staff' => $staff
                ]);
            $data['tgl_absen'] = date('Ymd');
            $data['jam_absen'] = date('H:i:s');
            $data['location'] = $request->location;
            $data['image'] = $imageName;
            $data['mesin'] = 'PAS';
            $data['ip_address'] = '100';
            $data['absen_time'] = date('d/m/Y') . ' ' . date('H:i:s');
            $data['pesan'] = "Absen Berhasil";
            $data['notif'] = "success";
            $data['status'] = "masuk";
            $data['spesial_information'] = $spesial_information;

            $this->absen_raw($data);

            // Upload gambar
            $image = $request->data_gambar;  // your base64 encoded
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            File::put(app()->basePath('public/absen_images/') . $imageName, base64_decode($image));
            return ['data' => $data];
        } else {
            $start  = new Carbon($cek->tgl . ' ' . $cek->jam);
            $end    = new Carbon(date('Y-m-d') . ' ' . date('H:i:s'));

            $diff = $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
            $diff_arr = explode(':', $diff);

            if ($diff_arr[0] == 0 && $diff_arr[1] <= 5) {
                $data['absen_time'] = $this->dateFormat($cek->tgl) . ' ' . $cek->jam;
                $data['pesan'] = "<span class='text-red'>Kamu Sudah Absen</span>";
                $data['status'] = "sudah-absen";
                $data['notif'] = "failed";
                $data['spesial_information'] = $spesial_information;
                return ['data' => $data];
            } else {
                $imageName = 'MASUK-' . uniqid() . '.' . 'jpeg';

                $insert = DB::connection('localhost')
                    ->table('t_absensi')
                    ->insert([
                        'idcard' => $data['idcard'],
                        'nik' => $data['nik'],
                        'nama' => $data['nama'],
                        'dept' => $data['dept'],
                        'tgl_in' => date('Ymd'),
                        'jam_in' => date('H:i:s'),
                        'gambar_masuk' => $imageName,
                        'location' => $request->location,
                        'mesin' => 'PAS',
                        'ip_address' => '100',
                        'staff' => $staff
                    ]);

                $data['tgl_absen'] = date('Ymd');
                $data['jam_absen'] = date('H:i:s');
                $data['location'] = $request->location;
                $data['image'] = $imageName;
                $data['mesin'] = 'PAS';
                $data['ip_address'] = '100';
                $data['absen_time'] = date('d/m/Y') . ' ' . date('H:i:s');
                $data['pesan'] = "Absen Berhasil";
                $data['notif'] = "success";
                $data['status'] = "masuk";
                $data['spesial_information'] = $spesial_information;
                $this->absen_raw($data);
                // Upload gambar
                $image = $request->data_gambar;  // your base64 encoded
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                File::put(app()->basePath('public/absen_images/') . $imageName, base64_decode($image));
                return ['data' => $data];
            }
        }
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

        return response()->json([
            'success' => true,
            'message' => 'Absen berhasil',
            'data' => $this->formatVisitorData($visitor, $sourceOrigin, $activity, $statusText),
        ]);
    }

    protected function getFotoUrl($visitor)
    {
        $fotoArray = json_decode($visitor->foto, true);
        return is_array($fotoArray) && !empty($fotoArray) ? $fotoArray[0] : 'https://via.placeholder.com/300';
    }

    private function catatLogGagal($visitor, $sourceOrigin, $catatan = '')
    {
        dd($visitor, $sourceOrigin);

        AbsensiRestLog::create([
            'trnvisitorid' => $visitor->trnvisitorid,
            'source_origin' => $sourceOrigin,
            'activity_type' => 'failed',
            'scan_time' => Carbon::now(),
            'tanggal_log' => Carbon::now()->toDateString(),
            'catatan' => $catatan,
        ]);
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
                'type' =>  $sourceOrigin === 'ga_visitor_vendor_transactions' ? 'vendor' : 'tamu',
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
                'created_at' => $visitor->created_at->format('d-m-Y H:i') ?? '-',
            ],
            'status_kartu' => $visitor->kartu_dikembalikan ? 'dikembalikan' : 'aktif',
            'status_display' => $visitor->kartu_dikembalikan ? 'Kartu Sudah Dikembalikan' : 'Kartu Aktif',
        ];
    }
}
