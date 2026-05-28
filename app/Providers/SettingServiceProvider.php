<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $organizationId = Auth::check()
                ? Auth::user()->organization_id
                : app(TenantContext::class)->id();

            if (! $organizationId) {
                return;
            }

            $cacheKey = 'app_settings_' . $organizationId;

            $settings = cache()->rememberForever($cacheKey, function () use ($organizationId) {
                return Setting::withoutGlobalScopes()
                    ->where('organization_id', $organizationId)
                    ->first();
            });

            if ($settings) {
                View::share('globalSettings', $settings);
                config(['settings' => $settings->toArray()]);
            }
        });
    }
}
