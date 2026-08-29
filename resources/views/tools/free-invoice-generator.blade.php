@extends('layouts.marketing')

@section('title', 'Free Invoice Generator | Create an Invoice Online | Inveqi')
@section('meta_description', 'Free invoice generator. Build a professional invoice with your business details, client details and line items, then preview and print it instantly.')
@section('meta_keywords', 'free invoice generator, create invoice online, invoice maker, make an invoice, free invoice builder, invoice creator, free invoice software')
@section('og_title', 'Free Invoice Generator | Inveqi')
@section('og_description', 'Create a professional invoice online in seconds, ready to preview and print.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Free Invoice Generator",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url()->current() }}",
    "description": "Create a professional invoice online with your business details and line items, then preview and print."
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
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Free invoice generator</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">Create a clean, professional invoice in a few minutes. Fill in your details and line items, then preview and print it or save it as a PDF.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold">Invoice form</h2>
                    <button id="next-no" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-blue-500/30 hover:text-blue-600">
                        New number
                    </button>
                </div>

                <div class="mt-6 grid gap-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Invoice number</label>
                            <input id="invNo" type="text" value="INV-2026-0001" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Date</label>
                            <input id="invDate" type="date" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Your business</label>
                        <input id="fromName" type="text" value="Your Business" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <input id="fromDetail" type="text" value="123 Main Street, Springfield" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Bill to (client)</label>
                        <input id="toName" type="text" value="Client Company" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <input id="toDetail" type="text" value="456 Another Ave, Springfield" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Due date</label>
                        <input id="dueDate" type="date" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Line items</h3>
                    <button id="addLine" class="inline-flex items-center gap-1 rounded-lg gradient-primary px-3 py-1.5 text-xs font-medium text-white shadow-soft hover:opacity-90">
                        <i class="fas fa-plus text-xs"></i> Add
                    </button>
                </div>
                <div id="items" class="mt-3 space-y-2"></div>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-500">Tax rate (%)</label>
                        <input id="taxRate" type="number" value="0" min="0" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="lg:sticky lg:top-24 self-start">
                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-soft print-area">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                        <span class="text-xl font-bold tracking-tight">Inveqi</span>
                        <span class="text-sm font-semibold text-blue-600" id="pvNo">INV-2026-0001</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-semibold" id="pvFromName">Your Business</p>
                            <p class="text-gray-500" id="pvFromDetail">123 Main Street, Springfield</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold" id="pvToName">Client Company</p>
                            <p class="text-gray-500" id="pvToDetail">456 Another Ave, Springfield</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-xs text-gray-500">
                        <div>
                            <p>Invoice date: <span id="pvDate">-</span></p>
                            <p>Due date: <span id="pvDue">-</span></p>
                        </div>
                    </div>

                    <table class="mt-5 w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs uppercase tracking-wide text-gray-500">
                                <th class="py-2 pr-2">Description</th>
                                <th class="py-2 pr-2 text-right">Qty</th>
                                <th class="py-2 pr-2 text-right">Price</th>
                                <th class="py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="pvItems"></tbody>
                    </table>

                    <div class="mt-5 space-y-1 border-t border-gray-200 pt-4 text-sm">
                        <div class="flex justify-between text-gray-500"><span>Subtotal</span><span id="pvSubtotal">$0.00</span></div>
                        <div class="flex justify-between text-gray-500"><span>Tax</span><span id="pvTax">$0.00</span></div>
                        <div class="flex justify-between text-lg font-bold text-gray-900"><span>Total</span><span id="pvTotal">$0.00</span></div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button id="printBtn" class="inline-flex items-center gap-2 rounded-lg gradient-primary px-5 py-2.5 text-sm font-medium text-white shadow-soft hover:opacity-90">
                        <i class="fas fa-print text-xs"></i> Print / Save PDF
                    </button>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:border-blue-500/30 hover:text-blue-600">
                        Brand it in Inveqi <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <p class="mt-8 text-xs text-gray-400">Free to use, no account required. For automatic numbering, branded templates, online payments and tracking, sign up for Inveqi.</p>
    </div>
</section>

@include('tools.partials.related', ['related' => $related])

@include('landing.partials.section-cta')
@include('landing.partials.footer')

<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection

<style>
@media print {
    body * { visibility: hidden; }
    .print-area, .print-area * { visibility: visible; }
    .print-area { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
}
</style>

<script>
(function () {
    var itemsEl = document.getElementById('items');
    var pvItems = document.getElementById('pvItems');
    var addBtn = document.getElementById('addLine');
    var taxRate = document.getElementById('taxRate');
    var invNo = document.getElementById('invNo');
    var invDate = document.getElementById('invDate');
    var dueDate = document.getElementById('dueDate');
    var today = new Date().toISOString().slice(0, 10);
    var due = new Date(Date.now() + 14 * 86400000).toISOString().slice(0, 10);
    invDate.value = today;
    dueDate.value = due;

    function money(n) {
        return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    }

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function addRow(desc, qty, price) {
        var none = document.getElementById('emptyItems');
        if (none) none.remove();
        var row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-1 items-end';
        row.innerHTML =
            '<div class="col-span-6"><input type="text" value="' + esc(desc || '') + '" placeholder="Item" class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none desc"></div>' +
            '<div class="col-span-2"><input type="number" value="' + (qty || 1) + '" min="0" class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm qty focus:border-blue-500 focus:outline-none"></div>' +
            '<div class="col-span-3"><input type="number" value="' + (price || 0) + '" min="0" step="0.01" class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm price focus:border-blue-500 focus:outline-none"></div>' +
            '<div class="col-span-1 text-right"><button type="button" class="text-red-400 hover:text-red-600 remove"><i class="fas fa-trash text-xs"></i></button></div>';
        itemsEl.appendChild(row);
        row.querySelector('.remove').addEventListener('click', function () { row.remove(); preview(); });
        ['desc','qty','price'].forEach(function (k) { row.querySelector('.' + k).addEventListener('input', preview); });
        preview();
    }

    function preview() {
        var rows = itemsEl.querySelectorAll('div.grid');
        var subtotal = 0;
        var pvRows = '';
        if (!rows.length) {
            pvRows = '<tr><td colspan="4" class="py-3 text-center text-gray-400" id="emptyItems">No items yet</td></tr>';
        }
        rows.forEach(function (row) {
            var d = row.querySelector('.desc').value || 'Item';
            var q = parseFloat(row.querySelector('.qty').value) || 0;
            var p = parseFloat(row.querySelector('.price').value) || 0;
            var t = q * p;
            subtotal += t;
            pvRows += '<tr class="border-b border-gray-100"><td class="py-2 pr-2">' + esc(d) + '</td>' +
                '<td class="py-2 pr-2 text-right">' + q + '</td>' +
                '<td class="py-2 pr-2 text-right">' + money(p) + '</td>' +
                '<td class="py-2 text-right font-medium">' + money(t) + '</td></tr>';
        });
        pvItems.innerHTML = pvRows;

        var taxPct = parseFloat(taxRate.value) || 0;
        var taxAmt = subtotal * (taxPct / 100);
        var total = subtotal + taxAmt;
        document.getElementById('pvSubtotal').textContent = money(subtotal);
        document.getElementById('pvTax').textContent = money(taxAmt);
        document.getElementById('pvTotal').textContent = money(total);
    }

    function refreshMeta() {
        document.getElementById('pvNo').textContent = invNo.value || 'INV-2026-0001';
        document.getElementById('pvFromName').textContent = document.getElementById('fromName').value || 'Your Business';
        document.getElementById('pvFromDetail').textContent = document.getElementById('fromDetail').value || '';
        document.getElementById('pvToName').textContent = document.getElementById('toName').value || 'Client';
        document.getElementById('pvToDetail').textContent = document.getElementById('toDetail').value || '';
        document.getElementById('pvDate').textContent = invDate.value ? new Date(invDate.value + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '-';
        document.getElementById('pvDue').textContent = dueDate.value ? new Date(dueDate.value + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '-';
    }

    ['invNo','fromName','fromDetail','toName','toDetail','invDate','dueDate'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', refreshMeta);
    });

    document.getElementById('next-no').addEventListener('click', function () {
        var c = parseInt(invNo.value.replace(/\D/g, '')) || 0;
        invNo.value = 'INV-' + new Date().getFullYear() + '-' + String(c + 1).padStart(4, '0');
        refreshMeta();
    });

    document.getElementById('printBtn').addEventListener('click', function () {
        window.print();
    });

    addBtn.addEventListener('click', function () { addRow(); });
    taxRate.addEventListener('input', preview);

    refreshMeta();
    addRow('Consulting services', 1, 550);
})();
</script>
