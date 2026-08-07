<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MicrosoftMailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Per-organization mail configuration is resolved at send-time by
        // App\Services\MailConfigurationService. No global transport override here.
    }
}
