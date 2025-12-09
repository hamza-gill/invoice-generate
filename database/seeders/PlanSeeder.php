<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free-plan',
                'description' => 'A free starter plan for small usage.',
                'price' => 0.00,
                'currency' => 'USD',
                'duration_days' => 30,
                'features' => json_encode([
                    'Basic dashboard access',
                    'Email support',
                ]),
                'limit_count' => 20, // 20 invoices / credits
                'is_active' => 1,
            ],
            [
                'name' => 'Basic Plan',
                'slug' => 'basic-plan',
                'description' => 'A basic plan suitable for freelancers.',
                'price' => 19.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'features' => json_encode([
                    'All Free plan features',
                    '100 invoices per month',
                    'Priority email support',
                ]),
                'limit_count' => 100,
                'is_active' => 1,
            ],
            [
                'name' => 'Pro Plan',
                'slug' => 'pro-plan',
                'description' => 'Perfect for small businesses.',
                'price' => 49.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'features' => json_encode([
                    'All Basic plan features',
                    '500 invoices per month',
                    'Phone support',
                ]),
                'limit_count' => 500,
                'is_active' => 1,
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise-plan',
                'description' => 'High-volume plan for large companies.',
                'price' => 199.99,
                'currency' => 'USD',
                'duration_days' => 30,
                'features' => json_encode([
                    'All Pro plan features',
                    'Unlimited invoices',
                    'Dedicated account manager',
                ]),
                'limit_count' => 0, // 0 = unlimited
                'is_active' => 1,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
