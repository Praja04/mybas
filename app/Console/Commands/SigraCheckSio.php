<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\SIO;
use App\Models\Sigra\SIOSertifikasi;
use App\Mail\Sigra\SIO as EmailSIO;

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

    function sendEmail($emails, $sertifikat)
    {
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
        // Get emails
        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'SIO')->get();

        $sioList = SIO::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($sioList as $key => $data) {
            $sertifikasi = SIOSertifikasi::where('id_sio', $data->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_terbit', 'desc')->first();

            // if ($sertifikasi != null) {
            //     if ($this->expired($sertifikasi->tanggal_habis) <= 30) {

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_habis);
                // buat kondisi kurang dari 30 hari dan tidak melewati dari 60 hari
                if ($selisih_hari <= 30 && $selisih_hari >= -60) {
                    // beresin yang mau digunakan di blade
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_perizinan = $data->nama_perizinan;
                    $sertifikasi->nama_karyawan = $data->nama_karyawan;
                    $sertifikasi->nik_karyawan = $data->nik_karyawan;
                    $sertifikasi->nomor_izin = $sertifikasi->nomor_izin;
                    $sertifikasi->due_date = $this->expired($sertifikasi->tanggal_habis);
                    $this->info('sudah mau expired .. ' . $sertifikasi->tanggal_habis . ' due date ' . $sertifikasi->due_date);
                    $certificates[] = $sertifikasi;
                }
            }
        }

        $this->sendEmail($emails, $certificates);
    }
}
