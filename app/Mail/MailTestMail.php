<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $companyName;

    public function __construct(Setting $setting)
    {
        $this->companyName = $setting->company_name ?? config('app.name');
    }

    public function build()
    {
        return $this->subject('Test email from ' . $this->companyName)
            ->view('emails.mail-test');
    }
}
