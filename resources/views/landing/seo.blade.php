@extends('layouts.marketing')

@section('title', $page['title'])
@section('meta_description', $page['meta_description'])
@section('meta_keywords', $page['meta_keywords'])
@section('og_title', $page['og_title'] ?? $page['title'])
@section('og_description', $page['og_description'] ?? $page['meta_description'])
@section('og_type', $page['og_type'] ?? 'website')

@push('jsonld')
@if(!empty($page['schema']))
<script type="application/ld+json">
{!! $page['schema'] !!}
</script>
@endif

{{-- FAQPage schema --}}
@if(!empty($page['faqs']))
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
    @foreach($page['faqs'] as $i => $faq)
        {
            "@@type": "Question",
            "name": {{ json_encode($faq['q']) }},
            "acceptedAnswer": { "@@type": "Answer", "text": {{ json_encode($faq['a']) }} }
        }{{ !$loop->last ? ',' : '' }}
    @endforeach
    ]
}
</script>
@endif

{{-- Breadcrumb schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "{{ $page['h1'] }}", "item": "{{ url($page['slug']) }}" }
    ]
}
</script>
@endpush

@section('content')
{{-- Nav --}}
@include('landing.partials.nav', ['active' => $page['slug']])

{{-- Hero: H1 targets primary keyword --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-40" style="mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);"></div>
    <div class="absolute -top-40 left-1/2 -z-10 h-[500px] w-[800px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-3xl"></div>
    <div class="mx-auto max-w-7xl px-6 pb-16 pt-16">
        <nav aria-label="Breadcrumb" class="mb-8">
            <ol class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <li><a href="{{ route('landing') }}" class="hover:text-gray-900">Home</a></li>
                <li><i class="fas fa-chevron-right text-[8px] text-gray-400"></i></li>
                <li class="font-medium text-gray-900">{{ $page['h1'] }}</li>
            </ol>
        </nav>
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">{{ $page['kicker'] }}</p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                {{ $page['h1'] }}@if(!empty($page['h1_highlight'])) <span class="block text-gradient">{{ $page['h1_highlight'] }}</span>@endif
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-500">{{ $page['subtitle'] }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="group inline-flex items-center gradient-primary text-white px-6 py-3 rounded-lg font-medium shadow-glow transition hover:opacity-95">
                    {{ $page['cta_text'] ?? 'Start Free Trial' }} <i class="fas fa-arrow-right ml-2 text-sm transition group-hover:translate-x-1"></i>
                </a>
                <a href="#pricing" class="inline-flex items-center border border-blue-500/20 px-6 py-3 rounded-lg font-medium hover:bg-blue-500/5 transition">
                    View Pricing
                </a>
            </div>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500">
                <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> No credit card</div>
                <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> 14-day trial</div>
                <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> Cancel anytime</div>
            </div>
        </div>
    </div>
</section>

{{-- Product screenshots --}}
@if(!empty($page['screenshots']))
<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Product tour</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">See {{ $page['h1'] }} in action</h2>
            <p class="mt-4 text-gray-500">{{ $page['screenshots_intro'] ?? 'A tour of the invoice workspace, templates, and payment flow.' }}</p>
        </div>
        <div class="mt-14 space-y-16">
            @foreach($page['screenshots'] as $shot)
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div class="{{ $loop->even ? 'lg:order-2' : '' }} relative">
                    <div class="absolute -inset-2 rounded-3xl gradient-primary opacity-20 blur-2xl"></div>
                    <img src="{{ asset('images/' . $shot['image']) }}" alt="{{ $shot['alt'] }}" loading="lazy" class="relative w-full rounded-2xl border border-gray-200 shadow-glow">
                </div>
                <div>
                    <h3 class="text-2xl font-bold tracking-tight">{{ $shot['title'] }}</h3>
                    <p class="mt-3 text-gray-500 leading-relaxed">{{ $shot['desc'] }}</p>
                    <ul class="mt-5 space-y-2 text-sm text-gray-600">
                        @foreach($shot['points'] ?? [] as $pt)
                        <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i>{{ $pt }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Benefits --}}
