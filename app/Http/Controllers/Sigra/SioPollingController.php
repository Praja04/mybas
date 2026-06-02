<?php

namespace App\Http\Controllers\Sigra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Mail\Sigra\SIO as EmailSIO;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SioPollingController extends Controller
{
    public function expired($expired_date)
    {
        return (strtotime($expired_date) - strtotime(date('Y-m-d'))) / 86400; // hitung hari 
    }

    // public function sendEmail($sertifikat)
    // {
    //     return; // Matikan sementara
    //     $emails = DB::table('sigra_email_penerima')
    //         ->where('jenis', 'SIO')
    //         ->where('active', 'Y')
    //         ->get();

    //     foreach ($emails as $email) {
    //         Mail::to($email->email_penerima)->send(new EmailSIO($sertifikat));
    //     }
    // }

    public function checkSio()
    {
        try {
            // $now = Carbon::now();

            // if (
            //     $now->dayOfWeek !== Carbon::WEDNESDAY ||
            //     ($now->hour !== 15 && $now->hour !== 16)
            // ) { 
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Belum waktunya polling SIO',
            //         'server_time' => $now->format('Y-m-d H:i:s')
            //     ], 403);
            // }

            // lock agar 1x kirim per hari
            // $cacheKey = 'sio_polling_sent_' . $now->format('Y-m-d');

            // if (cache()->has($cacheKey)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Polling hari ini sudah dijalankan. Email tidak dikirim ulang.',
            //         'server_time' => $now->format('Y-m-d H:i:s')
            //     ], 200);
            // }

            $certificates = [];

            $sioList = SIO::with('department')
                // ->where('status', '!=', 'deleted')
                // ->where('status', '!=', 'inactive')
                ->where('status', 'active')
                ->get();

            foreach ($sioList as $data) {
                $sertifikasi = SIOSertifikasi::where('id_sio', $data->id)
                    ->where('status', '!=', 'deleted')
                    ->orderBy('tanggal_terbit', 'desc')
                    ->first();

                if ($sertifikasi) {
                    $selisih_hari = $this->expired($sertifikasi->tanggal_habis);

                    if ($selisih_hari <= 60 && $selisih_hari >= -60) {
                        $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan ?? '-';
                        $sertifikasi->nama_perizinan = $data->nama_perizinan;
                        $sertifikasi->nama_karyawan = $data->nama_karyawan;
                        $sertifikasi->nik_karyawan = $data->nik_karyawan;
                        $sertifikasi->department = $data->department->name ?? '-';
                        $sertifikasi->tanggal_mulai_ikatan_dinas = $data->tanggal_mulai_ikatan_dinas;
                        $sertifikasi->tanggal_selesai_ikatan_dinas = $data->tanggal_selesai_ikatan_dinas;
                        $sertifikasi->nomor_izin = $sertifikasi->nomor_izin;
                        $sertifikasi->due_date = $selisih_hari;

                        $certificates[] = $sertifikasi;
                    }
                }
            }

            if (!empty($certificates)) {
                $this->sendEmail($certificates);

                // cache()->put($cacheKey, true, now()->endOfDay());

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengirimkan email notifikasi SIO melalui polling API.',
                    'total' => count($certificates)
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tidak ada sertifikat SIO yang akan expired.',
                'total' => 0
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Polling SIO Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error polling SIO API, retry...',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
