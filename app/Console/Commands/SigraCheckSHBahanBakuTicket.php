<?php

namespace App\Console\Commands;

use App\Mail\Purchasing\SertifikatExpiredInfoMail;
use App\Mail\Sigra\ConfirmTicketInfoMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SigraCheckSHBahanBakuTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sigra:check-sh-bahan-baku-ticket {status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk cek tiket sertifikat SH bahan baku';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $status = $this->argument('status');
        $this->info('--------------------------');
        $this->info('-> Memulai proses');

        $this->info('Mengambil data tiket..');
        // Cek tiket yang akan dikirim ke vendor dan purchasing
        $tickets = DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket')
            ->where('status', $status)
            ->get();

        $this->info('Menemukan ' . count($tickets) . ' tiket..');

        if($status == 1) {
            $this->handleSendNotificationToVendor($tickets);
        }elseif($status == 2) {
            $this->handleSendNotificationToRND($tickets);
        }

        $this->info('--------------------------');
    }

    private function handleSendNotificationToVendor($tickets) {
        $this->info('Kirim email ke purchasing dan vendor');
        $this->info('Mulai mengirim email..');
        foreach($tickets as $ticket)
        {
            $this->info('Mencari data pic');
            // Get PIC
            $pic = DB::table('purchasing_po_information_pic')
                ->where('plant', $ticket->plant)
                ->where('vendor_code', $ticket->vendor_code)
                ->get();

            $this->info(count($pic). ' PIC ditemukan');

            $this->info('Mengirim email ke PIC');
            $emails = $pic->where('as', 'to')->pluck('email')->toArray();
            $ccEmails = $pic->where('as', 'cc')->pluck('email')->toArray();

            $ticket->link = 'https://pas-certificate-upload.web.app?ticket=' . $ticket->nomor_tiket;
            
            try {
                Mail::to($emails)
                ->cc($ccEmails)
                ->send(new SertifikatExpiredInfoMail($ticket));
                $this->warn('Email berhasil dikirim ke Cc ' . implode(', ', $emails));
                $this->warn('Email berhasil dikirim ke To ' . implode(', ', $ccEmails));
                $is_success = 'Y';
                $failed_message = null;
            }catch(\Exception $e) {
                $is_success = 'N';
                $failed_message = $e->getMessage();
                $this->error('Gagal mengirim email ke PIC');
                $this->error($e->getMessage());
            }

            DB::table('sigra_sertifikat_sh_bahan_baku_expired_tiket_send_history')
            ->insert([
                'nomor_tiket' => $ticket->nomor_tiket,
                'is_success' => $is_success,
                'failed_message' => $failed_message,
                'to' => implode(', ', $emails),
                'cc' => implode(', ', $ccEmails),
                'sent_at' => now()
            ]);
        }
    }

    private function handleSendNotificationToRND($tickets) {
        $this->info('Kirim email ke RND');
        $this->info('Mulai mengirim email..');
        $emails = ['irvan.ramadhani@pt-pas-id.com'];
        $ccEmails = ['okta.setiawan@pt-pas-id.com', 'herilesmanapribadi@gmail.com'];
        try {
            Mail::to($emails)
            ->cc($ccEmails)
            ->send(new ConfirmTicketInfoMail($tickets));

            Mail::mailer(setEmail('adellarafa.naura@myemail.pas'))
            ->to('adellarafa.naura@myemail.pas')
            ->send(new ConfirmTicketInfoMail($tickets));
            $this->warn('Email berhasil dikirim ke Cc ' . implode(', ', $emails));
            $this->warn('Email berhasil dikirim ke To ' . implode(', ', $ccEmails));
            \File::append(base_path() . '/storage/logs/sigra/need-confirmation-certificate.log', "-> [SUCCEED] " . date('Y-m-d H:i:s') . " :: Notify need confirmation ticket. There is " . count($tickets) . " notified \n");
        }catch(\Exception $e) {
            \File::append(base_path() . '/storage/logs/sigra/need-confirmation-certificate.log', "-> [FAILED!] " . date('Y-m-d H:i:s') . " :: Notify need confirmation ticket. There is " . count($tickets) . " notified \n" . $e->getMessage() . " \n");
        }

    }
}
