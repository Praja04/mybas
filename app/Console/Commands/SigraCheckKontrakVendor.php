<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Sigra\KontrakVendor;
use App\Models\Sigra\MasterVendor;
use App\Mail\Sigra\KontrakVendor as EmailKontrakVendor;

class SigraCheckKontrakVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-kontrak-vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek status expired dari kontrak vendor';

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
            ->where('jenis', 'kontrak_vendor')->get();

        foreach ($emails as $email) {
            Mail::to($email->email_penerima)->send(new EmailKontrakVendor($sertifikat));
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

        $kontrak_vendor = MasterVendor::where('status', '!=', 'deleted')
            ->where('status', '!=', 'inactive')->get();

        foreach ($kontrak_vendor as $key => $data) {
            $sertifikasi = KontrakVendor::where('id_vendor', $data->id)
                ->where('status', '!=', 'deleted')
                ->orderBy('tanggal_selesai', 'desc')
                ->first();

            // if($sertifikasi != null) {
            //     if($this->expired($sertifikasi->tanggal_selesai) <= 30)
            //     {

            if ($sertifikasi != null) {
                $selisih_hari = $this->expired($sertifikasi->tanggal_selesai);
                if ($selisih_hari <= 30 && $selisih_hari >= -60) {
                    // Kirim email hanya jika selisih kurang dari atau sama dengan 30 hari dan belum lewat dari 60 hari
                    $sertifikasi->perusahaan = $data->perusahaan->nama_perusahaan;
                    $sertifikasi->nama_vendor = $data->nama_vendor;
                    $sertifikasi->jenis_pekerjaan = $data->jenis_pekerjaan;
                    $sertifikasi->due_date = $selisih_hari;
                    $this->info('Udah mau expired nih.. ' . $sertifikasi->tanggal_selesai . ' due date ' . $sertifikasi->due_date);
                    $certificates[] = $sertifikasi;
                }
            }
        }

        $this->sendEmail($certificates);
    }
}
