<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionService
{
    public function startTrial(Organization $organization, ?SubscriptionPlan $plan = null): Subscription
    {
        $plan ??= SubscriptionPlan::where('slug', 'starter')->first()
            ?? SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->first();

        return Subscription::create([
            'organization_id' => $organization->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays(config('subscription.trial_days', 14)),
            'current_period_start' => now(),
            'current_period_end' => now()->addDays(config('subscription.trial_days', 14)),
        ]);
    }

    public function createCheckoutSession(Organization $organization, SubscriptionPlan $plan, string $successUrl, string $cancelUrl): Session
    {
        Stripe::setApiKey(config('subscription.stripe_secret'));

        $sessionData = [
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'metadata' => [
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'organization_id' => $organization->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ];

        if ($organization->stripe_customer_id) {
            $sessionData['customer'] = $organization->stripe_customer_id;
        } else {
            $sessionData['customer_email'] = $organization->email ?? $organization->owner?->email;
        }

        return Session::create($sessionData);
    }

    public function activateFromStripe(array $metadata, string $stripeSubscriptionId, ?string $customerId = null): void
    {
        $organization = Organization::find($metadata['organization_id'] ?? null);
        $plan = SubscriptionPlan::find($metadata['plan_id'] ?? null);

        if (! $organization || ! $plan) {
            return;
        }

        if ($customerId) {
            $organization->update(['stripe_customer_id' => $customerId, 'status' => 'active']);
        }

        Subscription::where('organization_id', $organization->id)
            ->whereIn('status', ['trialing', 'active'])
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        Subscription::create([
            'organization_id' => $organization->id,
            'subscription_plan_id' => $plan->id,
            'stripe_subscription_id' => $stripeSubscriptionId,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        Setting::withoutGlobalScopes()
            ->where('organization_id', $organization->id)
            ->update(['payment_gateway_enabled' => $plan->payment_gateway_enabled]);
    }
}
