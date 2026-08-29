@php
    $ctaTitle = $ctaTitle ?? 'Ready to get paid faster?';
    $ctaSubtitle = $ctaSubtitle ?? 'Create, send and track professional invoices with recurring billing and Stripe payments — all in one secure workspace.';
    $ctaButton = $ctaButton ?? 'Start your free trial';
@endphp

<section class="px-6 pb-20 pt-8">
    <div class="relative mx-auto max-w-5xl overflow-hidden rounded-3xl gradient-primary p-12 text-center text-white shadow-glow md:p-16">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <h2 class="relative text-4xl font-bold tracking-tight md:text-5xl">{{ $ctaTitle }}</h2>
        <p class="relative mx-auto mt-4 max-w-xl opacity-90">{{ $ctaSubtitle }}</p>
        <div class="relative mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                {{ $ctaButton }} <i class="fas fa-arrow-right ml-2 text-sm"></i>
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center border border-white/30 bg-transparent text-white px-6 py-3 rounded-lg font-medium hover:bg-white/10 transition">
                Talk to sales
            </a>
        </div>
    </div>
</section>
