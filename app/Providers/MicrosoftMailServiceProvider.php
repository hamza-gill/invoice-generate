<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Services\MicrosoftMailer;

class MicrosoftMailServiceProvider extends ServiceProvider
{
    public function boot(MicrosoftMailer $msMailer): void
    {
        try {
            // Always configure transport on app boot
            Mail::mailer('smtp')->setSymfonyTransport($msMailer->getTransport());
        } catch (\Exception $e) {
            \Log::warning("⚠️ Microsoft OAuth2 mail boot setup failed: " . $e->getMessage());
        }
    }
}
