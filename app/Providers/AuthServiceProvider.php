<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
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
        // Define Gates for Reports
        Gate::define('view-reports', function (User $user) {
            return in_array($user->role, ['admin', 'developer', 'manager']);
        });

        Gate::define('export-reports', function (User $user) {
            return in_array($user->role, ['admin', 'developer', 'manager']);
        });

        Gate::define('view-financial-reports', function (User $user) {
            return in_array($user->role, ['admin', 'developer', 'manager']);
        });

        Gate::define('view-analytics', function (User $user) {
            return in_array($user->role, ['admin', 'developer', 'manager']);
        });

//        / You can also add role-based gates
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('is-developer', function (User $user) {
            return $user->role === 'developer';
        });

        Gate::define('is-manager', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('is-employee', function (User $user) {
            return $user->role === 'employee';
        });
    }
}
