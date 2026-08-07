@extends('layouts.auth.app')

@section('title', 'Checkout - ' . $plan->name)

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-8">
        <a href="{{ route('subscription.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left"></i> Back to Subscription
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="bg-orange-500 px-8 py-6 text-white">
            <h1 class="text-2xl font-bold">Complete Your Subscription</h1>
            <p class="text-orange-100 text-sm mt-1">You're upgrading {{ $organization?->name }} to the {{ $plan->name }} plan.</p>
        </div>

        <div class="p-8">
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid md:grid-cols-5 gap-8">
                <div class="md:col-span-3 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800">Order Summary</h2>

                    <div class="border border-gray-200 rounded-xl p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                                <p class="text-gray-500 text-sm mt-1">{{ $plan->description }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">${{ number_format($plan->price, 2) }}</p>
                                <p class="text-sm text-gray-500">/ {{ $plan->interval }}</p>
                            </div>
                        </div>

                        <ul class="text-sm text-gray-600 space-y-2 mt-5">
                            @foreach($plan->features ?? [] as $feature)
                                <li><i class="fas fa-check text-green-500 mr-1"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between">
                        <span class="text-gray-700 font-medium">Due today</span>
                        <span class="text-xl font-bold text-gray-800">${{ number_format($plan->price, 2) }}</span>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment</h2>
                        <p class="text-sm text-gray-500 mb-4">
                            You'll be redirected to <strong>Stripe</strong> to securely complete your payment.
                            Your subscription activates automatically once the payment succeeds.
                        </p>

                        @if($currentSubscription?->subscription_plan_id === $plan->id)
                            <button disabled class="w-full py-3 bg-gray-200 text-gray-500 rounded-lg font-semibold">Current Plan</button>
                        @else
                            <form action="{{ route('subscription.checkout.process', $plan) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold shadow transition">
                                    <i class="fas fa-lock mr-2"></i> Pay ${{ number_format($plan->price, 2) }}
                                </button>
                            </form>
                        @endif

                        <p class="text-xs text-gray-400 mt-4">
                            <i class="fas fa-shield-alt mr-1"></i> Secured by Stripe. Your card details never touch our servers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
