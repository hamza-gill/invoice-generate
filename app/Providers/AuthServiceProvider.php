<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Policies\CustomerPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        \App\Models\Setting::class => \App\Policies\SettingPolicy::class,
        \App\Models\WebhookSetting::class => \App\Policies\WebhookSettingPolicy::class,
        \App\Models\Customer::class => \App\Policies\CustomerPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        $this->registerPolicies();

        // Admin and Developer bypass all checks
        Gate::before(function ($user, $ability) {

            if (in_array($user->role, ['admin', 'developer'])) {
                return true;
            }
            return null;
        });

        // Role-based gates
        Gate::define('manage-records', fn($user) => in_array($user->role, ['admin', 'developer', 'manager']));
        Gate::define('view-records', fn($user) => in_array($user->role, ['admin', 'developer', 'manager', 'employee']));
        Gate::define('manage-system', fn($user) => in_array($user->role, ['admin', 'developer']));
        Gate::define('developer-tools', fn($user) => $user->role === 'developer');
    }
}
