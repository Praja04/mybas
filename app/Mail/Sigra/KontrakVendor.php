<?php

namespace App\Mail\Sigra;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KontrakVendor extends Mailable
{
    use Queueable, SerializesModels;

    private $kontrak_vendor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($kontrak_vendor)
    {
        $this->kontrak_vendor = $kontrak_vendor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.sigra.kontrak-vendor', [
            'kontrak_vendor' => $this->kontrak_vendor
        ]);
    }
}
