<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for freelancers and small businesses getting started.',
                'price' => 0,
                'interval' => 'month',
                'features' => ['Up to 5 invoices/month', '1 user', 'PDF export', 'Email invoices'],
                'max_invoices' => 5,
                'max_users' => 1,
                'payment_gateway_enabled' => false,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing businesses that need payment collection.',
                'price' => 29.99,
                'interval' => 'month',
                'features' => ['Unlimited invoices', '5 users', 'Stripe payments', 'Reports & analytics', 'Webhook integrations'],
                'max_invoices' => null,
                'max_users' => 5,
                'payment_gateway_enabled' => true,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Advanced features for teams with high volume invoicing.',
                'price' => 79.99,
                'interval' => 'month',
                'features' => ['Unlimited everything', '25 users', 'Priority support', 'Custom branding', 'API access'],
                'max_invoices' => null,
                'max_users' => 25,
                'payment_gateway_enabled' => true,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
