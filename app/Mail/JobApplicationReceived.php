<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class JobApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        $mail = $this->subject('طلب توظيف جديد - ' . $this->application->position)
            ->view('emails.job_application');

        if ($this->application->cv) {
            $mail->attach(Storage::disk('public')->path($this->application->cv));
        }

        return $mail;
    }
}