@if(!empty($page['benefits']))
<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Why it matters</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $page['benefits_title'] ?? 'Real benefits for your business' }}</h2>
            <p class="mt-4 text-gray-500">{{ $page['benefits_intro'] ?? '' }}</p>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach($page['benefits'] as $b)
            <div class="feature-card group relative rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                    <i class="fas {{ $b['icon'] }}"></i>
                </div>
                <h3 class="mt-5 text-lg font-semibold">{{ $b['title'] }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ $b['desc'] }}</p>
                <div class="bottom-glow absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-blue-500/40 to-transparent opacity-0 transition"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Features --}}
@if(!empty($page['features']))
<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Features</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $page['features_title'] ?? 'Everything you need to get paid' }}</h2>
            <p class="mt-4 text-gray-500">{{ $page['features_intro'] ?? '' }}</p>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($page['features'] as $f)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-glow">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
                    <i class="fas {{ $f['icon'] }}"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $f['title'] }}</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Use cases --}}
@if(!empty($page['use_cases']))
<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Use cases</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $page['use_cases_title'] ?? 'Who it is built for' }}</h2>
            <p class="mt-4 text-gray-500">{{ $page['use_cases_intro'] ?? '' }}</p>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-2">
            @foreach($page['use_cases'] as $uc)
            <div class="flex gap-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600">
                    <i class="fas {{ $uc['icon'] }} text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">{{ $uc['title'] }}</h3>
                    <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $uc['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Internal links --}}
@if(!empty($page['internal_links']))
<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Explore more</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Related {{ $page['h1'] }} resources</h2>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($page['internal_links'] as $link)
            <a href="{{ url($link['url']) }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-glow">
                <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600">{{ $link['title'] }}</div>
                @if(!empty($link['desc']))<p class="mt-2 text-sm text-gray-500">{{ $link['desc'] }}</p>@endif
                <div class="mt-4 flex items-center gap-1 text-sm font-medium text-blue-600">
                    Read more <i class="fas fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Testimonials --}}
@include('landing.partials.section-testimonials', ['testimonials' => $testimonials ?? null, 'testimonialsTitle' => $page['testimonials_title'] ?? null])

{{-- Pricing --}}
@include('landing.partials.section-pricing', ['plans' => $plans ?? null, 'pricingTitle' => $page['pricing_title'] ?? null])

{{-- FAQ --}}
@if(!empty($page['faqs']))
<section id="faq" class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-3xl px-6">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">FAQ</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">{{ $page['faqs_title'] ?? 'Frequently asked questions' }}</h2>
            <p class="mt-4 text-gray-500">{{ $page['faqs_intro'] ?? 'Answers to the questions we hear most about ' . $page['h1'] . '.' }}</p>
        </div>
        <div class="mt-10 space-y-2">
            @foreach($page['faqs'] as $faq)
            <div class="rounded-xl border border-gray-200">
                <button class="faq-toggle flex w-full items-center justify-between px-5 py-4 text-left text-sm font-medium hover:bg-gray-50 rounded-xl transition">
                    {{ $faq['q'] }}
                    <i class="fas fa-chevron-down faq-arrow text-xs text-gray-400 transition-transform duration-200"></i>
                </button>
                <div class="hidden px-5 pb-4 text-sm text-gray-500">{{ $faq['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
@include('landing.partials.section-cta', ['ctaTitle' => $page['cta_title'] ?? null, 'ctaSubtitle' => $page['cta_subtitle'] ?? ('Join ' . number_format($businessesCount) . ' businesses already streamlining their invoicing with Inveqi.'), 'ctaButton' => $page['cta_text'] ?? null])

{{-- Footer --}}
@include('landing.partials.footer')

{{-- Scroll to top --}}
<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection
