<?php

namespace App\Events\HR;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Perubahan thread diskusi PK (Penilaian Karya).
 *
 * DTO murni: seluruh data payload dibangun oleh pemanggil (controller),
 * event ini hanya mengarahkan ke kanal privat per-NIK penerima.
 */
class PkDiskusiChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * created | updated | answered
     *
     * @var string
     */
    public $type;

    /**
     * NIK penerima (peserta, pengaju, penjawab, manager, associate manager).
     *
     * @var array<int, string>
     */
    public $niks;

    /**
     * @var array<string, mixed>
     */
    public $payload;

    public function __construct($type, array $niks, array $payload)
    {
        $this->type = $type;
        $this->niks = array_values(array_unique(array_filter(array_map(function ($nik) {
            return trim((string) $nik);
        }, $niks), function ($nik) {
            return $nik !== '';
        })));
        $this->payload = $payload;
    }

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn()
    {
        return array_map(function ($nik) {
            return new PrivateChannel('pk-diskusi.' . $nik);
        }, $this->niks);
    }

    public function broadcastAs()
    {
        return 'pk-diskusi-' . $this->type;
    }

    public function broadcastWith()
    {
        return $this->payload;
    }
}
