<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class PlatformStripeWebhookController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    public function handle(Request $request)
    {
        Stripe::setApiKey(PlatformSetting::getStripeSecretKey());
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = PlatformSetting::getStripeWebhookSecret();

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error('Platform Stripe webhook error: ' . $e->getMessage());

            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                if ($session->mode === 'subscription') {
                    $this->subscriptionService->activateFromStripe(
                        (array) $session->metadata,
                        $session->subscription,
                        $session->customer
                    );
                }
                break;

            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                \App\Models\Subscription::where('stripe_subscription_id', $subscription->id)
                    ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                break;

            case 'customer.subscription.updated':
            case 'customer.subscription.created':
                $subscription = $event->data->object;
                \App\Models\Subscription::where('stripe_subscription_id', $subscription->id)
                    ->update([
                        'status' => $subscription->status === 'canceled' ? 'cancelled' : ($subscription->status ?: 'active'),
                        'current_period_start' => isset($subscription->current_period_start) ? \Illuminate\Support\Carbon::createFromTimestamp($subscription->current_period_start) : null,
                        'current_period_end' => isset($subscription->current_period_end) ? \Illuminate\Support\Carbon::createFromTimestamp($subscription->current_period_end) : null,
                    ]);
                break;
        }

        return response('OK', 200);
    }
}
