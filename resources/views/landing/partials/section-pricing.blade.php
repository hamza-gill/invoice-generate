@php
    $plans = $plans ?? collect();
    $pricingTitle = $pricingTitle ?? 'Pay only for what you need';
@endphp

@if(count($plans))
<section id="pricing" class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Pricing</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $pricingTitle }}</h2>
            <p class="mt-4 text-gray-500">Choose a plan that fits your business. All plans include a 14-day free trial.</p>
        </div>
        <div class="mt-16 grid gap-6 lg:grid-cols-3">
            @foreach($plans as $plan)
            <div class="relative rounded-2xl border bg-white p-8 transition {{ $plan->is_featured ? 'border-blue-500 shadow-glow lg:-translate-y-4' : 'border-gray-200 hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card' }}">
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
                    @foreach($plan->features ?? [] as $f)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fas fa-check mt-0.5 text-blue-500 text-xs"></i>
                        <span>{{ $f }}</span>
                    </li>
                    @endforeach
                    @if($plan->payment_gateway_enabled)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fas fa-check mt-0.5 text-blue-500 text-xs"></i>
                        <span>Stripe payment gateway</span>
                    </li>
                    @endif
                </ul>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-2.5 rounded-lg font-medium transition {{ $plan->is_featured ? 'gradient-primary text-white hover:opacity-95' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">Start Free Trial</a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
