<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Mail\Sigra\SIO as EmailSIO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;

class SendSioEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
<<<<<<< Updated upstream
    {
        // Log::info('SendSioEmailJob STARTED - DISABLED TEMPORARILY');
        return; // Matikan sementara
=======
>>>>>>> Stashed changes
        Log::info('SendSioEmailJob STARTED');

        $certificates = [];

        $sioList = SIO::with(['department', 'perusahaan', 'sertifikasi'])
            ->where('status', 'active')
            ->get();

        foreach ($sioList as $data) {
            $sertifikasi = $data->sertifikasi->sortByDesc('tanggal_terbit')->first();

            if (!$sertifikasi) {
                continue;
            }

            $selisih_hari = (strtotime($sertifikasi->tanggal_habis) - strtotime(date('Y-m-d'))) / 86400;

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

        if (empty($certificates)) {
            Log::info('Tidak ada SIO expired');
            return;
        }

        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'SIO')
            ->where('active', 'Y')
            ->pluck('email_penerima');

        foreach ($emails as $email) {
            Mail::to($email)->send(new EmailSIO($certificates));
        }

        Log::info('SendSioEmailJob FINISHED');
    }
}
