<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $organization = $user->organization;
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $currentSubscription = $organization?->activeSubscription;

        return view('subscription.index', compact('plans', 'organization', 'currentSubscription'));
    }

    public function checkoutPage(SubscriptionPlan $plan)
    {
        $organization = Auth::user()->organization;
        $currentSubscription = $organization?->activeSubscription;
        $stripeConfigured = (bool) PlatformSetting::getStripeSecretKey();

        return view('subscription.checkout', compact('plan', 'organization', 'currentSubscription', 'stripeConfigured'));
    }

    public function checkout(Request $request, SubscriptionPlan $plan)
    {
        $organization = Auth::user()->organization;

        if (! $plan->stripe_price_id) {
            return back()->with('error', 'This plan is not yet configured for billing. Please contact support.');
        }

        if (! PlatformSetting::getStripeSecretKey()) {
            return back()->with('error', 'The payment gateway is not configured yet. Please try again later.');
        }

        $session = $this->subscriptionService->createCheckoutSession(
            $organization,
            $plan,
            route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            route('subscription.index')
        );

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        return redirect()->route('dashboard')
            ->with('success', 'Subscription activated successfully! Welcome aboard.');
    }
}
