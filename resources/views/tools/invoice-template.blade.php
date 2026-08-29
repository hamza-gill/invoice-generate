@extends('layouts.marketing')

@section('title', 'Invoice Template | Professional Invoice Layouts | Inveqi')
@section('meta_description', 'Browse professional invoice templates you can use or customize. See a live preview of different layouts and learn what a good invoice template includes.')
@section('meta_keywords', 'invoice template, invoice layout, invoice design, free invoice template, professional invoice template, invoicing template, bill template')
@section('og_title', 'Invoice Template | Inveqi')
@section('og_description', 'Explore professional invoice template layouts and see a live preview.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Invoice Template",
    "url": "{{ url()->current() }}",
    "description": "Browse professional invoice template layouts and see a live preview of what a good invoice includes.",
    "isPartOf": {
        "@@type": "WebSite",
        "@@id": "{{ url('/') }}#website"
    }
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
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Invoice template</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">A good invoice is clear, professional and easy to read. Explore the layouts below and see what every template should include.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="space-y-3">
                <h2 class="text-lg font-bold">Choose a style</h2>
                <button data-style="classic" class="style-btn w-full rounded-2xl border-2 border-blue-600 bg-white p-4 text-left shadow-card">
                    <div class="flex items-center gap-2"><i class="fas fa-file-lines text-blue-600"></i><span class="font-medium">Classic</span></div>
                    <p class="mt-1 text-xs text-gray-500">Clean left-aligned rows, minimal color.</p>
                </button>
                <button data-style="modern" class="style-btn w-full rounded-2xl border-2 border-gray-200 bg-white p-4 text-left shadow-card">
                    <div class="flex items-center gap-2"><i class="fas fa-layer-group text-blue-600"></i><span class="font-medium">Modern</span></div>
                    <p class="mt-1 text-xs text-gray-500">Accent column and bold totals.</p>
                </button>
                <button data-style="minimal" class="style-btn w-full rounded-2xl border-2 border-gray-200 bg-white p-4 text-left shadow-card">
                    <div class="flex items-center gap-2"><i class="fas fa-minus text-blue-600"></i><span class="font-medium">Minimal</span></div>
                    <p class="mt-1 text-xs text-gray-500">Simple, borderless and airy.</p>
                </button>

                <label class="block pt-3">
                    <span class="text-sm font-medium text-gray-700">Accent color</span>
                    <span id="accents" class="mt-2 flex gap-2">
                        <button type="button" data-color="#2563eb" class="accent-dot flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-blue-600" style="background:#2563eb"><i class="fas fa-check text-xs text-white"></i></button>
                        <button type="button" data-color="#059669" class="accent-dot flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-transparent" style="background:#059669"></button>
                        <button type="button" data-color="#9333ea" class="accent-dot flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-transparent" style="background:#9333ea"></button>
                        <button type="button" data-color="#dc2626" class="accent-dot flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-transparent" style="background:#dc2626"></button>
                        <button type="button" data-color="#1f2937" class="accent-dot flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-transparent" style="background:#1f2937"></button>
                    </span>
                </label>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-soft" id="preview">
                    <div class="flex items-start justify-between pb-4">
                        <div>
                            <div class="flex items-center gap-2 text-xl font-bold tracking-tight">
                                <i class="fas fa-bolt accent-sym"></i>
                                <span>Acme Studio</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">123 Main Street Â· Springfield</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold accent-text">INVOICE</div>
                            <div class="text-xs text-gray-500">INV-2026-0042</div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-semibold">Bill to</p>
                            <p class="text-gray-500">Northwind Traders</p>
                            <p class="text-xs text-gray-400">Attention: Purchasing</p>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            <p>Issue date: Aug 12, 2026</p>
                            <p>Due date: Aug 26, 2026</p>
                        </div>
                    </div>
                    <table class="mt-5 w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide" style="color:#2563eb">
                                <th class="py-2 pr-2">Description</th>
                                <th class="py-2 pr-2 text-right">Qty</th>
                                <th class="py-2 pr-2 text-right">Rate</th>
                                <th class="py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-2">Brand identity design</td>
                                <td class="py-2 pr-2 text-right">1</td>
                                <td class="py-2 pr-2 text-right">$1,800.00</td>
                                <td class="py-2 text-right font-medium">$1,800.00</td>
                            </tr>
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pr-2">Print collateral</td>
                                <td class="py-2 pr-2 text-right">3</td>
                                <td class="py-2 pr-2 text-right">$125.00</td>
                                <td class="py-2 text-right font-medium">$375.00</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-5 ml-auto max-w-xs space-y-1 border-t pt-4 text-sm">
                        <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>$2,175.00</span></div>
                        <div class="flex justify-between text-gray-500"><span>Tax (8%)</span><span>$174.00</span></div>
                        <div class="flex justify-between border-t border-gray-100 pt-2 text-lg font-bold text-gray-900"><span>Total</span><span>$2,349.00</span></div>
                    </div>
                    <div class="mt-5 flex items-start gap-2 rounded-xl bg-gray-50 p-4 text-xs text-gray-500">
                        <i class="fas fa-circle-info mt-0.5"></i>
                        <p>Thank you for your business. Please remit payment by the due date. For questions about this invoice, contact billing@acmestudio.example.</p>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-400">This is an interactive preview of template layouts. Inveqi applies your logo, colors and fields automatically.</p>
            </div>
        </div>
    </div>
</section>

<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="text-3xl font-bold tracking-tight">What a good invoice template includes</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-building text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Your branding</h3>
                <p class="mt-2 text-sm text-gray-500">Your logo and contact details at the top make the invoice look official and professional.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-address-card text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Client details</h3>
                <p class="mt-2 text-sm text-gray-500">A clear "bill to" block with the client name and address avoids payment delays and confusion.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-table-list text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Line items & totals</h3>
                <p class="mt-2 text-sm text-gray-500">Itemize what you delivered, then show subtotal, tax and total clearly in one place.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-money-check text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Payment terms</h3>
                <p class="mt-2 text-sm text-gray-500">State the due date and payment methods so clients know exactly how and when to pay.</p>
            </div>
        </div>
        <p class="mt-8 text-sm text-gray-500">Want your logo and colors applied automatically? <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Start a free trial in Inveqi</a>.</p>
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
    var preview = document.getElementById('preview');
    var accent = '#2563eb';
    var style = 'classic';

    function apply() {
        preview.querySelectorAll('.accent-sym, .accent-text, th').forEach(function (el) { el.style.color = accent; });

        if (style === 'modern') {
            preview.style.borderLeft = '6px solid ' + accent;
        } else {
            preview.style.borderLeft = '';
        }
        if (style === 'minimal') {
            preview.style.boxShadow = 'none';
            preview.style.border = '1px solid #e5e7eb';
        } else {
            preview.style.boxShadow = '';
            preview.style.border = '1px solid #e5e7eb';
        }
    }

    document.querySelectorAll('.style-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.style-btn').forEach(function (b) {
                b.classList.remove('border-blue-600');
                b.classList.add('border-gray-200');
            });
            btn.classList.add('border-blue-600');
            btn.classList.remove('border-gray-200');
            style = btn.getAttribute('data-style');
            apply();
        });
    });

    document.querySelectorAll('.accent-dot').forEach(function (dot) {
        dot.addEventListener('click', function () {
            accent = dot.getAttribute('data-color');
            document.querySelectorAll('.accent-dot').forEach(function (d) {
                d.classList.remove('ring-blue-600');
                d.style.display = 'flex';
                d.innerHTML = '';
            });
            dot.classList.add('ring-blue-600');
            dot.innerHTML = '<i class="fas fa-check text-xs text-white"></i>';
            apply();
        });
    });

    apply();
})();
</script>
