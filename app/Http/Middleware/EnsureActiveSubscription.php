<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            return $next($request);
        }

        $organization = Organization::find($user->organization_id);

        if (! $organization) {
            return redirect()->route('subscription.index')
                ->with('error', 'Organization not found. Please contact support.');
        }

        if (! $organization->isActive()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Your organization account is suspended. Please renew your subscription.');
        }

        $subscription = $organization->activeSubscription;

        if (! $subscription && ! in_array($request->route()?->getName(), ['subscription.index', 'subscription.checkout', 'subscription.success'], true)) {
            return redirect()->route('subscription.index')
                ->with('error', 'Please choose a subscription plan to continue.');
        }

        return $next($request);
    }
}
