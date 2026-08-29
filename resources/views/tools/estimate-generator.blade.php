@extends('layouts.marketing')

@section('title', 'Estimate Generator | Create a Professional Quote | Inveqi')
@section('meta_description', 'Free estimate generator. Build a professional estimate with line items, quantities and prices, then see the total and share it with your client.')
@section('meta_keywords', 'estimate generator, quote generator, estimate maker, free estimate template, business quotation, estimate builder, quote estimator')
@section('og_title', 'Estimate Generator | Inveqi')
@section('og_description', 'Create a professional estimate or quote with line items and an instant total.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Estimate Generator",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url()->current() }}",
    "description": "Create a professional estimate or quote with line items and an instant total."
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
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Estimate generator</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">Put together a clear, professional estimate your clients can understand at a glance. Add items, set quantities and prices, and get an instant total.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-4xl px-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold">Estimate details</h2>
                    <p class="mt-1 text-sm text-gray-500">Estimate #: <span id="estNo" class="font-semibold text-gray-900">EST-2026-0001</span></p>
                </div>
                <button id="addLine" class="inline-flex items-center gap-2 rounded-lg gradient-primary px-4 py-2 text-sm font-medium text-white shadow-soft hover:opacity-90">
                    <i class="fas fa-plus text-xs"></i> Add item
                </button>
            </div>

            <div class="mt-6 space-y-3" id="items"></div>

            <div class="mt-8 rounded-2xl bg-gray-50 p-6">
                <div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span id="subtotal">$0.00</span></div>
                <div class="mt-2 flex justify-between text-sm text-gray-500"><span>Tax (<span id="taxLabel">0</span>%)</span><span id="taxAmount">$0.00</span></div>
                <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900"><span>Estimated total</span><span id="grandTotal">$0.00</span></div>
            </div>

            <div class="mt-6">
                <label class="text-sm font-medium text-gray-700">Tax rate (%)</label>
                <input id="taxRate" type="number" value="0" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none sm:w-40">
            </div>

            <p class="mt-6 text-xs text-gray-400">Free to use, no account required. In Inveqi you can send this estimate as a branded PDF and convert it to an invoice when the client approves.</p>
        </div>
    </div>
</section>

<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="text-3xl font-bold tracking-tight">Why estimates matter</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-check-circle text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Set clear expectations</h3>
                <p class="mt-2 text-sm text-gray-500">A detailed estimate shows exactly what you will deliver and what it will cost.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-file-contract text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Get approval in writing</h3>
                <p class="mt-2 text-sm text-gray-500">A signed or accepted estimate protects both you and your client.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-arrow-right-from-bracket text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Turn it into an invoice</h3>
                <p class="mt-2 text-sm text-gray-500">When work is done, convert the estimate to a final invoice in seconds.</p>
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
    var itemsEl = document.getElementById('items');
    var addBtn = document.getElementById('addLine');
    var taxRate = document.getElementById('taxRate');
    var estNo = document.getElementById('estNo');

    function money(n) {
        return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    }

    function addRow(desc, qty, price) {
        var row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-end';
        row.innerHTML =
            '<div class="col-span-5"><label class="text-xs font-medium text-gray-500">Description</label>' +
            '<input type="text" value="' + (desc || '') + '" placeholder="Work or product" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none desc"></div>' +
            '<div class="col-span-2"><label class="text-xs font-medium text-gray-500">Qty</label>' +
            '<input type="number" value="' + (qty || 1) + '" min="0" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm qty focus:border-blue-500 focus:outline-none"></div>' +
            '<div class="col-span-3"><label class="text-xs font-medium text-gray-500">Unit price</label>' +
            '<input type="number" value="' + (price || 0) + '" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm price focus:border-blue-500 focus:outline-none"></div>' +
            '<div class="col-span-2 text-right pb-1"><span class="text-sm font-semibold text-gray-900 line-total">$0.00</span>' +
            '<button type="button" class="ml-1 text-red-400 hover:text-red-600 remove"><i class="fas fa-trash text-sm"></i></button></div>';
        itemsEl.appendChild(row);
        row.querySelector('.remove').addEventListener('click', function () {
            row.remove();
            recalc();
        });
        ['desc','qty','price'].forEach(function (k) {
            row.querySelector('.' + k).addEventListener('input', recalc);
        });
        recalc();
    }

    function recalc() {
        var subtotal = 0;
        itemsEl.querySelectorAll('div.grid').forEach(function (row) {
            var q = parseFloat(row.querySelector('.qty')?.value) || 0;
            var p = parseFloat(row.querySelector('.price')?.value) || 0;
            var t = q * p;
            subtotal += t;
            row.querySelector('.line-total').textContent = money(t);
        });
        var taxPct = parseFloat(taxRate.value) || 0;
        var taxAmt = subtotal * (taxPct / 100);
        var grand = subtotal + taxAmt;
        document.getElementById('subtotal').textContent = money(subtotal);
        document.getElementById('taxAmount').textContent = money(taxAmt);
        document.getElementById('taxLabel').textContent = String(taxPct);
        document.getElementById('grandTotal').textContent = money(grand);
    }

    addBtn.addEventListener('click', function () { addRow(); });
    taxRate.addEventListener('input', recalc);

    addRow('Web design project', 1, 1200);
    addRow('Monthly maintenance', 1, 150);
})();
</script>
