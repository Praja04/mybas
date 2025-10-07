<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\Legalitas;
use App\Models\Sigra\SertifikasiLegalitas;
use App\Mail\Sigra\Legalitas as EmailLegalitas;

class SigraChecklegalitas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-legalitas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari legalitas';

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
        // Get an emails
        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'legalitas')->get();

        foreach ($emails as $email) {
            Mail::to($email->email_penerima)->send(new EmailLegalitas($sertifikat));
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

        $legalitas = Legalitas::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($legalitas as $key => $data) {
            $sertifikasi = SertifikasiLegalitas::where('id_legalitas', $data->id)->where('status', '!=', 'deleted')->orderBy('tanggal_habis', 'desc')->first();


            // if ($sertifikasi != null && $sertifikasi->tanggal_habis != null) {
            //     if ($this->expired($sertifikasi->tanggal_habis) <= 30) {

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_habis);
                // buat kondisi kurang dari 30 hari dan tidak melewati dari 60 hari
                if ($selisih_hari <= 30 && $selisih_hari >= -60) {
                    // Di sini dilakukan send notifikasi
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_legalitas = $data->nama_legalitas;
                    $sertifikasi->due_date = $this->expired($sertifikasi->tanggal_habis);
                    $this->info('Udah mau expired nih.. ' . $sertifikasi->tanggal_habis . ' due date ' . $sertifikasi->due_date);
                    $certificates[] = $sertifikasi;
                }
            }
        }

        $this->sendEmail($certificates);
    }
}
