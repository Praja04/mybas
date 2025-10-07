<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class sendPemakaianMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newJumlahPengambilan;
    protected $berasJumlahTerakhir;
    protected $jumlah_pemakaian;
    protected $newIdStock;

    public function __construct($newJumlahPengambilan, $berasJumlahTerakhir, $jumlah_pemakaian, $newIdStock)
    {
        $this->newJumlahPengambilan = $newJumlahPengambilan;
        $this->berasJumlahTerakhir = $berasJumlahTerakhir;
        $this->jumlah_pemakaian = $jumlah_pemakaian;
        $this->newIdStock = $newIdStock;
    }

    public function handle()
    {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $telegramChatId = env('TELEGRAM_CHAT_ID');
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);


        $localImagePath = 'D:\PAS\mybas\public\assets\mazer\dist\assets\compiled\png\literan.png';
        $imageResource = fopen($localImagePath, 'r');

        try {
            $response = $guzzleClient->post("https://api.telegram.org/bot$telegramBotToken/sendPhoto", [
                'multipart' => [
                    [
                        'name' => 'chat_id',
                        'contents' => $telegramChatId
                    ],
                    [
                        'name' => 'photo',
                        'contents' => $imageResource
                    ]
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error('Failed to send image to Telegram.');
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error('GuzzleException on sending image to Telegram: ' . $e->getMessage());
        } finally {
            if (is_resource($imageResource)) {
                fclose($imageResource);
            }
        }

        $message = "---- Laporan pemakaian Beras ----\n";
        if (is_object($this->berasJumlahTerakhir)) {
            $message .= "Stock Sebelum pemakaian: " . $this->berasJumlahTerakhir->jumlah_pengambilan_sesudah . " sak\n";
        } else {
            $message .= "Stock Sebelum pemakaian: Data tidak tersedia\n";
        }
        $message .= "Jumlah Pemakaian: " . $this->jumlah_pemakaian . " sak\n";
        $message .= "Stock Setelah Pemakaian: " . $this->newJumlahPengambilan . " sak\n";
        $message .= "ID Stock Baru: " . $this->newIdStock . "\n";
        $message .= "-----------------------------------\n";

        try {
            // Kirim pesan teks ke Telegram
            $response = $guzzleClient->post("https://api.telegram.org/bot$telegramBotToken/sendMessage", [
                'form_params' => [
                    'chat_id' => $telegramChatId,
                    'text' => $message,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error('Failed to send message to Telegram.');
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error('GuzzleException on sending message to Telegram: ' . $e->getMessage());
        }
    }
}
