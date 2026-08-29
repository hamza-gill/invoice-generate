@php
    $testimonials = $testimonials ?? [];
    $testimonialsTitle = $testimonialsTitle ?? 'Businesses that invoice with Inveqi';
    $quotes = [
        'We use Inveqi to send professional invoices, automate recurring billing and collect payments with Stripe.',
        'Inveqi handles our estimates, recurring invoicing and online payments all in one place.',
        'We manage customers, products and invoices on Inveqi and collect payments online with Stripe.',
    ];
@endphp

@if(count($testimonials))
<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Our customers</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $testimonialsTitle }}</h2>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach($testimonials as $i => $t)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
                <div class="flex gap-0.5 text-blue-500">
                    @for($j = 0; $j < 5; $j++)
                    <i class="fas fa-star text-sm"></i>
                    @endfor
                </div>
                <p class="mt-4 text-sm leading-relaxed text-gray-900">"{{ $quotes[$i % count($quotes)] }}"</p>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full gradient-primary text-sm font-semibold text-white">
                        {{ $t['initials'] ?? 'IN' }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ $t['name'] }}</div>
                        <div class="text-xs text-gray-500">{{ $t['company'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
