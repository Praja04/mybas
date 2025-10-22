<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;
use App\Mail\Sigra\SIO as EmailSIO;
use Illuminate\Support\Facades\Log;

class SigraCheckSio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-sio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari SIO';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    function expired($expired_date)
    {
        return (strtotime($expired_date) - strtotime(date('Y-m-d'))) / 86400;
    }

    function sendEmail($sertifikat)
    {
        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'SIO')
            ->where('active', 'Y')
            ->get();

        foreach ($emails as $email) {
            Mail::to($email->email_penerima)->send(new EmailSIO($sertifikat));
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $certificates = [];

        $sioList = SIO::with('department')
            ->where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')
            ->get();

        foreach ($sioList as $key => $data) {
            $sertifikasi = SIOSertifikasi::where('id_sio', $data->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_terbit', 'desc')
                ->first();

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_habis);

                // condition: akan expired <= 45 hari, atau sudah expired tapi belum lebih dari 60 hari
                if ($selisih_hari <= 45 && $selisih_hari >= -60) {
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_perizinan = $data->nama_perizinan;
                    $sertifikasi->nama_karyawan = $data->nama_karyawan;
                    $sertifikasi->nik_karyawan = $data->nik_karyawan;
                    $sertifikasi->department = $data->department->name;
                    $sertifikasi->tanggal_mulai_ikatan_dinas = $data->tanggal_mulai_ikatan_dinas;
                    $sertifikasi->tanggal_selesai_ikatan_dinas = $data->tanggal_selesai_ikatan_dinas;

                    $sertifikasi->nomor_izin = $sertifikasi->nomor_izin;
                    $sertifikasi->due_date = $selisih_hari;

                    if ($selisih_hari < 0) {
                        $this->info("[SIGRA SIO] {$sertifikasi->nama_perizinan} ({$sertifikasi->nama_karyawan}) sudah berakhir pada {$sertifikasi->tanggal_habis} (melewati " . abs($selisih_hari) . " hari)");
                    } else {
                        $this->info("[SIGRA SIO] {$sertifikasi->nama_perizinan} ({$sertifikasi->nama_karyawan}) akan berakhir pada {$sertifikasi->tanggal_habis} (dalam {$selisih_hari} hari)");
                    }

                    $certificates[] = $sertifikasi;
                }
            }
        }

        if (!empty($certificates)) {

            $this->sendEmail($certificates);
            $this->info('[SIGRA SIO] Email notifikasi pengingat SIO telah dikirim.');
        } else {
            $this->info('Tidak ada SIO yang akan atau sudah expired. Tidak ada email dikirim.');
        }
    }
}
