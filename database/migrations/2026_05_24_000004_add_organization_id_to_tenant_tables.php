<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        foreach (['settings', 'customers', 'products', 'invoices', 'webhook_settings'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                });
            }
        }

        if (Schema::hasTable('settings') && ! Schema::hasColumn('settings', 'payment_gateway_enabled')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('payment_gateway_enabled')->default(false)->after('stripe_secret_key');
            });
        }

        $this->backfillExistingData();
    }

    private function backfillExistingData(): void
    {
        if (! Schema::hasTable('organizations') || DB::table('organizations')->exists()) {
            return;
        }

        $setting = DB::table('settings')->first();
        $companyName = $setting->company_name ?? 'Default Organization';

        $orgId = DB::table('organizations')->insertGetId([
            'name' => $companyName,
            'slug' => Str::slug($companyName) . '-' . Str::random(4),
            'email' => $setting->contact_email ?? null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['users', 'settings', 'customers', 'products', 'invoices', 'webhook_settings'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                DB::table($tableName)->whereNull('organization_id')->update(['organization_id' => $orgId]);
            }
        }

        $adminUser = DB::table('users')->where('role', 'admin')->first();
        if ($adminUser) {
            DB::table('organizations')->where('id', $orgId)->update(['owner_id' => $adminUser->id]);
        }
    }

    public function down(): void
    {
        foreach (['users', 'settings', 'customers', 'products', 'invoices', 'webhook_settings'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('organization_id');
                });
            }
        }

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'payment_gateway_enabled')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('payment_gateway_enabled');
            });
        }
    }
};
