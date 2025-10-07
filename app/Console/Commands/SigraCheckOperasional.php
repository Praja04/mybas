<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\Operasional;
use App\Models\Sigra\SertifikasiOperasional;
use App\Mail\Sigra\Operasional as EmailOperasional;

class SigraCheckOperasional extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-operasional';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari operasional';

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
            Mail::mailer(setEmail($email->email_penerima))->to($email->email_penerima)->send(new EmailOperasional($sertifikat));
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
        // Get an emails
        $emails = DB::table('sigra_email_penerima')
            ->where('jenis', 'operasional')->get();

        $operasional = Operasional::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($operasional as $key => $data) {
            $sertifikasi = SertifikasiOperasional::where('id_operasional', $data->id)->where('status', '!=', 'deleted')->orderBy('tahun', 'desc')->first();

            // if ($sertifikasi != null) {
            //     if ($this->expired($sertifikasi->tanggal_expired) <= 30) {

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_expired);
                // buat kondisi kurang dari 30 hari dan tidak melewati dari 60 hari
                if ($selisih_hari <= 30 && $selisih_hari >= -60) {
                    // Di sini dilakukan send notifikasi
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_perizinan = $data->nama_perizinan;
                    $sertifikasi->nomor_perizinan = $data->nomor_perizinan;
                    $sertifikasi->due_date = $this->expired($sertifikasi->tanggal_expired);
                    $this->info('Udah mau expired nih.. ' . $sertifikasi->tanggal_expired . ' due date ' . $sertifikasi->due_date);
                    $certificates[] = $sertifikasi;
                }
            }
        }

        $this->sendEmail($emails, $certificates);
    }
}
