<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['slug' => 'inveqi-demo'],
            [
                'name' => 'Inveqi Demo',
                'email' => 'admin@example.com',
                'status' => 'active',
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'role' => 'admin',
                'password' => Hash::make('Admin@123'),
                'status' => 'active',
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'accountant@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Accountant',
                'role' => 'accountant',
                'password' => Hash::make('Accountant@123'),
                'status' => 'active',
                'organization_id' => $organization->id,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $admin = DB::table('users')->where('email', 'admin@example.com')->first();
        $organization->update(['owner_id' => $admin->id]);

        Setting::withoutGlobalScopes()->updateOrCreate(
            ['organization_id' => $organization->id],
            [
                'company_name' => 'Inveqi',
                'tax_id' => '123-456-789',
                'country' => 'United States',
                'base_currency' => 'USD - US Dollar',
                'address' => '123 Business Center, City',
                'contact_email' => 'info@inveqi.com',
                'stripe_public_key' => 'pk_test_demo123456',
                'stripe_secret_key' => 'sk_test_demo123456',
                'payment_gateway_enabled' => true,
                'enable_terms' => true,
                'enable_invoice_notes' => true,
                'enable_tax' => true,
                'enable_tax_id' => true,
                'enable_due_date' => true,
                'starting_invoice_number' => 'INV-' . now()->year . '-001',
            ]
        );
    }
}
