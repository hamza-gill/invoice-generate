<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'company_name'             => 'ReconX',
                'tax_id'                   => '123-456-789',
                'country'                  => 'United States',
                'base_currency'            => 'USD - US Dollar',
                'address'                  => '123 Business Center, City',
                'logo_path'                => 'uploads/settings/logo.png',

                'invoice_notes'            => 'Thank you for your business. Please contact info@reconx.com for any queries.',
                'invoice_terms'            => 'Payment is due within 30 days from invoice date.',

                'tax_percentage'           => 8.25,

                'stripe_public_key'        => 'pk_test_demo123456',
                'stripe_secret_key'        => 'sk_test_demo123456',

                'webhook_url'              => secure_url('/webhook'),
                'webhook_secret'           => 'whsec_demo123456',

                'contact_email'            => 'info@reconx.com',

                'enable_terms'             => true,
                'enable_invoice_notes'     => true,
                'enable_tax'               => true,
                'enable_tax_id'            => true,
                'enable_due_date'          => true,
                'enable_rush_delivery'     => false,

                'rush_delivery_options'    => json_encode([
                    ['label' => 'Same Day', 'price' => 50],
                    ['label' => 'Next Day', 'price' => 30],
                ]),

                // ✅ Dynamic year
                'starting_invoice_number'  => 'INV-' . now()->year . '-001',

                'google_places_key'        => 'AIzaSyDemoKey123456',

                'created_at'               => now(),
                'updated_at'               => now(),
            ]
        );
    }
}
