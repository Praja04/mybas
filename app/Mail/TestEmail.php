<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $timestamp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->timestamp = Carbon::now()->format('Y-m-d H:i:s');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Test Email MyBAS - ' . $this->timestamp)
            ->view('mail.test')
            ->with([
                'timestamp' => $this->timestamp,
            ]);
    }
}
