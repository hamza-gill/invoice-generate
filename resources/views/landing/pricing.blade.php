@extends('layouts.marketing')

@section('title', 'Pricing - ReconX')

@section('content')
{{-- Nav --}}
<header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg gradient-primary text-white shadow-soft">
                <i class="fas fa-bolt text-sm"></i>
            </div>
            <span class="text-lg font-bold tracking-tight">ReconX</span>
        </a>
        <nav class="hidden items-center gap-8 md:flex">
            <a href="{{ route('landing') }}#features" class="text-sm text-gray-500 transition hover:text-gray-900">Features</a>
            <a href="{{ route('landing') }}#advanced" class="text-sm text-gray-500 transition hover:text-gray-900">Invoicing</a>
            <a href="{{ route('landing') }}#dashboard" class="text-sm text-gray-500 transition hover:text-gray-900">Product</a>
            <a href="{{ route('pricing') }}" class="text-sm text-gray-900 font-medium">Pricing</a>
            <a href="{{ route('login') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Login</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('register') }}" class="hidden gradient-primary text-white text-sm font-medium px-4 py-2 rounded-lg shadow-soft hover:opacity-90 md:inline-flex">Get Started</a>
        </div>
    </div>
</header>

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
<footer class="border-t border-gray-200/60 py-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mt-0 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} ReconX. All rights reserved.
        </div>
    </div>
</footer>
@endsection
