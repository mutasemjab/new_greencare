<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public int $expiryMinutes;

    public function __construct(string $otp, int $expiryMinutes)
    {
        $this->otp = $otp;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function build()
    {
        return $this->subject('رمز التحقق - Green Care')
            ->view('emails.otp');
    }
}
