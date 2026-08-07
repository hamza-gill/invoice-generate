@extends('layouts.marketing')

@section('title', 'Pricing - Inveqi')
@section('meta_description', 'Simple, transparent pricing for invoicing software. Free Starter plan plus Professional and Business plans with Stripe payments, recurring billing and more. Start your 14-day free trial.')
@section('meta_keywords', 'invoice software pricing, invoicing plans, free invoice software, subscription billing, invoice management cost, recurring invoice pricing')

@section('content')
{{-- Nav --}}
@include('landing.partials.nav', ['active' => 'pricing'])

<section class="py-24">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Pricing</p>
        <h1 class="mt-3 text-4xl font-bold tracking-tight">Pricing Plans</h1>
        <p class="mt-4 text-gray-500 max-w-xl mx-auto">All plans include a 14-day free trial. No credit card required to start.</p>

        <div class="mt-16 grid md:grid-cols-3 gap-8 text-left">
            @foreach($plans as $plan)
            <div class="p-8 rounded-2xl border relative bg-white transition {{ $plan->is_featured ? 'border-blue-500 shadow-glow lg:-translate-y-4' : 'border-gray-200 hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card' }}">
                @if($plan->is_featured)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full gradient-primary px-4 py-1 text-xs font-semibold text-white shadow-soft">MOST POPULAR</div>
                @endif
                <h3 class="text-xl font-semibold">{{ $plan->name }}</h3>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="text-5xl font-bold tracking-tight">${{ number_format($plan->price, 0) }}</span>
                    <span class="text-sm text-gray-500">/{{ $plan->interval }}</span>
                </div>
                <p class="mt-3 text-sm text-gray-500">{{ $plan->description }}</p>
                <ul class="mt-6 space-y-3">
                    @foreach($plan->features ?? [] as $feature)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fas fa-check mt-0.5 text-blue-500 text-xs"></i>
                        <span>{{ $feature }}</span>
                    </li>
                    @endforeach
                    @if($plan->payment_gateway_enabled)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fas fa-check mt-0.5 text-blue-500 text-xs"></i>
                        <span>Stripe payment gateway</span>
                    </li>
                    @endif
                </ul>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-2.5 rounded-lg font-medium transition {{ $plan->is_featured ? 'gradient-primary text-white hover:opacity-95' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Start Free Trial
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Footer --}}
@include('landing.partials.footer')
@endsection
