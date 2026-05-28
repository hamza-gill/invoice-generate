<?php

return [
    'trial_days' => (int) env('SUBSCRIPTION_TRIAL_DAYS', 14),

    'stripe_key' => env('STRIPE_PLATFORM_PUBLIC_KEY'),
    'stripe_secret' => env('STRIPE_PLATFORM_SECRET_KEY'),
    'stripe_webhook_secret' => env('STRIPE_PLATFORM_WEBHOOK_SECRET'),
];
