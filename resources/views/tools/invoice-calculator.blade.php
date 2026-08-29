@extends('layouts.marketing')

@section('title', 'Invoice Calculator | Add Up Line Items, Tax & Discount | Inveqi')
@section('meta_description', 'Free invoice calculator. Add line items, apply tax and discounts, and get an instant subtotal, tax and grand total. Simple and free to use.')
@section('meta_keywords', 'invoice calculator, invoice total calculator, invoice amount calculator, bill calculator, invoice tax calculator, invoice discount calculator')
@section('og_title', 'Invoice Calculator | Inveqi')
@section('og_description', 'Add up invoice line items, tax and discounts with this free online calculator.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Invoice Calculator",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url()->current() }}",
    "description": "Add up invoice line items, apply tax and discounts and get an instant total with this free online calculator."
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
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Invoice calculator</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">Add up line items, apply tax and a discount, and see your subtotal and grand total update instantly.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-4xl px-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">Line items</h2>
                <button id="addLine" class="inline-flex items-center gap-2 rounded-lg gradient-primary px-4 py-2 text-sm font-medium text-white shadow-soft hover:opacity-90">
                    <i class="fas fa-plus text-xs"></i> Add item
                </button>
            </div>

            <div id="items" class="mt-6 space-y-3"></div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Tax rate (%)</label>
                    <input id="taxRate" type="number" value="0" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Discount ($)</label>
                    <input id="discount" type="number" value="0" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="mt-8 rounded-2xl bg-gray-50 p-6">
                <div class="flex justify-between text-sm text-gray-500"><span>Subtotal</span><span id="subtotal">$0.00</span></div>
                <div class="mt-2 flex justify-between text-sm text-gray-500"><span>Tax (<span id="taxLabel">0</span>%)</span><span id="taxAmount">$0.00</span></div>
                <div class="mt-2 flex justify-between text-sm text-gray-500"><span>Discount</span><span id="discountAmount">-$0.00</span></div>
                <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900"><span>Grand total</span><span id="grandTotal">$0.00</span></div>
            </div>

            <p class="mt-6 text-xs text-gray-400">Free to use, no account required. When you are ready, Inveqi turns these numbers into a professional, sendable invoice.</p>
        </div>
    </div>
</section>

<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="text-3xl font-bold tracking-tight">How the invoice calculator works</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-list-ul text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Add your items</h3>
                <p class="mt-2 text-sm text-gray-500">Enter a description, quantity and unit price for each line item.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-percent text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Apply tax & discount</h3>
                <p class="mt-2 text-sm text-gray-500">Set a tax rate and optional discount, then watch totals update live.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-money-bill-wave text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">See your total</h3>
                <p class="mt-2 text-sm text-gray-500">Get an accurate grand total you can put straight onto an invoice.</p>
            </div>
        </div>
        <p class="mt-8 text-sm text-gray-500">Ready to send it? <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">Create the invoice in Inveqi</a> and collect card payments with Stripe.</p>
    </div>
</section>

@include('tools.partials.related', ['related' => $related])

@include('landing.partials.section-cta')
@include('landing.partials.footer')

<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
(function () {
    var itemsEl = document.getElementById('items');
    var addBtn = document.getElementById('addLine');
    var taxRate = document.getElementById('taxRate');
    var discount = document.getElementById('discount');

    function money(n) {
        return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    }

    function addRow(desc, qty, price) {
        var row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 items-end';
        row.innerHTML =
            '<div class="col-span-5"><label class="text-xs font-medium text-gray-500">Description</label>' +
            '<input type="text" value="' + (desc || '') + '" placeholder="Item or service" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none desc"></div>' +
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
        var disc = parseFloat(discount.value) || 0;
        var taxAmt = subtotal * (taxPct / 100);
        var grand = Math.max(0, subtotal + taxAmt - disc);
        document.getElementById('subtotal').textContent = money(subtotal);
        document.getElementById('taxAmount').textContent = money(taxAmt);
        document.getElementById('taxLabel').textContent = String(taxPct);
        document.getElementById('discountAmount').textContent = '-' + money(disc);
        document.getElementById('grandTotal').textContent = money(grand);
    }

    addBtn.addEventListener('click', function () { addRow(); });
    taxRate.addEventListener('input', recalc);
    discount.addEventListener('input', recalc);

    addRow('Consulting services', 1, 250);
    addRow('Software license', 1, 150);
})();
</script>
@endsection
