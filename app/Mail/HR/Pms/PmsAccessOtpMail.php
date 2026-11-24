<?php

namespace App\Mail\HR\Pms;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PmsAccessOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $expiresMinutes;
    public $name;
    public $requestedAt;
    public $requestedFrom;

    public function __construct($code, $expiresMinutes, $name, $requestedAt = null, $requestedFrom = null)
    {
        $this->code = (string) $code;
        $this->expiresMinutes = (int) $expiresMinutes;
        $this->name = (string) $name;
        $this->requestedAt = $requestedAt;
        $this->requestedFrom = $requestedFrom;
    }

    public function build()
    {
        $fromAddress = (string) config('pms.access_otp.from_address', 'pms@pt-bas-id.com');
        $fromName = (string) config('pms.access_otp.from_name', 'PMS PT Bumi Alam Segar');

        return $this->from($fromAddress, $fromName)
            ->subject('Kode OTP Akses PMS')
            ->view('mail.hr.pms.access-otp', [
                'code' => $this->code,
                'expiresMinutes' => $this->expiresMinutes,
                'name' => $this->name,
                'requestedAt' => $this->requestedAt,
                'requestedFrom' => $this->requestedFrom,
            ])
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-PMS-OTP', '1');
            });
    }
}
