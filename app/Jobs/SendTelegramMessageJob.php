<?php

namespace App\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; 

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newJumlahStock, $berasJumlahTerakhir, $jumlah_pengambilan, $newIdStockBerasJumlah;

    public function __construct($newJumlahStock, $berasJumlahTerakhir, $jumlah_pengambilan, $newIdStockBerasJumlah)
    {
        $this->newJumlahStock = $newJumlahStock;
        $this->berasJumlahTerakhir = $berasJumlahTerakhir;
        $this->jumlah_pengambilan = $jumlah_pengambilan;
        $this->newIdStockBerasJumlah = $newIdStockBerasJumlah;
    }

    public function handle()
    {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $telegramChatId = env('TELEGRAM_CHAT_ID');
        $guzzleClient = new Client(['verify' => false]);

        $localImagePath = 'D:\PAS\mybas\public\assets\mazer\dist\assets\compiled\png\rice-vektor.png';
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
        } catch (GuzzleException $e) {
            Log::error('GuzzleException on sending image to Telegram: ' . $e->getMessage());
        } finally {
            if (is_resource($imageResource)) {
                fclose($imageResource);
            }
        }

        $message = "<pre>---- Laporan Pengambilan Beras ----</pre>";
        if (is_object($this->berasJumlahTerakhir)) {
            $message .= "<pre>Stock Sebelum Pengambilan: " . $this->berasJumlahTerakhir->jumlah_stock_sesudah . " sak</pre>";
        } else {
            $message .= "<pre>Stock Sebelum Pengambilan: Data tidak tersedia</pre>";
        }
        $message .= "<pre>Jumlah Pengambilan: " . $this->jumlah_pengambilan . " sak</pre>";
        $message .= "<pre>Stock Setelah Pengambilan: " . $this->newJumlahStock . " sak</pre>";
        $message .= "<pre>ID Stock Baru: " . $this->newIdStockBerasJumlah . "</pre>";
        $message .= "<pre>-----------------------------------</pre>";

        try {
            $response = $guzzleClient->post("https://api.telegram.org/bot$telegramBotToken/sendMessage", [
                'form_params' => [
                    'chat_id' => $telegramChatId,
                    'parse_mode' => 'HTML',
                    'text' => $message,
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::error('GuzzleException on sending text message to Telegram: ' . $e->getMessage());
        }
    }
}