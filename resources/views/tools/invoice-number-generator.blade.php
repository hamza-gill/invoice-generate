@extends('layouts.marketing')

@section('title', 'Invoice Number Generator | Create Unique Invoice Numbers | Inveqi')
@section('meta_description', 'Free invoice number generator. Instantly create a unique invoice number with your own prefix, year and counter. Copy it and use it anywhere.')
@section('meta_keywords', 'invoice number generator, invoice number format, generate invoice number, invoice id generator, sequential invoice number, invoice number examples')
@section('og_title', 'Invoice Number Generator | Inveqi')
@section('og_description', 'Generate a unique invoice number with a custom prefix, year and counter in seconds.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Invoice Number Generator",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url()->current() }}",
    "description": "Generate a unique invoice number with a custom prefix, year and counter."
}
</script>
@endpush

@section('content')
@include('landing.partials.nav')

<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-40" style="mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);"></div>
    <div class="mx-auto max-w-4xl px-6 py-20 text-center">
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">{{ $tool['kicker'] }}</p>
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Invoice number generator</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">Create a unique, professional invoice number in seconds. Choose your prefix, include the year and keep a running counter.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-3xl px-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Prefix</label>
                    <input id="prefix" type="text" value="INV" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Year</label>
                    <select id="year" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                        <option value="none">No year</option>
                        <option value="short">Short (25)</option>
                        <option value="full" selected>Full (2026)</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Next number</label>
                    <input id="counter" type="number" value="1" min="0" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3 rounded-2xl border border-blue-500/20 bg-blue-50 p-6">
                <span class="text-sm font-medium text-blue-700">Your number:</span>
                <span id="result" class="text-2xl font-bold tracking-tight text-gray-900">INV-2026-0001</span>
                <button id="gen" class="inline-flex items-center gap-2 rounded-lg gradient-primary px-4 py-2 text-sm font-medium text-white shadow-soft hover:opacity-90">
                    <i class="fas fa-refresh text-xs"></i> Generate next
                </button>
            </div>

            <button id="copy" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-blue-500/30 hover:text-blue-600">
                <i class="fas fa-copy text-xs"></i> Copy number
            </button>
            <p id="copied" class="mt-2 hidden text-sm font-medium text-green-600">Copied to clipboard!</p>

            <p class="mt-6 text-xs text-gray-400">Free to use, no account required. Inveqi numbers your invoices automatically.</p>
        </div>
    </div>
</section>

<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="text-3xl font-bold tracking-tight">Invoice numbering formats</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-hashtag text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Simple sequence</h3>
                <p class="mt-2 text-sm text-gray-500">INV-0001, INV-0002, INV-0003. The easiest format to keep straight.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-calendar text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Year-based</h3>
                <p class="mt-2 text-sm text-gray-500">INV-2026-0001. Restarting the counter each year keeps it readable.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-user-tag text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Client code</h3>
                <p class="mt-2 text-sm text-gray-500">ACME-0001. A short client or project code makes numbers meaningful.</p>
            </div>
        </div>
    </div>
</section>

@include('tools.partials.related', ['related' => $related])

@include('landing.partials.section-cta')
@include('landing.partials.footer')

<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection

<script>
(function () {
    var prefix = document.getElementById('prefix');
    var year = document.getElementById('year');
    var counter = document.getElementById('counter');
    var result = document.getElementById('result');

    function pad(n) {
        return String(n).padStart(4, '0');
    }

    function build() {
        var p = (prefix.value || '').trim().replace(/-+$/, '');
        var y = year.value === 'short' ? String(new Date().getFullYear()).slice(-2)
             : year.value === 'full' ? String(new Date().getFullYear()) : '';
        var c = pad(parseInt(counter.value) || 0);
        var parts = [p];
        if (y) parts.push(y);
        parts.push(c);
        result.textContent = parts.join('-');
    }

    function next() {
        counter.value = (parseInt(counter.value) || 0) + 1;
        build();
    }

    document.getElementById('gen').addEventListener('click', next);
    document.getElementById('copy').addEventListener('click', function () {
        navigator.clipboard.writeText(result.textContent).then(function () {
            var el = document.getElementById('copied');
            el.classList.remove('hidden');
            setTimeout(function () { el.classList.add('hidden'); }, 1500);
        });
    });
    prefix.addEventListener('input', build);
    year.addEventListener('change', build);
    counter.addEventListener('input', build);
    build();
})();
</script>
