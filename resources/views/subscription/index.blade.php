@extends('layouts.auth.app')

@section('title', 'Subscription')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Subscription</h1>
    <p class="text-gray-500 mb-8">Manage your plan for <strong>{{ $organization?->name }}</strong></p>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    @if($currentSubscription)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
        <h2 class="font-semibold text-blue-900">Current Plan: {{ $currentSubscription->plan->name }}</h2>
        <p class="text-blue-700 text-sm mt-1">
            Status: <span class="capitalize">{{ $currentSubscription->status }}</span>
            @if($currentSubscription->onTrial())
                — Trial ends {{ $currentSubscription->trial_ends_at->format('M d, Y') }}
            @endif
        </p>
    </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white border rounded-2xl p-6 shadow-sm {{ $plan->is_featured ? 'ring-2 ring-orange-500' : '' }}">
            <h3 class="text-xl font-bold">{{ $plan->name }}</h3>
            <p class="text-3xl font-bold my-3">${{ number_format($plan->price, 2) }}<span class="text-sm text-gray-500">/{{ $plan->interval }}</span></p>
            <p class="text-gray-500 text-sm mb-4">{{ $plan->description }}</p>
            <ul class="text-sm text-gray-600 space-y-2 mb-6">
                @foreach($plan->features ?? [] as $feature)
                <li><i class="fas fa-check text-green-500 mr-1"></i> {{ $feature }}</li>
                @endforeach
            </ul>
            @if($currentSubscription?->subscription_plan_id === $plan->id)
                <button disabled class="w-full py-2 bg-gray-100 text-gray-500 rounded-lg font-medium">Current Plan</button>
            @elseif($plan->price > 0)
                <form action="{{ route('subscription.checkout', $plan) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium">Upgrade</button>
                </form>
            @else
                <span class="block text-center py-2 text-gray-500 text-sm">Free tier active</span>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
